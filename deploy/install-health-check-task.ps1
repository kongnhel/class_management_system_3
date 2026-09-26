# ============================================================
# NMU Health Check - Installer (Windows Scheduled Task)
# ============================================================
# Registers "NMU-Health-Check":
#   - 5 minutes after every boot (so a restart verifies itself)
#   - daily at 06:00 (morning heartbeat before classes)
#
#   Install:   powershell -ExecutionPolicy Bypass -File .\deploy\install-health-check-task.ps1
#   Uninstall: powershell -ExecutionPolicy Bypass -File .\deploy\install-health-check-task.ps1 -Remove
# ============================================================

param([switch]$Remove)

$taskName = "NMU-Health-Check"
$siteRoot = Split-Path -Parent $PSScriptRoot
$script = Join-Path $PSScriptRoot "health-check.ps1"

# ---- require Administrator ---------------------------------------------
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "This script must run as Administrator." -ForegroundColor Red
    Write-Host "Right-click PowerShell -> Run as Administrator, then run it again." -ForegroundColor Red
    exit 1
}

if ($Remove) {
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue
    Write-Host "Removed." -ForegroundColor Green
    exit 0
}

if (-not (Test-Path $script)) { Write-Host "health-check.ps1 not found." -ForegroundColor Red; exit 1 }

Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue

$action = New-ScheduledTaskAction -Execute "powershell.exe" `
    -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$script`"" `
    -WorkingDirectory $siteRoot

# Trigger 1: at startup, delayed 5 minutes (let everything boot first)
$bootTrigger = New-ScheduledTaskTrigger -AtStartup
$bootTrigger.Delay = "PT5M"

# Trigger 2: daily morning heartbeat
$dailyTrigger = New-ScheduledTaskTrigger -Daily -At 06:00

$settings = New-ScheduledTaskSettingsSet -ExecutionTimeLimit (New-TimeSpan -Minutes 30)

Register-ScheduledTask -TaskName $taskName -Action $action `
    -Trigger @($bootTrigger, $dailyTrigger) -Settings $settings `
    -User "SYSTEM" -RunLevel Highest | Out-Null

# verify the task actually got created (Register-ScheduledTask can fail silently)
if (-not (Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: task '$taskName' was NOT created - registration failed. Check output above." -ForegroundColor Red
    exit 1
}

Write-Host "Task '$taskName' installed: 5 min after every boot + daily at 06:00." -ForegroundColor Green
Write-Host "Test it now:"
Write-Host "  powershell -NoProfile -ExecutionPolicy Bypass -File .\deploy\health-check.ps1"
