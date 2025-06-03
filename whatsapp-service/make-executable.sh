#!/bin/bash

# Make Linux scripts executable
# Run this script to set proper permissions for Linux auto-startup scripts

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "Setting executable permissions for Linux scripts..."

# List of scripts to make executable
SCRIPTS=(
    "setup-linux-autostart.sh"
    "install-systemd-service.sh"
    "verify-linux-autostart.sh"
)

for script in "${SCRIPTS[@]}"; do
    if [ -f "$SCRIPT_DIR/$script" ]; then
        chmod +x "$SCRIPT_DIR/$script"
        echo "✅ Made executable: $script"
    else
        echo "⚠️  Script not found: $script"
    fi
done

echo ""
echo "Done! You can now run:"
echo "  ./setup-linux-autostart.sh      - Setup PM2 auto-startup"
echo "  sudo ./install-systemd-service.sh  - Install systemd service"
echo "  ./verify-linux-autostart.sh     - Verify auto-startup setup"
