# ============================================================
# IIS Performance Tuning Script - NMU Class Management System
# ============================================================
# Run this ON THE WINDOWS SERVER (the one hosting the IIS site).
#
# HOW TO RUN (PowerShell, as Administrator):
#   cd C:\path\to\your\site
#   powershell -ExecutionPolicy Bypass -File .\deploy\iis-tune.ps1
#
# WHAT IT DOES:
#   1. Counts your CPU cores and sizes the PHP worker pool (2 workers per core, min 8)
#   2. Finds the PHP (php-cgi.exe) that IIS is using
#   3. FastCGI tuning: more workers, workers not recycled every 200 requests,
#      no auto-shutdown on temporary PHP errors
#   4. App pool "nmu-cms": never idle-sleeps, auto-starts, restarts at 3:00 AM only
#   5. Recycles the app pool and shows a quick verification
# ============================================================

$appcmd = "C:\Windows\System32\inetsrv\appcmd.exe"

if (-not (Test-Path $appcmd)) {
    Write-Host "IIS (appcmd.exe) not found on this machine." -ForegroundColor Red
    Write-Host "This script must run on the Windows Server that hosts the site, not on your dev PC." -ForegroundColor Red
    exit 1
}

# ---- Step 1: CPU count -> worker count -------------------------------
$cores = $env:NUMBER_OF_PROCESSORS
$maxInstances = [Math]::Max(8, [int]$cores * 2)
Write-Host ""
Write-Host "CPU cores detected: $cores  ->  PHP workers to configure: $maxInstances" -ForegroundColor Cyan

# ---- Step 2: find the PHP that IIS uses ------------------------------
$raw = & $appcmd list config -section:system.webServer/fastCgi 2>&1
$phpPaths = @()
foreach ($line in @($raw)) {
    if ("$line" -match 'fullPath="([^"]+)"') { $phpPaths += $Matches[1] }
}
$phpPaths = $phpPaths | Select-Object -Unique

if ($phpPaths.Count -eq 0) {
    Write-Host "No PHP FastCGI registration found in IIS." -ForegroundColor Red
    Write-Host "The site may be running PHP another way. Check IIS Manager > Handler Mappings." -ForegroundColor Red
    exit 1
}

# ---- Step 3: FastCGI tuning ------------------------------------------
foreach ($php in $phpPaths) {
    Write-Host ""
    Write-Host "Tuning FastCGI for: $php" -ForegroundColor Cyan

    & $appcmd set config -section:system.webServer/fastCgi "/[fullPath='$php'].maxInstances:$maxInstances"
    & $appcmd set config -section:system.webServer/fastCgi "/[fullPath='$php'].instanceMaxRequests:10000"
    & $appcmd set config -section:system.webServer/fastCgi "/[fullPath='$php'].activityTimeout:300"
    & $appcmd set config -section:system.webServer/fastCgi "/[fullPath='$php'].requestTimeout:300"
    & $appcmd set config -section:system.webServer/fastCgi "/[fullPath='$php'].rapidFailSupport:False"
}

# ---- Step 4: App pool "nmu-cms" --------------------------------------
Write-Host ""
Write-Host "Tuning application pool: nmu-cms" -ForegroundColor Cyan

& $appcmd set apppool /apppool.name:"nmu-cms" /processModel.idleTimeout:00:00:00
& $appcmd set apppool /apppool.name:"nmu-cms" /startMode:AlwaysRunning
& $appcmd set apppool /apppool.name:"nmu-cms" /recycling.periodicRestart.time:00:00:00

# Restart only at 3:00 AM (remove any old schedule, add the 3 AM one)
& $appcmd set apppool /apppool.name:"nmu-cms" /recycling.periodicRestart.schedule: 2>$null
& $appcmd set apppool /apppool.name:"nmu-cms" /+recycling.periodicRestart.schedule.[value='03:00:00']

# ---- Step 5: recycle + verify -----------------------------------------
Write-Host ""
Write-Host "Recycling app pool so all settings take effect..." -ForegroundColor Cyan
& $appcmd recycle apppool /apppool.name:"nmu-cms"

Write-Host ""
Write-Host "ALL DONE." -ForegroundColor Green
Write-Host "To verify: browse the site for a minute, then run:" -ForegroundColor Green
Write-Host "  Get-Process php-cgi | Measure-Object | Select-Object Count"
Write-Host "The number should climb toward $maxInstances under load." -ForegroundColor Green
