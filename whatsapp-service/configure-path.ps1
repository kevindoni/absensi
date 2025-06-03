# PowerShell script to configure WhatsApp Service path
# Run this script to change the service installation path

param(
    [string]$NewPath = ""
)

Write-Host "========================================" -ForegroundColor Green
Write-Host "    WhatsApp Service Path Setup" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

$currentPath = $PSScriptRoot
Write-Host "Current service path: $currentPath" -ForegroundColor Yellow

if ([string]::IsNullOrEmpty($NewPath)) {
    $NewPath = Read-Host "Enter the new service path (or press Enter to keep current)"
}

if ([string]::IsNullOrEmpty($NewPath)) {
    $NewPath = $currentPath
    Write-Host "Keeping current path: $currentPath" -ForegroundColor Green
} else {
    Write-Host "Setting new path to: $NewPath" -ForegroundColor Green
}

Write-Host ""
Write-Host "Updating configuration files..." -ForegroundColor Yellow

# Update config.ps1
$configPsContent = @"
# WhatsApp Service Configuration
# This file contains the configurable path for the WhatsApp service

# Service Path Configuration
`$global:WHATSAPP_SERVICE_PATH = "$NewPath"

# You can change this path to match your installation directory
# Examples:
# `$global:WHATSAPP_SERVICE_PATH = "C:\Projects\absensi\whatsapp-service"
# `$global:WHATSAPP_SERVICE_PATH = "E:\MyApps\whatsapp-service"

# Log directory will be relative to service path
`$global:LOG_DIR = "`$global:WHATSAPP_SERVICE_PATH\logs"

# Export for use in other scripts
`$env:WHATSAPP_SERVICE_PATH = `$global:WHATSAPP_SERVICE_PATH
"@

Set-Content -Path "$PSScriptRoot\config.ps1" -Value $configPsContent

# Update config.bat
$configBatContent = @"
@echo off
REM WhatsApp Service Configuration
REM This file contains the configurable path for the WhatsApp service

REM Service Path Configuration (change this to match your installation)
set WHATSAPP_SERVICE_PATH=$NewPath

REM You can change this path to match your installation directory
REM Examples:
REM set WHATSAPP_SERVICE_PATH=C:\Projects\absensi\whatsapp-service
REM set WHATSAPP_SERVICE_PATH=E:\MyApps\whatsapp-service
"@

Set-Content -Path "$PSScriptRoot\config.bat" -Value $configBatContent

Write-Host "Configuration files updated successfully!" -ForegroundColor Green
Write-Host ""

if ($NewPath -ne $currentPath) {
    Write-Host "IMPORTANT: Since you changed the path, you need to:" -ForegroundColor Red
    Write-Host "1. Move all WhatsApp service files to: $NewPath" -ForegroundColor Yellow
    Write-Host "2. Update any existing Windows startup shortcuts" -ForegroundColor Yellow
    Write-Host "3. Remove old PM2 process: pm2 delete whatsapp-service" -ForegroundColor Yellow
    Write-Host "4. Start service from new location" -ForegroundColor Yellow
    Write-Host ""
    
    $moveFiles = Read-Host "Do you want to move files to the new location now? (y/n)"
    if ($moveFiles -eq "y" -or $moveFiles -eq "Y") {
        try {
            # Create destination directory if it doesn't exist
            if (-not (Test-Path $NewPath)) {
                New-Item -ItemType Directory -Path $NewPath -Force
                Write-Host "Created directory: $NewPath" -ForegroundColor Green
            }
            
            # Copy all files
            Copy-Item -Path "$currentPath\*" -Destination $NewPath -Recurse -Force
            Write-Host "Files copied successfully to: $NewPath" -ForegroundColor Green
            
            Write-Host ""
            Write-Host "Next steps:" -ForegroundColor Yellow
            Write-Host "1. Navigate to: $NewPath" -ForegroundColor White
            Write-Host "2. Run: pm2 delete whatsapp-service" -ForegroundColor White
            Write-Host "3. Run: pm2 start ecosystem.config.js" -ForegroundColor White
            
        } catch {
            Write-Host "Error moving files: $($_.Exception.Message)" -ForegroundColor Red
        }
    }
} else {
    Write-Host "Path unchanged. Service should continue working normally." -ForegroundColor Green
}

Write-Host ""
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
