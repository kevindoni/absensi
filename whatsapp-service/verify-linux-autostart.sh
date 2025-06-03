#!/bin/bash

# Verification script for WhatsApp Service Auto-startup on Linux
# This script helps verify that auto-startup is working correctly

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

print_success() {
    echo -e "${GREEN}✅${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠️${NC} $1"
}

print_error() {
    echo -e "${RED}❌${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️${NC} $1"
}

# Get current directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

print_header "WhatsApp Service Auto-Startup Verification (Linux)"
echo ""

# Check if Node.js is available
if command -v node &> /dev/null; then
    print_success "Node.js available: $(node --version)"
else
    print_error "Node.js not found"
fi

# Check if PM2 is available
echo ""
echo "PM2 Process Manager:"
if command -v pm2 &> /dev/null; then
    print_success "PM2 available: $(pm2 --version)"
    
    # Check PM2 service status
    echo ""
    echo "PM2 Service Status:"
    if pm2 list | grep -q "whatsapp-service"; then
        print_success "WhatsApp service found in PM2"
        pm2 list | grep -E "(App name|whatsapp-service)"
    else
        print_warning "WhatsApp service not found in PM2"
    fi
    
    # Check PM2 startup
    if pm2 startup | grep -q "already"; then
        print_success "PM2 startup is configured"
    else
        print_warning "PM2 startup may not be configured"
    fi
    
else
    print_warning "PM2 not available"
fi

# Check systemd service
echo ""
echo "systemd Service:"
if systemctl list-unit-files | grep -q "whatsapp-service.service"; then
    print_success "systemd service installed"
    
    # Check if enabled
    if systemctl is-enabled whatsapp-service &> /dev/null; then
        print_success "systemd service is enabled for auto-start"
    else
        print_warning "systemd service is not enabled for auto-start"
    fi
    
    # Check if active
    if systemctl is-active whatsapp-service &> /dev/null; then
        print_success "systemd service is currently running"
    else
        print_error "systemd service is not running"
    fi
    
    # Show service status
    echo ""
    echo "systemd Service Status:"
    systemctl status whatsapp-service --no-pager -l
    
else
    print_info "systemd service not installed"
fi

# Check if process is running on port 3001
echo ""
echo "Port Check:"
if netstat -tulpn 2>/dev/null | grep -q ":3001"; then
    print_success "Service is listening on port 3001"
    netstat -tulpn 2>/dev/null | grep ":3001"
elif lsof -i :3001 &> /dev/null; then
    print_success "Service is listening on port 3001"
    lsof -i :3001
else
    print_error "No service listening on port 3001"
fi

# Test HTTP health endpoint
echo ""
echo "HTTP Health Endpoint Test:"
if command -v curl &> /dev/null; then
    if curl -s --connect-timeout 5 http://localhost:3001/health > /dev/null; then
        print_success "Health endpoint responding"
        echo "Response:"
        curl -s http://localhost:3001/health | python3 -m json.tool 2>/dev/null || curl -s http://localhost:3001/health
    else
        print_error "Health endpoint not responding"
    fi
else
    print_warning "curl not available for health check"
fi

# Check logs
echo ""
echo "Logs Location:"
LOGS_DIR="$SCRIPT_DIR/logs"
if [ -d "$LOGS_DIR" ]; then
    print_success "Logs directory found: $LOGS_DIR"
    
    # Show recent log files
    if [ "$(ls -A $LOGS_DIR 2>/dev/null)" ]; then
        echo "Recent log files:"
        ls -la "$LOGS_DIR" | head -10
    else
        print_warning "Logs directory is empty"
    fi
else
    print_warning "Logs directory not found"
fi

# Check PM2 logs
if command -v pm2 &> /dev/null; then
    PM2_LOGS_DIR="$HOME/.pm2/logs"
    if [ -d "$PM2_LOGS_DIR" ]; then
        echo ""
        echo "PM2 Logs:"
        print_success "PM2 logs directory: $PM2_LOGS_DIR"
        if ls "$PM2_LOGS_DIR"/*whatsapp-service* &> /dev/null; then
            echo "WhatsApp service log files:"
            ls -la "$PM2_LOGS_DIR"/*whatsapp-service* | head -5
        fi
    fi
fi

# Show systemd logs if service exists
if systemctl list-unit-files | grep -q "whatsapp-service.service"; then
    echo ""
    echo "Recent systemd Logs:"
    journalctl -u whatsapp-service --no-pager -n 10 --since "1 hour ago" || print_warning "Cannot access systemd logs"
fi

echo ""
print_header "Verification Complete"
echo ""

print_info "Manual Control Commands:"
echo ""
if command -v pm2 &> /dev/null; then
    echo "PM2 Commands:"
    echo "  pm2 status           - Check PM2 service status"
    echo "  pm2 restart all      - Restart service"
    echo "  pm2 logs             - View logs"
    echo "  pm2 monit           - Real-time monitoring"
    echo ""
fi

if systemctl list-unit-files | grep -q "whatsapp-service.service"; then
    echo "systemd Commands:"
    echo "  sudo systemctl status whatsapp-service    - Check status"
    echo "  sudo systemctl restart whatsapp-service   - Restart service"
    echo "  sudo journalctl -u whatsapp-service -f    - View live logs"
    echo ""
fi

print_info "Health Check:"
echo "  curl http://localhost:3001/health"
echo ""

print_info "To test auto-startup:"
echo "  sudo reboot"
echo ""
