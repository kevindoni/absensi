@echo off
echo Stopping WhatsApp Service...
cd /d "d:\laragon\www\absensi\whatsapp-service"
pm2 stop whatsapp-service
echo WhatsApp Service stopped!
pause
