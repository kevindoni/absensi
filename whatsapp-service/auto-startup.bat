@echo off
title WhatsApp Service Auto Startup

echo ========================================
echo    WhatsApp Service Auto Startup
echo ========================================
echo.

REM Load configuration
call "%~dp0config.bat"

:: Navigate to service directory
cd /d "%WHATSAPP_SERVICE_PATH%"

:: Wait for system to be ready
echo Waiting for system to be ready...
timeout /t 30 /nobreak >nul

:: Check if Node.js is available
node --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: Node.js not found in PATH
    echo Please ensure Node.js is installed and added to PATH
    pause
    exit /b 1
)

:: Check if PM2 is available
pm2 --version >nul 2>&1
if errorlevel 1 (
    echo ERROR: PM2 not found in PATH
    echo Installing PM2 globally...
    npm install -g pm2
    if errorlevel 1 (
        echo Failed to install PM2
        pause
        exit /b 1
    )
)

echo Starting WhatsApp Service...

:: Ping PM2 daemon
pm2 ping

:: Start the service
pm2 start ecosystem.config.js

:: Save process list
pm2 save

:: Show status
echo.
echo Current service status:
pm2 status

echo.
echo WhatsApp Service started successfully!
echo You can close this window now.

:: Keep window open for a few seconds
timeout /t 5 /nobreak >nul
