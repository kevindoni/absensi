# WhatsApp Service Auto-Running Guide

Panduan lengkap untuk menjalankan WhatsApp service secara otomatis di **Windows** dan **Linux**.

## 🪟 **WINDOWS SETUP (CURRENT)**

**✅ STATUS: SUDAH DIKONFIGURASI dan BERJALAN**

- ✅ PM2 process manager running
- ✅ Windows Startup folder integration 
- ✅ Auto-restart on system boot
- ✅ Health monitoring and logging

### Windows Commands:
```bash
npm run verify       # Verify auto-startup setup
npm run health       # Run health check
npm run pm2:status   # Check service status
```

---

## 🐧 **LINUX SETUP (BELUM DIKONFIGURASI)**

**❌ STATUS: BELUM DIKONFIGURASI untuk Linux**

### Quick Linux Setup:

#### Opsi 1: PM2 Auto-Startup (Recommended)
```bash
# Make scripts executable
chmod +x *.sh

# Setup PM2 auto-startup
./setup-linux-autostart.sh

# Verify setup
./verify-linux-autostart.sh
```

#### Opsi 2: systemd Service
```bash
# Install as systemd service (requires sudo)
sudo ./install-systemd-service.sh

# Verify
./verify-linux-autostart.sh
```

### Linux Commands:
```bash
npm run setup:linux     # Setup PM2 auto-startup
npm run install:systemd # Install systemd service
npm run verify:linux    # Verify Linux auto-startup
```

📖 **Detailed Linux Guide:** `LINUX_AUTO_STARTUP_GUIDE.md`

---

## 🔄 Opsi 1: PM2 Process Manager (Recommended)

### Setup PM2:
```bash
# Install PM2 globally
npm install -g pm2

# Start service dengan PM2
npm run pm2:start

# Check status
npm run pm2:status

# View logs
npm run pm2:logs
```

### Perintah PM2 Tersedia:
- `npm run pm2:start` - Start service
- `npm run pm2:stop` - Stop service  
- `npm run pm2:restart` - Restart service
- `npm run pm2:delete` - Delete dari PM2
- `npm run pm2:status` - Check status
- `npm run pm2:logs` - View logs

### Auto-start dengan Windows Task Scheduler:

1. **Buka Task Scheduler** (Win + R, ketik `taskschd.msc`)

2. **Create Basic Task**:
   - Name: `WhatsApp Service Startup`
   - Description: `Auto-start WhatsApp service on Windows boot`

3. **Trigger**: `When the computer starts`

4. **Action**: `Start a program`
   - Program: `powershell.exe`
   - Arguments: `-ExecutionPolicy Bypass -File "d:\laragon\www\absensi\whatsapp-service\start-service.ps1"`

5. **Settings**:
   - ✅ Run whether user is logged on or not
   - ✅ Run with highest privileges
   - ✅ Configure for Windows 10

## 🔄 Opsi 2: Windows Service (True Service)

### Install sebagai Windows Service:
```bash
# Install service (Run as Administrator)
npm run service:install

# Uninstall service
npm run service:uninstall
```

⚠️ **Catatan**: Memerlukan administrator privileges

## 🔄 Opsi 3: Manual Startup Scripts

### Menggunakan Batch Files:
- `start-service.bat` - Manual start
- `stop-service.bat` - Manual stop  
- `restart-service.bat` - Manual restart

### Menggunakan PowerShell:
- `start-service.ps1` - Advanced startup script

## 📊 Monitoring & Troubleshooting

### Check Service Status:
```bash
pm2 status
pm2 monit
```

### View Logs:
```bash
pm2 logs whatsapp-service
pm2 logs whatsapp-service --lines 100
```

### Restart Jika Bermasalah:
```bash
npm run pm2:restart
```

### Reset Semua:
```bash
npm run pm2:delete
npm run pm2:start
```

## 🎯 Rekomendasi Production

1. **Gunakan PM2** untuk development dan testing
2. **Gunakan Windows Service** untuk production server
3. **Gunakan Task Scheduler** untuk auto-start dengan user session

## 🔧 Configuration Files

- `ecosystem.config.js` - PM2 configuration
- `install-service.js` - Windows service installer
- `start-service.ps1` - PowerShell startup script

## 🚀 Quick Start

```bash
# Method 1: PM2 (Recommended)
npm run pm2:start

# Method 2: Windows Service (Admin required)
npm run service:install

# Method 3: Manual
node server.js
```

## ✅ **CURRENT STATUS: AUTO-STARTUP CONFIGURED**

✅ **WhatsApp Service is now configured to auto-start on Windows boot!**

**What's been set up:**
- ✅ PM2 process manager running the service
- ✅ Windows Startup folder integration (no admin required)
- ✅ Auto-restart on system boot
- ✅ Health monitoring and logging
- ✅ HTTP endpoint monitoring

**Verification:**
```bash
npm run verify     # Check auto-startup status
npm run health     # Run health check
npm run pm2:status # Check PM2 status
```

## 🔧 Monitoring Commands

## 📝 Logs Location

- PM2 Logs: `./logs/`
- Windows Service Logs: Check Windows Event Viewer
- Console Logs: Terminal output

## 🎯 **SETUP COMPLETE SUMMARY**

**✅ Your WhatsApp Service is now fully configured for auto-startup!**

**Installed Components:**
- PM2 Process Manager (running)
- Windows Startup Folder Integration
- Health Check Monitoring
- Complete logging system

**Available Commands:**
```bash
npm run verify       # Verify auto-startup setup
npm run health       # Run health check
npm run pm2:status   # Check service status
npm run pm2:restart  # Restart service
npm run pm2:logs     # View service logs
```

**Auto-startup Method Used:** Windows Startup Folder (User-level, no admin required)

**Service Status:** ✅ ONLINE and READY

**To Test Auto-startup:** Restart your computer - the service will start automatically!
