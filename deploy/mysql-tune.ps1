# ============================================================
# NMU MySQL Tuning Script
# ============================================================
# Run this ON THE WINDOWS SERVER (the machine hosting the site's
# MySQL), as Administrator:
#
#   powershell -ExecutionPolicy Bypass -File .\deploy\mysql-tune.ps1
#
# WHAT IT DOES:
#   1. Finds the MySQL that actually serves the site (port 3306)
#   2. Sizes InnoDB buffer pool to ~40% of server RAM (minimum 2GB)
#   3. Appends a clearly-marked tuning block to my.ini (backs up first)
#      - innodb_buffer_pool_size  : hot data cached in RAM (default 128M is tiny)
#      - max_connections=300      : enough for 16 PHP workers + queue + tools
#      - max_allowed_packet=64M   : safe for big Excel grade imports
#      - innodb_flush_log_at_trx_commit=2 : much faster writes, at most 1s of
#        transactions lost on a hard crash (fine for attendance/grades)
#      - slow_query_log=ON (>1s) : so we can find slow queries later
#   4. Restarts MySQL (about 10 seconds of downtime - run outside class hours)
#   5. Verifies the live values via the app's own DB connection
#
# SAFE TO RE-RUN: an old tuning block is removed before the new one
# is appended, and my.ini is backed up next to the original.
# ============================================================

param([switch]$Yes)

$marker = "# ==== NMU CMS MySQL tuning (deploy\mysql-tune.ps1) ===="

# ---- require Administrator ---------------------------------------------
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "This script must run as Administrator." -ForegroundColor Red
    Write-Host "Right-click PowerShell -> Run as Administrator, then run it again." -ForegroundColor Red
    exit 1
}

# ---- Step 1: find the MySQL that actually serves the site -----------
$dbPort = 3306
$portLine = Select-String -Path ".env" -Pattern "^DB_PORT=(.*)$" -ErrorAction SilentlyContinue | Select-Object -First 1
if ($portLine -and $portLine.Matches[0].Groups[1].Value.Trim()) { $dbPort = $portLine.Matches[0].Groups[1].Value.Trim() }

$conn = Get-NetTCPConnection -LocalPort $dbPort -State Listen -ErrorAction SilentlyContinue | Select-Object -First 1
if (-not $conn) {
    Write-Host "Nothing is listening on port $dbPort. Is MySQL running?" -ForegroundColor Red
    exit 1
}
$proc = Get-Process -Id $conn.OwningProcess -ErrorAction SilentlyContinue
if (-not $proc) {
    Write-Host "Could not identify the process owning port $dbPort." -ForegroundColor Red
    exit 1
}
Write-Host "MySQL on port ${dbPort}: $($proc.ProcessName) (PID $($proc.Id))" -ForegroundColor Cyan

$svc = Get-CimInstance Win32_Service | Where-Object { $_.ProcessId -eq $proc.Id } | Select-Object -First 1
$isService = ($null -ne $svc)
if ($isService) {
    Write-Host "Runs as Windows service: $($svc.Name)" -ForegroundColor Cyan
} else {
    Write-Host "MySQL runs as a plain process (e.g. Laragon). You will restart it manually at the end." -ForegroundColor Yellow
}

# ---- Step 2: locate my.ini ------------------------------------------
$iniCandidates = @()
if ($isService -and "$($svc.PathName)" -match '--defaults-file=("[^"]+"|\S+)') {
    $iniCandidates += $Matches[1].Trim('"')
}
if ($proc.Path) {
    $exeDir = Split-Path $proc.Path
    $iniCandidates += (Join-Path $exeDir "my.ini")
    $iniCandidates += (Join-Path $exeDir "..\my.ini")
}
$iniCandidates += @(
    "C:\ProgramData\MySQL\MySQL Server 8.0\my.ini",
    "C:\ProgramData\MySQL\MySQL Server 8.4\my.ini",
    "C:\ProgramData\MySQL\MySQL Server 9.0\my.ini"
)

