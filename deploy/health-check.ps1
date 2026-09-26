# ============================================================
# NMU Server Health Check
# ============================================================
# Verifies everything that matters after a reboot or at any time:
#   1. IIS app pool "nmu-cms" is started
#   2. MySQL (port 3306) is running
#   3. Queue worker task is running
#   4. Free disk space on C: (warns under 10 GB)
#   5. The site answers HTTP
#   6. The database answers a query
# Then sends ONE Telegram message with the status.
#
# Runs automatically: 5 minutes after every boot + daily at 06:00
# (task "NMU-Health-Check" from install-health-check-task.ps1).
# Run by hand any time:
#   powershell -ExecutionPolicy Bypass -File .\deploy\health-check.ps1
# ============================================================

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

$appcmd = "C:\Windows\System32\inetsrv\appcmd.exe"
$botToken = Get-EnvValue "TELEGRAM_BOT_TOKEN"
$chatId   = Get-EnvValue "TELEGRAM_ALERT_CHAT_ID"
$appUrl   = Get-EnvValue "APP_URL"
if (-not $appUrl) { $appUrl = "http://127.0.0.1" }

$results = @()
$allOk = $true

# ---- 1. IIS app pool ----------------------------------------------------
$poolOk = $false
if (Test-Path $appcmd) {
    $pool = & $appcmd list apppool /apppool.name:"nmu-cms" 2>$null
    if ("$pool" -match "state:Started") { $poolOk = $true }
    elseif ("$pool" -match "state:(\w+)") { $results += "[FAIL] App pool nmu-cms state: $($Matches[1])"; $allOk = $false }
    else { $results += "[FAIL] App pool nmu-cms not found"; $allOk = $false }
} else {
    $results += "[FAIL] IIS (appcmd) not found"; $allOk = $false
}
if ($poolOk) { $results += "[OK] App pool nmu-cms: Started" }

# ---- 2. MySQL ------------------------------------------------------------
$conn = Get-NetTCPConnection -LocalPort 3306 -State Listen -ErrorAction SilentlyContinue | Select-Object -First 1
if ($conn) {
    $svc = Get-CimInstance Win32_Service -ErrorAction SilentlyContinue | Where-Object { $_.ProcessId -eq $conn.OwningProcess } | Select-Object -First 1
    $results += if ($svc) { "[OK] MySQL running (service: $($svc.Name))" } else { "[OK] MySQL running (process)" }
} else {
    $results += "[FAIL] MySQL NOT running - nothing on port 3306"
    $allOk = $false
}

# ---- 3. Queue worker --------------------------------------------------------
try {
    $t = Get-ScheduledTask -TaskName "NMU-Queue-Worker" -ErrorAction Stop
    if ($t.State -eq "Running") { $results += "[OK] Queue worker: Running" }
    else { $results += "[FAIL] Queue worker task state: $($t.State)"; $allOk = $false }
} catch {
    $results += "[FAIL] Queue worker task not found (install-queue-worker.ps1 not run?)"; $allOk = $false
}

# ---- 4. Disk space ------------------------------------------------------------
$drive = Get-PSDrive C -ErrorAction SilentlyContinue
if ($drive) {
    $freeGB = [Math]::Round($drive.Free / 1GB, 1)
    if ($freeGB -lt 10) { $results += "[WARN] DISK LOW: only $freeGB GB free on C: (under 10 GB)"; $allOk = $false }
    else { $results += "[OK] Disk C: $freeGB GB free" }
} else {
    $results += "[FAIL] Disk check could not read C: drive"; $allOk = $false
}

# ---- 5. Site answers HTTP -----------------------------------------------------
$httpOk = $false
try {
    $resp = Invoke-WebRequest -Uri "$appUrl/login" -UseBasicParsing -TimeoutSec 15
    if ($resp.StatusCode -ge 200 -and $resp.StatusCode -lt 400) { $httpOk = $true }
    else { $results += "[FAIL] Site returned HTTP $($resp.StatusCode)"; $allOk = $false }
} catch {
    $results += "[FAIL] Site not answering at $appUrl ($($_.Exception.Message))"; $allOk = $false
}
if ($httpOk) { $results += "[OK] Site answering: $appUrl" }

# ---- 6. Database answers a query (30s kill-proof timeout - a wedged MySQL
#         must not hang the whole health check before it can report) ------
$siteRoot = (Get-Location).Path
$tmpOut = Join-Path $env:TEMP "nmu-hc-dbcheck.txt"
$proc = Start-Process -FilePath "php" `
    -ArgumentList @("artisan", "tinker", "--execute=var_export(DB::table('users')->count());") `
    -WorkingDirectory $siteRoot -WindowStyle Hidden `
    -RedirectStandardOutput $tmpOut -PassThru

if ($proc.WaitForExit(30000)) {
    $userCount = (Get-Content $tmpOut -Raw -ErrorAction SilentlyContinue)
    $userCount = "$userCount".Trim()
    if ("$userCount" -match "^\d+$") {
        $results += "[OK] Database OK ($userCount users)"
    } else {
        $results += "[FAIL] Database query failed"; $allOk = $false
    }
} else {
    $proc.Kill()
    $results += "[FAIL] Database check TIMED OUT (30s) - server may be wedged"; $allOk = $false
}
Remove-Item $tmpOut -Force -ErrorAction SilentlyContinue

# ---- send the report -----------------------------------------------------------
$stamp = Get-Date -Format "yyyy-MM-dd HH:mm"
$header = if ($allOk) { "[OK] NMU Health Check OK - $stamp" } else { "[FAIL] NMU Health Check PROBLEM - $stamp" }
$message = $header + "`n" + ($results -join "`n")

$hcLogDir = Join-Path (Get-Location) "storage\logs"
if (-not (Test-Path $hcLogDir)) { New-Item -ItemType Directory -Path $hcLogDir | Out-Null }

Add-Content -Path (Join-Path $hcLogDir "health-check.log") -Value "[$stamp]`n$message`n"

if (-not (Send-Telegram $botToken $chatId $message)) {
    Write-Host "Telegram not sent (TELEGRAM_ALERT_CHAT_ID not set in .env) - status:" -ForegroundColor Yellow
}
Write-Host $message

if ($allOk) { exit 0 } else { exit 1 }
