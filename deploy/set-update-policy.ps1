# ============================================================
# NMU Windows Update Policy - "never surprise us"
# ============================================================
# Sets Windows Update so it can NEVER auto-restart the server:
#   - Active hours 07:00-23:00 (no restarts during school time)
#   - Updates download but wait for YOU to click install
#     (this is the PRIMARY protection - AUOptions=3 means
#      Windows never reaches the reboot step on its own)
#   - NoAutoRebootWithLoggedOnUsers as a secondary safety net
#     (note: this only protects while someone is interactively
#      logged on - on a headless server it does nothing, and if
#      anyone ever changes AUOptions to 4 = auto-install, this
#      flag won't help during unattended hours)
#
# NOTE: These are LOCAL registry settings. If this server is
# ever joined to Active Directory with a Windows Update GPO,
# that domain policy will silently override these values on
# the next gpupdate cycle.
#
#   Run: powershell -ExecutionPolicy Bypass -File .\deploy\set-update-policy.ps1
#
# After you install updates manually and reboot, the
# NMU-Health-Check task fires 5 minutes later and sends you a
# Telegram status confirming everything came back up.
# ============================================================

$activeHoursKey = "HKLM:\SOFTWARE\Microsoft\WindowsUpdate\UX\Settings"
$auKey = "HKLM:\SOFTWARE\Policies\Microsoft\Windows\WindowsUpdate\AU"

# ---- require Administrator ---------------------------------------------
$isAdmin = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if (-not $isAdmin) {
    Write-Host "This script must run as Administrator." -ForegroundColor Red
    Write-Host "Right-click PowerShell -> Run as Administrator, then run it again." -ForegroundColor Red
    exit 1
}

if (-not (Test-Path $activeHoursKey)) { New-Item -Path $activeHoursKey -Force | Out-Null }
if (-not (Test-Path $auKey)) { New-Item -Path $auKey -Force | Out-Null }

# Active hours: 07:00 to 23:00 (16-hour window is the maximum allowed)
Set-ItemProperty -Path $activeHoursKey -Name "ActiveHoursStart" -Value 7 -Type DWord
Set-ItemProperty -Path $activeHoursKey -Name "ActiveHoursEnd" -Value 23 -Type DWord
Set-ItemProperty -Path $activeHoursKey -Name "SmartActiveHoursState" -Value 0 -Type DWord

# AUOptions = 3: download updates automatically, but YOU choose when to install.
# THIS IS THE PRIMARY REBOOT PROTECTION - Windows can never reach the
# reboot step on its own while AUOptions is 3. If anyone changes it to 4
# (auto-install + auto-schedule), reboot protection is lost during
# unattended hours. Keep it at 3.
Set-ItemProperty -Path $auKey -Name "AUOptions" -Value 3 -Type DWord

# Secondary: never auto-restart while a user is interactively logged on.
# (Only helps when someone is actually at the console/RDP - on a headless
#  server this does nothing. The real protection is AUOptions=3 above.)
Set-ItemProperty -Path $auKey -Name "NoAutoRebootWithLoggedOnUsers" -Value 1 -Type DWord

# Restart the Windows Update service so these registry changes take effect
# immediately instead of waiting for the next policy refresh cycle
Restart-Service -Name wuauserv -Force -ErrorAction SilentlyContinue

Write-Host "Windows Update policy set (wuauserv restarted to apply immediately):" -ForegroundColor Green
Write-Host "  - Active hours 07:00-23:00 (no restarts during school time)" -ForegroundColor Green
Write-Host "  - Updates download automatically, install only when YOU click" -ForegroundColor Green
Write-Host "  - Never auto-restarts while someone is logged on (headless-server caveat: this is secondary)" -ForegroundColor Green
Write-Host ""
Write-Host "Recommended monthly routine:" -ForegroundColor Cyan
Write-Host "  1. Pick a school holiday / weekend" -ForegroundColor Cyan
Write-Host "  2. Check Windows Update, click Install" -ForegroundColor Cyan
Write-Host "  3. Reboot when it asks" -ForegroundColor Cyan
Write-Host "  4. Watch Telegram - health-check pings you 5 min later" -ForegroundColor Cyan
