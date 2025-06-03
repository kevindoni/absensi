@echo off
echo Starting WhatsApp Service...
cd /d "d:\laragon\www\absensi\whatsapp-service"
pm2 start ecosystem.config.js
echo WhatsApp Service started!
pause
