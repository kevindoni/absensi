@echo off
echo Starting WhatsApp Service...

REM Load configuration
call "%~dp0config.bat"

cd /d "%WHATSAPP_SERVICE_PATH%"
pm2 start ecosystem.config.js
echo WhatsApp Service started!
pause
