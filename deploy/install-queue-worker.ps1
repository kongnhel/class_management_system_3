# ============================================================
# NMU Queue Worker - Installer (Windows Scheduled Task)
# ============================================================
# Run this ON THE WINDOWS SERVER, as Administrator:
#
#   Install (first time):
#     powershell -ExecutionPolicy Bypass -File .\deploy\install-queue-worker.ps1
#
#   Uninstall:
#     powershell -ExecutionPolicy Bypass -File .\deploy\install-queue-worker.ps1 -Remove
#
# What it does:
#   - Registers a scheduled task "NMU-Queue-Worker" that runs at
#     system startup (as SYSTEM) and keeps a queue:work loop alive
#   - Starts it immediately after install
#   - The worker picks up new code automatically when you run
#     "php artisan queue:restart" during deploys
# ============================================================

param(
    [switch]$Remove
)

$taskName = "NMU-Queue-Worker"

if ($Remove) {
    Write-Host "Removing scheduled task '$taskName'..." -ForegroundColor Cyan
    Stop-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue
    Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue
    Write-Host "Removed." -ForegroundColor Green
    exit 0
}

$siteRoot = Split-Path -Parent $PSScriptRoot
$loopScript = Join-Path $PSScriptRoot "queue-worker-loop.ps1"

if (-not (Test-Path $loopScript)) {
    Write-Host "queue-worker-loop.ps1 not found next to this installer." -ForegroundColor Red
    exit 1
}

# Remove any previous version of the task first (safe to re-run)
Unregister-ScheduledTask -TaskName $taskName -Confirm:$false -ErrorAction SilentlyContinue

$action = New-ScheduledTaskAction `
    -Execute "powershell.exe" `
    -Argument "-NoProfile -ExecutionPolicy Bypass -File `"$loopScript`"" `
    -WorkingDirectory $siteRoot

$trigger = New-ScheduledTaskTrigger -AtStartup

# -ExecutionTimeLimit Zero = the task may run forever (default kills it after 72h!)
# -RestartCount/Interval   = if the loop itself crashes, Windows restarts it
$settings = New-ScheduledTaskSettingsSet `
    -ExecutionTimeLimit ([TimeSpan]::Zero) `
    -RestartCount 999 `
    -RestartInterval (New-TimeSpan -Minutes 1) `
    -DisallowHardTerminate

Register-ScheduledTask `
    -TaskName $taskName `
    -Action $action `
    -Trigger $trigger `
    -Settings $settings `
    -User "SYSTEM" `
    -RunLevel Highest | Out-Null

Start-ScheduledTask -TaskName $taskName

Write-Host ""
Write-Host "Installed and started." -ForegroundColor Green
Write-Host ""
Write-Host "Check it is alive:"
Write-Host "  Get-ScheduledTask -TaskName '$taskName' | Select-Object State"
Write-Host "  (State should be Running)"
Write-Host ""
Write-Host "Watch the worker log:"
Write-Host "  Get-Content '$siteRoot\storage\logs\queue-worker.log' -Tail 20 -Wait"
Write-Host ""
Write-Host "Jobs waiting right now:"
php artisan tinker --execute="echo 'jobs pending: ' . DB::table('jobs')->count() . ' | failed jobs: ' . DB::table('failed_jobs')->count() . PHP_EOL;"
