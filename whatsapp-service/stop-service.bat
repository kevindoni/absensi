@echo off
echo Stopping WhatsApp Service...

REM Load configuration
call "%~dp0config.bat"

cd /d "%WHATSAPP_SERVICE_PATH%"
pm2 stop whatsapp-service
echo WhatsApp Service stopped!
pause
