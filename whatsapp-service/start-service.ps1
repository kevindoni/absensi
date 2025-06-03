# PowerShell script to start WhatsApp Service
# Auto-startup script for Windows Task Scheduler

# Load configuration
. "$PSScriptRoot\config.ps1"

$servicePath = $global:WHATSAPP_SERVICE_PATH
$logFile = "$servicePath\logs\startup.log"

# Function to write logs
function Write-Log($message) {
    $timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    $logMessage = "[$timestamp] $message"
    Write-Host $logMessage
    Add-Content -Path $logFile -Value $logMessage
}

try {
    # Ensure log directory exists
    $logDir = Split-Path $logFile -Parent
    if (-not (Test-Path $logDir)) {
        New-Item -ItemType Directory -Path $logDir -Force
    }

    Write-Log "Starting WhatsApp Service startup script..."
    
    # Change to service directory
    Set-Location $servicePath
    Write-Log "Changed to directory: $servicePath"

    # Wait for system to be ready (optional delay)
    Start-Sleep -Seconds 30
    Write-Log "System ready, proceeding with service start..."

    # Check if Node.js is available
    try {
        $nodeVersion = & node --version
        Write-Log "Node.js version: $nodeVersion"
    } catch {
        Write-Log "ERROR: Node.js not found in PATH"
        exit 1
    }

    # Check if PM2 is available
    try {
        $pm2Version = & pm2 --version
        Write-Log "PM2 version: $pm2Version"
    } catch {
        Write-Log "ERROR: PM2 not found in PATH"
        exit 1
    }

    # Ping PM2 daemon
    Write-Log "Pinging PM2 daemon..."
    & pm2 ping

    # Check if whatsapp-service is already running
    $serviceList = & pm2 jlist | ConvertFrom-Json
    $existingService = $serviceList | Where-Object { $_.name -eq "whatsapp-service" }

    if ($existingService -and $existingService.pm2_env.status -eq "online") {
        Write-Log "WhatsApp Service is already running (PID: $($existingService.pid))"
    } else {
        Write-Log "Starting WhatsApp Service..."
        
        # Start the service
        & pm2 start ecosystem.config.js
        
        if ($LASTEXITCODE -eq 0) {
            Write-Log "WhatsApp Service started successfully!"
        } else {
            Write-Log "ERROR: Failed to start WhatsApp Service (Exit code: $LASTEXITCODE)"
            exit 1
        }
    }

    # Save PM2 process list
    & pm2 save
    Write-Log "PM2 process list saved"

    # Show final status
    Write-Log "Current service status:"
    & pm2 status

    Write-Log "WhatsApp Service startup completed successfully!"

} catch {
    Write-Log "ERROR: $($_.Exception.Message)"
    Write-Log "Stack trace: $($_.ScriptStackTrace)"
    exit 1
}
