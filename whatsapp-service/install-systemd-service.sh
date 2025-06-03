#!/bin/bash

# Install WhatsApp Service as systemd service
# Run this script with sudo privileges

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

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "This script must be run as root (use sudo)"
    exit 1
fi

# Get current directory and paths
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SERVICE_DIR="$SCRIPT_DIR"
SERVICE_FILE="/etc/systemd/system/whatsapp-service.service"
TEMPLATE_FILE="$SCRIPT_DIR/whatsapp-service.service"

print_header "Installing WhatsApp Service as systemd Service"
echo ""

# Check if template service file exists
if [ ! -f "$TEMPLATE_FILE" ]; then
    print_error "Service template file not found: $TEMPLATE_FILE"
    exit 1
fi

# Check if Node.js exists
NODE_PATH=$(which node)
if [ -z "$NODE_PATH" ]; then
    print_error "Node.js not found. Please install Node.js first."
    exit 1
fi

print_status "Node.js found at: $NODE_PATH"
print_status "Service directory: $SERVICE_DIR"

# Get current user (who ran sudo)
REAL_USER=${SUDO_USER:-$USER}
REAL_GROUP=$(id -gn $REAL_USER)

print_status "Service will run as user: $REAL_USER:$REAL_GROUP"

# Create service file from template
print_status "Creating systemd service file..."

# Replace placeholders in template
sed -e "s|/path/to/your/whatsapp-service|$SERVICE_DIR|g" \
    -e "s|/usr/bin/node|$NODE_PATH|g" \
    -e "s|User=www-data|User=$REAL_USER|g" \
    -e "s|Group=www-data|Group=$REAL_GROUP|g" \
    "$TEMPLATE_FILE" > "$SERVICE_FILE"

print_status "Service file created at: $SERVICE_FILE"

# Create logs directory if it doesn't exist
mkdir -p "$SERVICE_DIR/logs"
chown $REAL_USER:$REAL_GROUP "$SERVICE_DIR/logs"

# Install dependencies if needed
if [ ! -d "$SERVICE_DIR/node_modules" ]; then
    print_status "Installing Node.js dependencies..."
    cd "$SERVICE_DIR"
    sudo -u $REAL_USER npm install
fi

# Reload systemd
print_status "Reloading systemd daemon..."
systemctl daemon-reload

# Enable service for auto-start
print_status "Enabling service for auto-start..."
systemctl enable whatsapp-service

# Start the service
print_status "Starting WhatsApp service..."
systemctl start whatsapp-service

# Wait a moment for service to start
sleep 3

# Check service status
print_header "Service Status"
systemctl status whatsapp-service --no-pager

# Test health endpoint
echo ""
print_status "Testing health endpoint..."
if curl -s http://localhost:3001/health > /dev/null; then
    print_status "✅ Health endpoint is responding!"
    curl -s http://localhost:3001/health | python3 -m json.tool 2>/dev/null || curl -s http://localhost:3001/health
else
    print_warning "⚠️  Health endpoint not responding. Check service logs:"
    echo "  sudo journalctl -u whatsapp-service -f"
fi

echo ""
print_header "Installation Complete"
echo ""
print_status "✅ systemd service: INSTALLED"
print_status "✅ Auto-start: ENABLED"
print_status "✅ Service: RUNNING"
echo ""
print_status "Management commands:"
echo "  sudo systemctl start whatsapp-service     - Start service"
echo "  sudo systemctl stop whatsapp-service      - Stop service"
echo "  sudo systemctl restart whatsapp-service   - Restart service"
echo "  sudo systemctl status whatsapp-service    - Check status"
echo "  sudo journalctl -u whatsapp-service -f    - View live logs"
echo ""
print_status "Service will now start automatically on system boot!"

# Create uninstall script
cat > "$SERVICE_DIR/uninstall-systemd-service.sh" << 'EOF'
#!/bin/bash

# Uninstall WhatsApp systemd service

if [ "$EUID" -ne 0 ]; then
    echo "This script must be run as root (use sudo)"
    exit 1
fi

echo "Stopping and disabling WhatsApp service..."
systemctl stop whatsapp-service
systemctl disable whatsapp-service

echo "Removing service file..."
rm -f /etc/systemd/system/whatsapp-service.service

echo "Reloading systemd..."
systemctl daemon-reload

echo "WhatsApp systemd service uninstalled successfully!"
EOF

chmod +x "$SERVICE_DIR/uninstall-systemd-service.sh"
chown $REAL_USER:$REAL_GROUP "$SERVICE_DIR/uninstall-systemd-service.sh"

print_status "Uninstall script created: $SERVICE_DIR/uninstall-systemd-service.sh"
echo ""
