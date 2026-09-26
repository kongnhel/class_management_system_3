# ============================================================
# NMU Nightly Database Backup
# ============================================================
# Dumps the MySQL database to a zipped file in storage\backups,
# keeps 30 days locally, copies to your cloud-synced folder,
# and sends a Telegram success/failure message.
#
# Setup (in the server's .env - see deploy\note.txt section 4):
#   BACKUP_CLOUD_DIR=C:\Users\Administrator\Google Drive\NMU-Backups
#   TELEGRAM_ALERT_CHAT_ID=123456789        (get it from @userinfobot)
#
# Normally runs automatically via the "NMU-Backup" task created
# by deploy\install-backup-task.ps1 - but you can run by hand:
#   powershell -ExecutionPolicy Bypass -File .\deploy\backup.ps1
# ============================================================

$ErrorActionPreference = "Continue"
Set-Location (Split-Path -Parent $PSScriptRoot)

function Get-EnvValue($key) {
    $line = Select-String -Path ".env" -Pattern ("^" + $key + "=(.*)$") -ErrorAction SilentlyContinue | Select-Object -First 1
    if ($line) { return $line.Matches[0].Groups[1].Value.Trim() }
    return $null
}

function Send-Telegram($token, $chatId, $text) {
    if (-not $token -or -not $chatId) { return $false }
    try {
        Invoke-RestMethod -Uri "https://api.telegram.org/bot$token/sendMessage" `
            -Method Post -Body @{ chat_id = $chatId; text = $text } -TimeoutSec 15 | Out-Null
        return $true
    } catch { return $false }
}

$dbHost = Get-EnvValue "DB_HOST";        if (-not $dbHost) { $dbHost = "127.0.0.1" }
$dbPort = Get-EnvValue "DB_PORT";        if (-not $dbPort) { $dbPort = "3306" }
$dbName = Get-EnvValue "DB_DATABASE"
$dbUser = Get-EnvValue "DB_USERNAME"
$dbPass = Get-EnvValue "DB_PASSWORD"
$cloudDir = Get-EnvValue "BACKUP_CLOUD_DIR"
$botToken = Get-EnvValue "TELEGRAM_BOT_TOKEN"
$chatId   = Get-EnvValue "TELEGRAM_ALERT_CHAT_ID"

$logDir = Join-Path (Get-Location) "storage\logs"
if (-not (Test-Path $logDir)) { New-Item -ItemType Directory -Path $logDir | Out-Null }
$log = Join-Path $logDir "backup.log"
$stamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
Add-Content $log "[$stamp] backup starting"

# ---- find mysqldump.exe ---------------------------------------------
$conn = Get-NetTCPConnection -LocalPort $dbPort -State Listen -ErrorAction SilentlyContinue | Select-Object -First 1
$dumpCandidates = @()
if ($conn) {
    $p = Get-Process -Id $conn.OwningProcess -ErrorAction SilentlyContinue
    if ($p -and $p.Path) { $dumpCandidates += (Join-Path (Split-Path $p.Path) "mysqldump.exe") }
}
$dumpCandidates += @(
    "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe",
    "C:\Program Files\MySQL\MySQL Server 8.4\bin\mysqldump.exe"
)
$mysqldump = $dumpCandidates | Where-Object { $_ -and (Test-Path $_) } | Select-Object -First 1
if (-not $mysqldump) {
    $cmd = Get-Command mysqldump -ErrorAction SilentlyContinue
    if ($cmd) { $mysqldump = $cmd.Source }
}
if (-not $mysqldump) {
    Add-Content $log "[$stamp] FAILED: mysqldump.exe not found"
    Send-Telegram $botToken $chatId "[FAIL] NMU Backup FAILED: mysqldump.exe not found on the server" | Out-Null
    Write-Host "mysqldump.exe not found" -ForegroundColor Red
    exit 1
}

# ---- dump -------------------------------------------------------------
$backupDir = Join-Path (Get-Location) "storage\backups"
if (-not (Test-Path $backupDir)) { New-Item -ItemType Directory -Path $backupDir | Out-Null }

$date = Get-Date -Format "yyyyMMdd-HHmm"
$tmpSql = Join-Path $backupDir "nmu-cms-$date.sql"
$zipFile = Join-Path $backupDir "nmu-cms-$date.zip"

$dumpArgs = @(
    "--single-transaction", "--routines", "--events", "--triggers",
    "--default-character-set=utf8mb4",
    "--host=$dbHost", "--port=$dbPort",
    "--user=$dbUser"
)
# Password goes via environment variable (MYSQL_PWD), not the command
# line - keeps it out of the process list / Task Manager / WMI queries.
if ($dbPass) { $env:MYSQL_PWD = $dbPass }
$dumpArgs += @($dbName, "--result-file=$tmpSql")

& $mysqldump @dumpArgs 2>> $log
Remove-Item Env:\MYSQL_PWD -ErrorAction SilentlyContinue
$dumpOk = ($LASTEXITCODE -eq 0) -and (Test-Path $tmpSql) -and ((Get-Item $tmpSql).Length -gt 0)

if (-not $dumpOk) {
    $msg = "[FAIL] NMU Backup FAILED - mysqldump exited with code $LASTEXITCODE. Check storage\logs\backup.log"
    Add-Content $log "[$stamp] FAILED (exit $LASTEXITCODE)"
    Send-Telegram $botToken $chatId $msg | Out-Null
    exit 1
}

Compress-Archive -Path $tmpSql -DestinationPath $zipFile -Force
Remove-Item $tmpSql -Force

# ---- retention: keep 30 days -------------------------------------------
Get-ChildItem $backupDir -Filter "nmu-cms-*.zip" |
    Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-30) } |
    Remove-Item -Force -ErrorAction SilentlyContinue

# ---- second copy: cloud-synced folder ----------------------------------
$cloudOk = $true
if ($cloudDir) {
    if (Test-Path $cloudDir) {
        Copy-Item $zipFile -Destination $cloudDir -Force
        # keep the cloud folder trimmed too (30 days)
        Get-ChildItem $cloudDir -Filter "nmu-cms-*.zip" |
            Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-30) } |
            Remove-Item -Force -ErrorAction SilentlyContinue
    } else {
        $cloudOk = $false
        Add-Content $log "[$stamp] WARNING: BACKUP_CLOUD_DIR does not exist: $cloudDir"
    }
} else {
    $cloudOk = $false
    Add-Content $log "[$stamp] WARNING: BACKUP_CLOUD_DIR not set in .env - no second copy made"
}

# ---- report -------------------------------------------------------------
$sizeMB = [Math]::Round((Get-Item $zipFile).Length / 1MB, 2)
$totalBackups = (Get-ChildItem $backupDir -Filter "nmu-cms-*.zip").Count

$cloudNote = if ($cloudOk) { "cloud copy OK" } else { "NO cloud copy (see .env BACKUP_CLOUD_DIR)" }
$msg = "[OK] NMU Backup OK`nFile: nmu-cms-$date.zip ($sizeMB MB)`nTotal: $totalBackups backups kept (30 days)`nCloud: $cloudNote"
Add-Content $log "[$stamp] OK $zipFile ($sizeMB MB), $cloudNote"
Send-Telegram $botToken $chatId $msg | Out-Null

Write-Host "Backup complete: $zipFile ($sizeMB MB) - $cloudNote"
exit 0
