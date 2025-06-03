# PowerShell script to add WhatsApp Service to Windows Startup
# Run this script as Administrator

$servicePath = "d:\laragon\www\absensi\whatsapp-service"
$startupScript = "$servicePath\auto-startup.bat"
$shortcutName = "WhatsApp Service Startup.lnk"

# Get Windows Startup folder path
$startupFolder = [Environment]::GetFolderPath("Startup")
$shortcutPath = Join-Path $startupFolder $shortcutName

try {
    Write-Host "Adding WhatsApp Service to Windows Startup..."
    Write-Host "Startup folder: $startupFolder"

    # Check if startup script exists
    if (-not (Test-Path $startupScript)) {
        Write-Host "ERROR: Startup script not found at $startupScript"
        exit 1
    }

    # Remove existing shortcut if it exists
    if (Test-Path $shortcutPath) {
        Write-Host "Removing existing shortcut..."
        Remove-Item $shortcutPath -Force
    }

    # Create Windows Script Host object
    $WshShell = New-Object -ComObject WScript.Shell

    # Create shortcut
    $Shortcut = $WshShell.CreateShortcut($shortcutPath)
    $Shortcut.TargetPath = $startupScript
    $Shortcut.WorkingDirectory = $servicePath
    $Shortcut.Description = "Auto-start WhatsApp Service on Windows boot"
    $Shortcut.WindowStyle = 7  # Minimized window
    $Shortcut.Save()

    Write-Host "Shortcut created successfully at: $shortcutPath"
    Write-Host ""
    Write-Host "WhatsApp Service will now start automatically when Windows boots."
    Write-Host "The service will start minimized in the background."
    Write-Host ""
    Write-Host "To remove from startup:"
    Write-Host "1. Press Win+R, type 'shell:startup', press Enter"
    Write-Host "2. Delete the 'WhatsApp Service Startup.lnk' file"
    Write-Host ""

    # Test the shortcut
    $testRun = Read-Host "Do you want to test the shortcut now? (y/n)"
    if ($testRun -eq "y" -or $testRun -eq "Y") {
        Write-Host "Running startup script..."
        Start-Process -FilePath $startupScript -WindowStyle Minimized
    }

} catch {
    Write-Host "ERROR: $($_.Exception.Message)"
    exit 1
}
