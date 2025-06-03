@echo off
echo Restarting WhatsApp Service...
cd /d "d:\laragon\www\absensi\whatsapp-service"
pm2 restart whatsapp-service
echo WhatsApp Service restarted!
pause