$ini = $iniCandidates | Where-Object { $_ -and (Test-Path $_) } | Select-Object -First 1
if (-not $ini) {
    Write-Host "Could not find my.ini. Looked in:" -ForegroundColor Red
    $iniCandidates | ForEach-Object { Write-Host "  $_" }
    exit 1
}
Write-Host "Config file: $ini" -ForegroundColor Cyan

# ---- Step 3: size the buffer pool from RAM ---------------------------
$ramGB = [Math]::Round((Get-CimInstance Win32_ComputerSystem).TotalPhysicalMemory / 1GB, 0)
$poolGB = [Math]::Min(8, [Math]::Max(2, [Math]::Floor($ramGB * 0.4)))
Write-Host "Server RAM: $ramGB GB  ->  InnoDB buffer pool: $poolGB GB (40% of RAM, capped at 8GB)" -ForegroundColor Cyan

# ---- Confirm before touching anything --------------------------------
if (-not $Yes) {
    Write-Host ""
    Write-Host "This will BACK UP my.ini, append the tuning block, and RESTART MySQL (~10s downtime)." -ForegroundColor Yellow
    $answer = Read-Host "Continue? (y/n)"
    if ($answer -notin @("y", "Y", "yes")) { Write-Host "Cancelled."; exit 0 }
}

# ---- Step 4: backup + apply ------------------------------------------
$backup = "$ini.backup-$(Get-Date -Format yyyyMMdd-HHmmss)"
Copy-Item $ini $backup
Write-Host "Backup saved: $backup" -ForegroundColor Green

$content = Get-Content $ini -Raw
$idx = $content.IndexOf($marker)
if ($idx -ge 0) {
    $content = $content.Substring(0, $idx).TrimEnd() + "`r`n"
    Set-Content -Path $ini -Value $content -NoNewline
    Write-Host "Removed previous tuning block (re-applying fresh values)." -ForegroundColor Cyan
}

$block = @"

$marker
# Applied by deploy\mysql-tune.ps1 - delete this whole section to revert.
[mysqld]
innodb_buffer_pool_size=$($poolGB)G
max_connections=300
max_allowed_packet=64M
innodb_flush_log_at_trx_commit=2
slow_query_log=ON
long_query_time=1
"@
Add-Content -Path $ini -Value $block
Write-Host "Tuning block appended." -ForegroundColor Green

# ---- Step 5: restart MySQL -------------------------------------------
if ($isService) {
    Write-Host "Restarting MySQL service '$($svc.Name)'..." -ForegroundColor Cyan
    Restart-Service -Name $svc.Name -Force
    Start-Sleep -Seconds 3
    $status = (Get-Service -Name $svc.Name).Status
    Write-Host "Service status: $status" -ForegroundColor Cyan
    if ($status -ne "Running") {
        Write-Host ""
        Write-Host "ERROR: MySQL did not come back up (status: $status)." -ForegroundColor Red
        Write-Host "Check the MySQL error log for the cause. Your my.ini backup: $backup" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host ""
    Write-Host "MySQL is not a Windows service (Laragon-style)." -ForegroundColor Yellow
    Write-Host "RESTART IT YOURSELF NOW (e.g. Laragon: Stop All / Start All)." -ForegroundColor Yellow
    Write-Host "Press Enter after MySQL is back up..."
    Read-Host | Out-Null
}

# ---- Step 6: verify through the app's DB connection ------------------
Set-Location (Split-Path -Parent $PSScriptRoot)
$verify = @'
<?php
foreach (DB::select("SHOW VARIABLES WHERE Variable_name IN ('innodb_buffer_pool_size','max_connections','max_allowed_packet','innodb_flush_log_at_trx_commit','slow_query_log','long_query_time')") as $v) {
    echo $v->Variable_name . " = " . $v->Value . PHP_EOL;
}
'@
$tmp = Join-Path $env:TEMP "nmu-mysql-verify.php"
Set-Content -Path $tmp -Value $verify -Encoding UTF8
Write-Host ""
Write-Host "Live MySQL values now:" -ForegroundColor Green
php artisan tinker --execute="require '$tmp';"
Remove-Item $tmp -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "DONE. Expected: buffer_pool around $poolGB G, max_connections=300." -ForegroundColor Green
Write-Host "Slow queries (over 1 second) are now logged in MySQL's data directory." -ForegroundColor Green
