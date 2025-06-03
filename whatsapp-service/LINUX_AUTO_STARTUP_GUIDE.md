# WhatsApp Service Auto-Startup Guide for Linux

Panduan lengkap untuk menjalankan WhatsApp service secara otomatis di Linux.

## 🐧 **OPSI 1: PM2 Startup (RECOMMENDED)**

### Setup PM2 Auto-Startup:

```bash
# 1. Install PM2 globally
sudo npm install -g pm2

# 2. Start service dengan PM2
cd /path/to/your/whatsapp-service
pm2 start ecosystem.config.js

# 3. Save PM2 process list
pm2 save

# 4. Generate dan install startup script
pm2 startup
# Follow the instructions to run the generated command with sudo

# 5. Test startup (optional)
pm2 resurrect
```

### Contoh Output PM2 Startup:
```bash
$ pm2 startup
[PM2] Init System found: systemd
[PM2] To setup the Startup Script, copy/paste the following command:
sudo env PATH=$PATH:/usr/bin /usr/lib/node_modules/pm2/bin/pm2 startup systemd -u $USER --hp $HOME

# Jalankan command yang diberikan PM2
```

## 🐧 **OPSI 2: systemd Service (Ubuntu/CentOS/RHEL)**

### 1. Buat systemd service file:

```bash
sudo nano /etc/systemd/system/whatsapp-service.service
```

### 2. Isi file service:

```ini
[Unit]
Description=WhatsApp Service for Laravel Attendance
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/path/to/your/whatsapp-service
ExecStart=/usr/bin/node server.js
Restart=always
RestartSec=10
StandardOutput=syslog
StandardError=syslog
SyslogIdentifier=whatsapp-service
Environment=NODE_ENV=production
Environment=PORT=3001

[Install]
WantedBy=multi-user.target
```

### 3. Enable dan start service:

```bash
# Reload systemd
sudo systemctl daemon-reload

# Enable auto-start
sudo systemctl enable whatsapp-service

# Start service
sudo systemctl start whatsapp-service

# Check status
sudo systemctl status whatsapp-service
```

## 🐧 **OPSI 3: Docker dengan Auto-Restart**

### 1. Buat Dockerfile:

```dockerfile
FROM node:18-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install

COPY . .

EXPOSE 3001

CMD ["node", "server.js"]
```

### 2. Build dan run dengan auto-restart:

```bash
# Build image
docker build -t whatsapp-service .

# Run dengan auto-restart
docker run -d \
  --name whatsapp-service \
  --restart=always \
  -p 3001:3001 \
  -v $(pwd)/sessions:/app/sessions \
  -v $(pwd)/logs:/app/logs \
  whatsapp-service
```

## 🐧 **OPSI 4: Cron @reboot (Simple)**

### 1. Edit crontab:

```bash
crontab -e
```

### 2. Tambahkan entry:

```bash
@reboot cd /path/to/your/whatsapp-service && npm start >> /var/log/whatsapp-service.log 2>&1
```

## 📊 **Monitoring Commands**

### PM2:
```bash
pm2 status          # Check status
pm2 logs            # View logs
pm2 restart all     # Restart
pm2 monit          # Real-time monitoring
```

### systemd:
```bash
sudo systemctl status whatsapp-service    # Status
sudo journalctl -u whatsapp-service -f    # Live logs
sudo systemctl restart whatsapp-service   # Restart
```

### Docker:
```bash
docker ps                                 # Check running containers
docker logs whatsapp-service -f           # Live logs
docker restart whatsapp-service           # Restart
```

## 🔧 **Troubleshooting**

### Permission Issues:
```bash
# Give proper permissions
sudo chown -R $USER:$USER /path/to/whatsapp-service
chmod +x /path/to/whatsapp-service/server.js
```

### Port Issues:
```bash
# Check if port 3001 is in use
sudo netstat -tulpn | grep 3001
sudo lsof -i :3001
```

### Node.js Path Issues:
```bash
# Find Node.js path
which node

# Update service file with correct path
sudo nano /etc/systemd/system/whatsapp-service.service
```

## ⚡ **Quick Setup Commands**

### PM2 Method (Recommended):
```bash
# One-liner setup
npm install -g pm2 && pm2 start ecosystem.config.js && pm2 save && pm2 startup
```

### systemd Method:
```bash
# Quick systemd setup
sudo cp whatsapp-service.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable whatsapp-service
sudo systemctl start whatsapp-service
```

## 🎯 **Rekomendasi per Distro**

- **Ubuntu/Debian**: PM2 startup atau systemd
- **CentOS/RHEL**: systemd atau PM2  
- **Alpine Linux**: PM2 atau Docker
- **Raspberry Pi**: PM2 startup

## 📝 **Log Locations**

- **PM2**: `~/.pm2/logs/`
- **systemd**: `journalctl -u whatsapp-service`
- **Docker**: `docker logs whatsapp-service`
- **Cron**: `/var/log/whatsapp-service.log`

---

**Status: BELUM DIKONFIGURASI untuk Linux**  
**Untuk mengkonfigurasi, pilih salah satu opsi di atas dan ikuti langkah-langkahnya.**
