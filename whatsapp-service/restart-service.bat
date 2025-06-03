@echo off
echo Restarting WhatsApp Service...

REM Load configuration
call "%~dp0config.bat"

cd /d "%WHATSAPP_SERVICE_PATH%"
pm2 restart whatsapp-service
echo WhatsApp Service restarted!
pause
