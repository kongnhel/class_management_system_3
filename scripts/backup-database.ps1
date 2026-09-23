$ErrorActionPreference = 'Stop'
Set-StrictMode -Version Latest

$projectRoot = Split-Path -Parent $PSScriptRoot
$envFile = Join-Path $projectRoot '.env'
$backupDirectory = Join-Path $projectRoot 'storage/app/backups'
$mysqldump = 'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysqldump.exe'
$retentionDays = 7

if (-not (Test-Path -LiteralPath $envFile)) {
    throw "The .env file was not found: $envFile"
}
if (-not (Test-Path -LiteralPath $mysqldump)) {
    throw "mysqldump was not found: $mysqldump"
}

$settings = @{}
foreach ($line in Get-Content -LiteralPath $envFile) {
    if ($line -match '^\s*([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)\s*$') {
        $key = $Matches[1]
        $value = $Matches[2].Trim()
        if ($value.Length -ge 2 -and $value.StartsWith('"') -and $value.EndsWith('"')) {
            $value = $value.Substring(1, $value.Length - 2)
        }
        $settings[$key] = $value
    }
}

foreach ($required in @('DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD')) {
    if (-not $settings.ContainsKey($required)) {
        throw "Missing $required in .env"
    }
}

$hostName = if ($settings.ContainsKey('DB_HOST')) { $settings['DB_HOST'] } else { '127.0.0.1' }
$port = if ($settings.ContainsKey('DB_PORT')) { $settings['DB_PORT'] } else { '3306' }
$timestamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$backupFile = Join-Path $backupDirectory "class_management_system_$timestamp.sql"
$tempName = 'mysql-backup-' + ([guid]::NewGuid().ToString('N')) + '.cnf'
$configFile = Join-Path ([System.IO.Path]::GetTempPath()) $tempName

New-Item -ItemType Directory -Path $backupDirectory -Force | Out-Null

try {
    @(
        '[client]'
        "host=$hostName"
        "port=$port"
        "user=$($settings['DB_USERNAME'])"
        "password=$($settings['DB_PASSWORD'])"
    ) | Set-Content -LiteralPath $configFile -Encoding ascii

    $arguments = @(
        "--defaults-extra-file=$configFile"
        '--single-transaction'
        '--routines'
        '--events'
        '--triggers'
        '--hex-blob'
        "--result-file=$backupFile"
        $settings['DB_DATABASE']
    )
    & $mysqldump @arguments

    if ($LASTEXITCODE -ne 0 -or -not (Test-Path -LiteralPath $backupFile)) {
        throw "mysqldump failed with exit code $LASTEXITCODE"
    }

    Get-ChildItem -LiteralPath $backupDirectory -Filter '*.sql' -File |
        Where-Object { $_.LastWriteTime -lt (Get-Date).AddDays(-$retentionDays) } |
        Remove-Item -Force

    Write-Output "Database backup created: $backupFile"
}
finally {
    if (Test-Path -LiteralPath $configFile) {
        Remove-Item -LiteralPath $configFile -Force -ErrorAction SilentlyContinue
    }
}
