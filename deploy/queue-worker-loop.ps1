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
if (-not (Test-Path (Split-Path $log))) { New-Item -ItemType Directory -Path (Split-Path $log) | Out-Null }

# Log rotation: if the file exceeds 10MB, keep it as .old and start fresh.
# Max disk usage: ~20MB (current + one .old). The .old replaces any previous .old.
$maxLogBytes = 10MB

while ($true) {
    # rotate before starting a new cycle
    if ((Test-Path $log) -and ((Get-Item $log).Length -gt $maxLogBytes)) {
        Remove-Item "$log.old" -Force -ErrorAction SilentlyContinue
        Move-Item $log "$log.old" -Force
        Add-Content -Path $log -Value "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] log rotated (previous size: $([Math]::Round((Get-Item "$log.old").Length / 1MB, 1)) MB)"
    }

    $stamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $log -Value "[$stamp] queue worker starting"

    php artisan queue:work --tries=3 --backoff=30 --timeout=60 --sleep=1 *>> $log

    $stamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
    Add-Content -Path $log -Value "[$stamp] queue worker exited (code $LASTEXITCODE) - restarting in 5s"
    Start-Sleep -Seconds 5
}
