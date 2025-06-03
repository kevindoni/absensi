@echo off
title WhatsApp Service Path Configuration
echo ========================================
echo    WhatsApp Service Path Setup
echo ========================================
echo.
echo This script will help you configure the WhatsApp service path.
echo Current path: %~dp0
echo.

set /p "newPath=Enter the new service path (or press Enter to keep current): "

if "%newPath%"=="" (
    echo Keeping current path: %~dp0
    set "newPath=%~dp0"
) else (
    echo Setting new path to: %newPath%
)

echo.
echo Updating configuration files...

REM Update config.bat
echo @echo off > "%~dp0config.bat"
echo REM WhatsApp Service Configuration >> "%~dp0config.bat"
echo REM This file contains the configurable path for the WhatsApp service >> "%~dp0config.bat"
echo. >> "%~dp0config.bat"
echo REM Service Path Configuration (change this to match your installation) >> "%~dp0config.bat"
echo set WHATSAPP_SERVICE_PATH=%newPath% >> "%~dp0config.bat"

REM Update config.ps1
echo # WhatsApp Service Configuration > "%~dp0config.ps1"
echo # This file contains the configurable path for the WhatsApp service >> "%~dp0config.ps1"
echo. >> "%~dp0config.ps1"
echo # Service Path Configuration >> "%~dp0config.ps1"
echo $global:WHATSAPP_SERVICE_PATH = "%newPath%" >> "%~dp0config.ps1"
echo. >> "%~dp0config.ps1"
echo # Log directory will be relative to service path >> "%~dp0config.ps1"
echo $global:LOG_DIR = "$global:WHATSAPP_SERVICE_PATH\logs" >> "%~dp0config.ps1"
echo. >> "%~dp0config.ps1"
echo # Export for use in other scripts >> "%~dp0config.ps1"
echo $env:WHATSAPP_SERVICE_PATH = $global:WHATSAPP_SERVICE_PATH >> "%~dp0config.ps1"

echo Configuration files updated successfully!
echo.
echo IMPORTANT: If you changed the path, you need to:
echo 1. Move all WhatsApp service files to the new location
echo 2. Update any existing Windows startup shortcuts
echo 3. Update PM2 ecosystem configuration
echo.
pause
