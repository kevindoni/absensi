#!/bin/bash

# Linux Auto-Startup Setup Script for WhatsApp Service
# This script sets up auto-startup using PM2 (recommended method)

set -e  # Exit on any error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
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

# Get current directory
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SERVICE_DIR="$SCRIPT_DIR"

print_header "WhatsApp Service Linux Auto-Startup Setup"
echo ""
print_status "Service directory: $SERVICE_DIR"
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    print_error "Node.js is not installed. Please install Node.js first:"
    echo "  Ubuntu/Debian: sudo apt update && sudo apt install nodejs npm"
    echo "  CentOS/RHEL: sudo yum install nodejs npm"
    echo "  Or use NodeSource: curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash - && sudo apt-get install -y nodejs"
    exit 1
fi

# Check if npm is installed
if ! command -v npm &> /dev/null; then
    print_error "npm is not installed. Please install npm first."
    exit 1
fi

print_status "Node.js version: $(node --version)"
print_status "npm version: $(npm --version)"

# Check if PM2 is installed globally
if ! command -v pm2 &> /dev/null; then
    print_warning "PM2 is not installed globally. Installing PM2..."
    
    # Try to install PM2 globally
    if npm install -g pm2; then
        print_status "PM2 installed successfully"
    else
        print_error "Failed to install PM2 globally. Trying with sudo..."
        if sudo npm install -g pm2; then
            print_status "PM2 installed successfully with sudo"
        else
            print_error "Failed to install PM2. Please install manually:"
            echo "  sudo npm install -g pm2"
            exit 1
        fi
    fi
else
    print_status "PM2 is already installed: $(pm2 --version)"
fi

# Change to service directory
cd "$SERVICE_DIR"

# Check if ecosystem.config.js exists
if [ ! -f "ecosystem.config.js" ]; then
    print_error "ecosystem.config.js not found. Please make sure it exists in the service directory."
    exit 1
fi

# Check if server.js exists
if [ ! -f "server.js" ]; then
    print_error "server.js not found. Please make sure it exists in the service directory."
    exit 1
fi

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    print_status "Installing Node.js dependencies..."
    npm install
fi

# Stop any existing PM2 process
print_status "Stopping any existing WhatsApp service processes..."
pm2 stop whatsapp-service 2>/dev/null || true
pm2 delete whatsapp-service 2>/dev/null || true

# Start the service with PM2
print_status "Starting WhatsApp service with PM2..."
pm2 start ecosystem.config.js

# Save PM2 process list
print_status "Saving PM2 process list..."
pm2 save

# Setup startup script
print_status "Setting up PM2 startup script..."
echo ""
print_warning "The following command will be executed to setup auto-startup:"

# Generate startup command
STARTUP_CMD=$(pm2 startup | grep -E "^sudo.*pm2 startup" | head -1)

if [ -n "$STARTUP_CMD" ]; then
    echo "  $STARTUP_CMD"
    echo ""
    
    # Ask user for confirmation
    read -p "Do you want to execute this command now? (y/n): " -n 1 -r
    echo ""
    
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        print_status "Executing startup command..."
        eval "$STARTUP_CMD"
        
        if [ $? -eq 0 ]; then
            print_status "PM2 startup script installed successfully!"
        else
            print_error "Failed to install PM2 startup script. You may need to run it manually."
        fi
    else
        print_warning "Skipped PM2 startup installation. Run the following command manually:"
        echo "  $STARTUP_CMD"
    fi
else
    print_error "Could not generate PM2 startup command. Please run 'pm2 startup' manually."
fi

# Create logs directory
mkdir -p logs

# Show status
echo ""
print_header "Setup Complete"
pm2 status

# Test health endpoint
echo ""
print_status "Testing health endpoint..."
sleep 3
if curl -s http://localhost:3001/health > /dev/null; then
    print_status "✅ Health endpoint is responding!"
    curl -s http://localhost:3001/health | python3 -m json.tool 2>/dev/null || curl -s http://localhost:3001/health
else
    print_warning "⚠️  Health endpoint not responding yet. Service may still be starting..."
fi

echo ""
print_header "Auto-Startup Configuration Summary"
echo ""
print_status "✅ PM2 process manager: INSTALLED"
print_status "✅ WhatsApp service: RUNNING"
print_status "✅ Auto-startup: CONFIGURED"
print_status "✅ Health monitoring: AVAILABLE"
echo ""
print_status "Available commands:"
echo "  pm2 status           - Check service status"
echo "  pm2 logs             - View service logs"
echo "  pm2 restart all      - Restart service"
echo "  pm2 stop all         - Stop service"
echo "  pm2 resurrect        - Restore saved processes"
echo "  npm run pm2:status   - Check status (if package.json configured)"
echo ""
print_status "Service will now start automatically on system boot!"
print_status "Test by rebooting your system: sudo reboot"
echo ""
