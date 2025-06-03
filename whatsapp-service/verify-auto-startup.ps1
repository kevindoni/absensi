# Verification script for WhatsApp Service Auto-startup
# This script helps verify that auto-startup is working correctly

# Load configuration
. "$PSScriptRoot\config.ps1"

$servicePath = $global:WHATSAPP_SERVICE_PATH

Write-Host "=== WhatsApp Service Auto-Startup Verification ===" -ForegroundColor Green
Write-Host ""

# Check if startup shortcut exists
$startupPath = "$env:APPDATA\Microsoft\Windows\Start Menu\Programs\Startup\WhatsApp Service Startup.lnk"
if (Test-Path $startupPath) {
    Write-Host "✅ Startup shortcut found: $startupPath" -ForegroundColor Green
} else {
    Write-Host "❌ Startup shortcut NOT found" -ForegroundColor Red
}

# Check PM2 service status
Write-Host ""
Write-Host "PM2 Service Status:" -ForegroundColor Yellow
try {
    & pm2 status
} catch {
    Write-Host "❌ PM2 not available or service not running" -ForegroundColor Red
}

# Check HTTP health endpoint
Write-Host ""
Write-Host "Testing HTTP Health Endpoint..." -ForegroundColor Yellow
try {
    $response = Invoke-RestMethod -Uri "http://localhost:3001/health" -TimeoutSec 5
    Write-Host "✅ Health endpoint responding:" -ForegroundColor Green
    Write-Host "   Status: $($response.status)"
    Write-Host "   Uptime: $($response.uptime) seconds"
} catch {
    Write-Host "❌ Health endpoint not responding" -ForegroundColor Red
    Write-Host "   Error: $($_.Exception.Message)"
}

# Show logs directory
Write-Host ""
Write-Host "Logs Location:" -ForegroundColor Yellow
$logsDir = "$servicePath\logs"
if (Test-Path $logsDir) {
    Write-Host "✅ Logs directory: $logsDir"
    $logFiles = Get-ChildItem $logsDir -File | Sort-Object LastWriteTime -Descending | Select-Object -First 5
    if ($logFiles) {
        Write-Host "   Recent log files:"
        foreach ($file in $logFiles) {
            Write-Host "   - $($file.Name) ($(Get-Date $file.LastWriteTime -Format 'yyyy-MM-dd HH:mm:ss'))"
        }
    }
} else {
    Write-Host "⚠️  Logs directory not found" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=== Verification Complete ===" -ForegroundColor Green
Write-Host ""
Write-Host "To manually control the service:" -ForegroundColor Cyan
Write-Host "• Start: npm run pm2:start"
Write-Host "• Stop: npm run pm2:stop" 
Write-Host "• Restart: npm run pm2:restart"
Write-Host "• Logs: npm run pm2:logs"
Write-Host "• Status: npm run pm2:status"
Write-Host ""
Write-Host "To remove auto-startup:" -ForegroundColor Cyan
Write-Host "• Press Win+R, type 'shell:startup', delete 'WhatsApp Service Startup.lnk'"
