# WhatsApp Service Configuration
# This file contains the configurable path for the WhatsApp service

# Service Path Configuration
$global:WHATSAPP_SERVICE_PATH = "d:\laragon\www\absensi\whatsapp-service"

# You can change this path to match your installation directory
# Examples:
# $global:WHATSAPP_SERVICE_PATH = "C:\Projects\absensi\whatsapp-service"
# $global:WHATSAPP_SERVICE_PATH = "E:\MyApps\whatsapp-service"

# Log directory will be relative to service path
$global:LOG_DIR = "$global:WHATSAPP_SERVICE_PATH\logs"

# Export for use in other scripts
$env:WHATSAPP_SERVICE_PATH = $global:WHATSAPP_SERVICE_PATH
