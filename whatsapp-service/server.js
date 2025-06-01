const { default: makeWASocket, DisconnectReason, useMultiFileAuthState, fetchLatestBaileysVersion } = require('@whiskeysockets/baileys');
const express = require('express');
const cors = require('cors');
const fs = require('fs-extra');
const path = require('path');
const QRCode = require('qrcode');
const winston = require('winston');
const { Server } = require('socket.io');
const http = require('http');
require('dotenv').config();

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: process.env.LARAVEL_APP_URL || "http://localhost:8000",
        methods: ["GET", "POST"]
    }
});

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Logger setup
const logger = winston.createLogger({
    level: process.env.LOG_LEVEL || 'info',
    format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.json()
    ),
    transports: [
        new winston.transports.File({ filename: 'logs/error.log', level: 'error' }),
        new winston.transports.File({ filename: 'logs/combined.log' }),
        new winston.transports.Console({
            format: winston.format.simple()
        })
    ]
});

// Ensure directories exist
fs.ensureDirSync('sessions');
fs.ensureDirSync('logs');

// Global variables
let sock;
let qrCode = null;
let isConnected = false;
let connectionState = 'disconnected';

// Initialize WhatsApp connection
async function connectToWhatsApp() {
    try {
        const { state, saveCreds } = await useMultiFileAuthState('sessions');
        const { version, isLatest } = await fetchLatestBaileysVersion();
        
        logger.info(`Using WA v${version.join('.')}, isLatest: ${isLatest}`);

        sock = makeWASocket({
            version,
            auth: state,
            printQRInTerminal: true,
            browser: ['Laravel Attendance', 'Desktop', '1.0.0'],
            defaultQueryTimeoutMs: 60000,
        });

        // Handle connection updates
        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;
            
            if (qr) {
                qrCode = await QRCode.toDataURL(qr);
                connectionState = 'qr';
                io.emit('qrCode', qrCode);
                logger.info('QR Code generated');
            }

            if (connection === 'close') {
                const shouldReconnect = (lastDisconnect?.error)?.output?.statusCode !== DisconnectReason.loggedOut;
                isConnected = false;
                connectionState = 'disconnected';
                io.emit('connectionState', { state: 'disconnected' });
                
                logger.info('Connection closed due to', lastDisconnect?.error, ', reconnecting:', shouldReconnect);
                
                if (shouldReconnect) {
                    setTimeout(connectToWhatsApp, 5000);
                }
            } else if (connection === 'open') {
                isConnected = true;
                connectionState = 'connected';
                qrCode = null;
                io.emit('connectionState', { state: 'connected' });
                logger.info('WhatsApp connection opened');
            }
        });

        // Save credentials
        sock.ev.on('creds.update', saveCreds);

        // Handle messages
        sock.ev.on('messages.upsert', ({ messages }) => {
            const message = messages[0];
            if (!message.key.fromMe && message.message) {
                logger.info('Received message:', message);
                // Bisa diteruskan ke Laravel jika perlu
            }
        });

    } catch (error) {
        logger.error('Error connecting to WhatsApp:', error);
        setTimeout(connectToWhatsApp, 10000);
    }
}

// API Routes

// Get QR Code
app.get('/api/qr-code', (req, res) => {
    res.json({
        success: true,
        qrCode: qrCode,
        connectionState: connectionState
    });
});

// Get connection status
app.get('/api/status', (req, res) => {
    res.json({
        success: true,
        isConnected: isConnected,
        connectionState: connectionState
    });
});

// Send message
app.post('/api/send-message', async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(400).json({
                success: false,
                error: 'Phone number and message are required'
            });
        }

        if (!isConnected) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp not connected'
            });
        }

        // Format phone number (add country code if not present)
        let formattedPhone = phone.replace(/\D/g, '');
        if (!formattedPhone.startsWith('62')) {
            if (formattedPhone.startsWith('0')) {
                formattedPhone = '62' + formattedPhone.substring(1);
            } else {
                formattedPhone = '62' + formattedPhone;
            }
        }
        formattedPhone += '@s.whatsapp.net';

        const result = await sock.sendMessage(formattedPhone, { text: message });
        
        logger.info(`Message sent to ${phone}:`, result);
        
        res.json({
            success: true,
            messageId: result.key.id,
            data: result
        });

    } catch (error) {
        logger.error('Error sending message:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Send media (image, document, etc)
app.post('/api/send-media', async (req, res) => {
    try {
        const { phone, mediaUrl, caption, mediaType = 'image' } = req.body;

        if (!phone || !mediaUrl) {
            return res.status(400).json({
                success: false,
                error: 'Phone number and media URL are required'
            });
        }

        if (!isConnected) {
            return res.status(503).json({
                success: false,
                error: 'WhatsApp not connected'
            });
        }

        // Format phone number
        let formattedPhone = phone.replace(/\D/g, '');
        if (!formattedPhone.startsWith('62')) {
            if (formattedPhone.startsWith('0')) {
                formattedPhone = '62' + formattedPhone.substring(1);
            } else {
                formattedPhone = '62' + formattedPhone;
            }
        }
        formattedPhone += '@s.whatsapp.net';

        let mediaMessage = {};
        if (mediaType === 'image') {
            mediaMessage = {
                image: { url: mediaUrl },
                caption: caption || ''
            };
        } else if (mediaType === 'document') {
            mediaMessage = {
                document: { url: mediaUrl },
                caption: caption || ''
            };
        }

        const result = await sock.sendMessage(formattedPhone, mediaMessage);
        
        logger.info(`Media sent to ${phone}:`, result);
        
        res.json({
            success: true,
            messageId: result.key.id,
            data: result
        });

    } catch (error) {
        logger.error('Error sending media:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Disconnect WhatsApp
app.post('/api/disconnect', async (req, res) => {
    try {
        if (sock) {
            await sock.logout();
            await sock.end();
        }
        
        // Clear session
        await fs.remove('sessions');
        
        isConnected = false;
        connectionState = 'disconnected';
        qrCode = null;
        
        io.emit('connectionState', { state: 'disconnected' });
        
        res.json({
            success: true,
            message: 'Disconnected successfully'
        });

    } catch (error) {
        logger.error('Error disconnecting:', error);
        res.status(500).json({
            success: false,
            error: error.message
        });
    }
});

// Socket.IO connection
io.on('connection', (socket) => {
    logger.info('Client connected to Socket.IO');
    
    // Send current state to new client
    socket.emit('connectionState', { state: connectionState });
    if (qrCode) {
        socket.emit('qrCode', qrCode);
    }

    socket.on('disconnect', () => {
        logger.info('Client disconnected from Socket.IO');
    });
});

// Health check
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        timestamp: new Date().toISOString(),
        uptime: process.uptime()
    });
});

// Start server
const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    logger.info(`WhatsApp Gateway Server running on port ${PORT}`);
    console.log(`🚀 WhatsApp Gateway Server running on http://localhost:${PORT}`);
    console.log(`📱 Health check: http://localhost:${PORT}/health`);
    
    // Start WhatsApp connection
    connectToWhatsApp();
});

// Graceful shutdown
process.on('SIGINT', async () => {
    logger.info('Shutting down gracefully...');
    if (sock) {
        await sock.end();
    }
    server.close(() => {
        logger.info('Server closed');
        process.exit(0);
    });
});
