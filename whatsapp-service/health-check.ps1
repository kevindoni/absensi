# PowerShell script to check WhatsApp Service health
$servicePath = "d:\laragon\www\absensi\whatsapp-service"
$logFile = "$servicePath\logs\health-check.log"

function Write-Log($message) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] $message"
    Write-Host $logMessage
    
    # Ensure log directory exists
    $logDir = Split-Path $logFile -Parent
    if (-not (Test-Path $logDir)) {
        New-Item -ItemType Directory -Path $logDir -Force
    }
    
    Add-Content -Path $logFile -Value $logMessage
}

function Test-ServiceHealth {
    try {
        # Test if service responds on port 3001
        $response = Invoke-WebRequest -Uri "http://localhost:3001/health" -TimeoutSec 10 -ErrorAction Stop
        return $response.StatusCode -eq 200
    } catch {
        return $false
    }
}

function Restart-Service {
    Write-Log "Attempting to restart WhatsApp Service..."
    
    Set-Location $servicePath
    
    # Stop service
    & pm2 stop whatsapp-service
    Start-Sleep -Seconds 5
    
    # Start service
    & pm2 start ecosystem.config.js
    Start-Sleep -Seconds 10
    
    Write-Log "Service restart completed"
}

# Main health check logic
Write-Log "Starting health check..."

# Check if PM2 process exists
$serviceList = & pm2 jlist | ConvertFrom-Json
$whatsappService = $serviceList | Where-Object { $_.name -eq "whatsapp-service" }

if (-not $whatsappService) {
    Write-Log "ERROR: WhatsApp service not found in PM2"
    Write-Log "Starting service..."
    
    Set-Location $servicePath
    & pm2 start ecosystem.config.js
    exit 0
}

$status = $whatsappService.pm2_env.status
$memory = [math]::Round($whatsappService.monit.memory / 1MB, 2)
$cpu = $whatsappService.monit.cpu

Write-Log "Service Status: $status"
Write-Log "Memory Usage: ${memory}MB"
Write-Log "CPU Usage: ${cpu}%"

if ($status -ne "online") {
    Write-Log "WARNING: Service is not online (Status: $status)"
    Restart-Service
} elseif (-not (Test-ServiceHealth)) {
    Write-Log "WARNING: Service health check failed"
    Restart-Service
} else {
    Write-Log "Service is healthy"
}

# Check memory usage (restart if over 500MB)
if ($memory -gt 500) {
    Write-Log "WARNING: High memory usage (${memory}MB), restarting service"
    Restart-Service
}

Write-Log "Health check completed"
