# PowerShell script to create Windows Task Scheduler entry
# Run this script as Administrator

$taskName = "WhatsApp Service Startup"
$servicePath = "d:\laragon\www\absensi\whatsapp-service"
$scriptPath = "$servicePath\start-service.ps1"

try {
    Write-Host "Creating Windows Task Scheduler entry for WhatsApp Service..."

    # Check if script exists
    if (-not (Test-Path $scriptPath)) {
        Write-Host "ERROR: Script not found at $scriptPath"
        exit 1
    }

    # Remove existing task if it exists
    $existingTask = Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
    if ($existingTask) {
        Write-Host "Removing existing task..."
        Unregister-ScheduledTask -TaskName $taskName -Confirm:$false
    }

    # Create action
    $action = New-ScheduledTaskAction -Execute "powershell.exe" -Argument "-ExecutionPolicy Bypass -WindowStyle Hidden -File `"$scriptPath`""

    # Create trigger (at startup)
    $trigger = New-ScheduledTaskTrigger -AtStartup

    # Create settings
    $settings = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable -DontStopOnIdleEnd

    # Create principal (run with highest privileges)
    $principal = New-ScheduledTaskPrincipal -UserId "SYSTEM" -LogonType ServiceAccount -RunLevel Highest

    # Register the task
    Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger -Settings $settings -Principal $principal -Description "Auto-start WhatsApp service using PM2"

    Write-Host "Task Scheduler entry created successfully!"
    Write-Host ""
    Write-Host "Task Details:"
    Write-Host "- Name: $taskName"
    Write-Host "- Trigger: At system startup"
    Write-Host "- Action: Run PowerShell script"
    Write-Host "- Script: $scriptPath"
    Write-Host ""
    Write-Host "You can view/modify this task in Task Scheduler (taskschd.msc)"

    # Test the task
    Write-Host ""
    $testRun = Read-Host "Do you want to test run the task now? (y/n)"
    if ($testRun -eq "y" -or $testRun -eq "Y") {
        Write-Host "Starting task..."
        Start-ScheduledTask -TaskName $taskName
        Write-Host "Task started. Check the service status with: pm2 status"
    }

} catch {
    Write-Host "ERROR: $($_.Exception.Message)"
    Write-Host "Make sure you're running this script as Administrator"
    exit 1
}
