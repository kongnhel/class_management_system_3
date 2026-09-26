# ============================================================
# NMU Backup - Installer (Windows Scheduled Task)
# ============================================================
# Registers the "NMU-Backup" task: runs deploy\backup.ps1 every
# night at 02:30. If the server was off at that time, it runs
# as soon as the machine is back (StartWhenAvailable).
#
#   Install:   powershell -ExecutionPolicy Bypass -File .\deploy\install-backup-task.ps1
#   Uninstall: powershell -ExecutionPolicy Bypass -File .\deploy\install-backup-task.ps1 -Remove
# ============================================================

param([switch]$Remove)

$taskName = "NMU-Backup"
$siteRoot = Split-Path -Parent $PSScriptRoot
$script = Join-Path $PSScriptRoot "backup.ps1"

# ---- require Administrator ---------------------------------------------
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "This script must run as Administrator." -ForegroundColor Red
    Write-Host "Right-click PowerShell -> Run as Administrator, then run it again." -ForegroundColor Red
    exit 1
}

if ($Remove) {
    Write-Host "Removing task '$taskName'..." -ForegroundColor Cyan
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue
    Write-Host "Removed." -ForegroundColor Green
    exit 0
}

if (-not (Test-Path $script)) { Write-Host "backup.ps1 not found." -ForegroundColor Red; exit 1 }

Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue

$action = New-ScheduledTaskAction -Execute "powershell.exe" `
    -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$script`"" `
    -WorkingDirectory $siteRoot

$trigger = New-ScheduledTaskTrigger -Daily -At 02:30

$settings = New-ScheduledTaskSettingsSet `
    -ExecutionTimeLimit (New-TimeSpan -Hours 2) `
    -StartWhenAvailable

Register-ScheduledTask -TaskName $taskName -Action $action -Trigger $trigger `
    -Settings $settings -User "SYSTEM" -RunLevel Highest | Out-Null

# verify the task actually got created (Register-ScheduledTask can fail silently)
if (-not (Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: task '$taskName' was NOT created - registration failed. Check output above." -ForegroundColor Red
    exit 1
}

Write-Host "Task '$taskName' installed: nightly 02:30 + catch-up if the server was off." -ForegroundColor Green

# Config sanity check
foreach ($key in @("BACKUP_CLOUD_DIR", "TELEGRAM_ALERT_CHAT_ID")) {
    $line = Select-String -Path (Join-Path $siteRoot ".env") -Pattern ("^" + $key + "=(.*)$") -ErrorAction SilentlyContinue | Select-Object -First 1
    $val = if ($line) { $line.Matches[0].Groups[1].Value.Trim() } else { "" }
    if (-not $val) {
        Write-Host "WARNING: $key is not set in .env - see deploy\note.txt section 4" -ForegroundColor Yellow
    } else {
        Write-Host "OK: $key = $val" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Test it now (runs one backup immediately):"
Write-Host "  powershell -NoProfile -ExecutionPolicy Bypass -File .\deploy\backup.ps1"
