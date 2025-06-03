#!/bin/bash

# Quick Setup Script - Auto-detect platform and setup WhatsApp Service auto-startup
# This script works on both Windows (Git Bash/WSL) and Linux

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

# Detect platform
detect_platform() {
    if [[ "$OSTYPE" == "msys" ]] || [[ "$OSTYPE" == "win32" ]] || [[ "$OSTYPE" == "cygwin" ]]; then
        echo "windows"
    elif [[ "$OSTYPE" == "linux-gnu"* ]]; then
        echo "linux"
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        echo "macos"
    else
        echo "unknown"
    fi
}

PLATFORM=$(detect_platform)

print_header "WhatsApp Service Quick Auto-Startup Setup"
echo ""
print_status "Detected platform: $PLATFORM"
echo ""

case $PLATFORM in
    "windows")
        print_status "Setting up for Windows..."
        echo ""
        
        # Check if already configured
        if powershell -Command "Test-Path '$env:APPDATA\\Microsoft\\Windows\\Start Menu\\Programs\\Startup\\WhatsApp Service Startup.lnk'" 2>/dev/null | grep -q "True"; then
            print_status "✅ Windows auto-startup is already configured!"
            
            # Verify current status
            print_status "Running verification..."
            if command -v powershell &> /dev/null; then
                powershell -ExecutionPolicy Bypass -File verify-auto-startup.ps1
            else
                print_warning "PowerShell not available for verification"
            fi
        else
            print_status "Configuring Windows auto-startup..."
            
            # Setup startup folder integration
            if powershell -ExecutionPolicy Bypass -File add-to-startup.ps1; then
                print_status "✅ Windows auto-startup configured successfully!"
            else
                print_error "Failed to configure Windows auto-startup"
                exit 1
            fi
        fi
        
        print_status "Available Windows commands:"
        echo "  npm run verify     - Verify setup"
        echo "  npm run health     - Health check"
        echo "  npm run pm2:status - Check PM2 status"
        ;;
        
    "linux")
        print_status "Setting up for Linux..."
        echo ""
        
        # Make scripts executable
        chmod +x setup-linux-autostart.sh verify-linux-autostart.sh install-systemd-service.sh 2>/dev/null || true
        
        # Check if PM2 startup is already configured
        if command -v pm2 &> /dev/null && pm2 startup 2>&1 | grep -q "already"; then
            print_status "✅ PM2 auto-startup is already configured!"
            
            # Check if service is running
            if pm2 list | grep -q "whatsapp-service"; then
                print_status "✅ WhatsApp service is running in PM2"
            else
                print_status "Starting WhatsApp service with PM2..."
                pm2 start ecosystem.config.js
                pm2 save
            fi
            
            # Run verification
            ./verify-linux-autostart.sh
        else
            print_status "Running Linux auto-startup setup..."
            ./setup-linux-autostart.sh
        fi
        
        print_status "Available Linux commands:"
        echo "  npm run verify:linux    - Verify setup"
        echo "  npm run setup:linux     - Setup PM2 auto-startup"
        echo "  npm run install:systemd - Install systemd service"
        ;;
        
    "macos")
        print_warning "macOS detected - using PM2 setup similar to Linux"
        print_status "Setting up PM2 auto-startup for macOS..."
        
        # Install PM2 if not available
        if ! command -v pm2 &> /dev/null; then
            print_status "Installing PM2..."
            npm install -g pm2
        fi
        
        # Start service
        pm2 start ecosystem.config.js
        pm2 save
        
        # Setup startup
        print_status "Setting up PM2 startup..."
        pm2 startup
        
        print_status "✅ macOS setup complete!"
        print_status "Follow the PM2 startup instructions above if prompted."
        ;;
        
    *)
        print_error "Unsupported platform: $PLATFORM"
        print_status "Please manually setup using one of these methods:"
        echo "  - PM2: pm2 start ecosystem.config.js && pm2 save && pm2 startup"
        echo "  - systemd: sudo ./install-systemd-service.sh"
        exit 1
        ;;
esac

echo ""
print_header "Setup Summary"
echo ""
print_status "Platform: $PLATFORM"
print_status "Service Status:"

# Check service status regardless of platform
if command -v pm2 &> /dev/null; then
    pm2 status 2>/dev/null || print_warning "PM2 not running or no processes"
fi

# Test health endpoint
echo ""
print_status "Testing health endpoint..."
if command -v curl &> /dev/null; then
    if curl -s --connect-timeout 5 http://localhost:3001/health > /dev/null 2>&1; then
        print_status "✅ Health endpoint responding!"
        curl -s http://localhost:3001/health
    else
        print_warning "⚠️  Health endpoint not responding (service may still be starting)"
    fi
else
    print_warning "curl not available for health check"
fi

echo ""
print_header "Next Steps"
echo ""
print_status "1. Test auto-startup by rebooting your system"
print_status "2. Monitor logs and service status"
print_status "3. Check documentation for advanced configuration"
echo ""
print_status "Documentation files:"
echo "  - AUTO_RUNNING_GUIDE.md (Windows & Linux overview)"
echo "  - LINUX_AUTO_STARTUP_GUIDE.md (Detailed Linux guide)"
echo ""
