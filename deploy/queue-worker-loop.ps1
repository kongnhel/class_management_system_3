# ============================================================
# NMU Queue Worker Loop
# ============================================================
# Runs php artisan queue:work forever. If the worker exits
# (crash, or a deploy ran "queue:restart"), it restarts in 5s.
#
# This file is launched automatically by the scheduled task
# created by deploy\install-queue-worker.ps1 - do not run it
# by hand (it never exits on its own).
# ============================================================

$siteRoot = Split-Path -Parent $PSScriptRoot
Set-Location $siteRoot

$log = Join-Path $siteRoot "storage\logs\queue-worker.log"

while ($true) {
    $stamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $log -Value "[$stamp] queue worker starting"

    php artisan queue:work --tries=3 --backoff=30 --timeout=60 --sleep=1 *>> $log

    $stamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $log -Value "[$stamp] queue worker exited (code $LASTEXITCODE) - restarting in 5s"
    Start-Sleep -Seconds 5
}
