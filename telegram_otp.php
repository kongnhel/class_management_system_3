<?php
/**
 * CLI bridge for MadelineProto OTP operations.
 * Called by TelegramClientService via proc_open.
 *
 * Usage:
 *   php telegram_otp.php send <phone>
 *   php telegram_otp.php verify <phone> <code>
 *
 * Writes result to a fixed temp file (tg_otp_result.json).
 */

require __DIR__ . '/vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;
use danog\MadelineProto\RPCErrorException;
use Illuminate\Support\Facades\Log;

function outputResult(bool $success, ?string $message = null, ?string $phoneCodeHash = null): void
{
    $result = ['success' => $success];
    if ($message !== null) $result['message'] = $message;
    if ($phoneCodeHash !== null) $result['phone_code_hash'] = $phoneCodeHash;

    // Write to a fixed temp file — the service reads this file after running the script.
    $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tg_otp_result.json';
    file_put_contents($tmpFile, json_encode($result, JSON_UNESCAPED_UNICODE));
}

function normalizePhone(string $phone): string
{
    $phone = trim($phone);
    if (str_starts_with($phone, '+')) return $phone;
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (str_starts_with($phone, '855')) return '+' . $phone;
    if (str_starts_with($phone, '0')) return '+855' . substr($phone, 1);
    return '+855' . $phone;
}

function buildSettings(): Settings
{
    $apiId = (int) (getenv('TG_API_ID') ?: 33704242);
    $apiHash = getenv('TG_API_HASH') ?: 'a8241f6857875c58bf7ec46d3ed666de';
    $settings = new Settings();
    $appInfo = new AppInfo();
    $appInfo->setApiId($apiId);
    $appInfo->setApiHash($apiHash);
    $appInfo->setDeviceModel('NMU Class Management');
    $appInfo->setSystemVersion('1.0');
    $appInfo->setAppVersion('1.0');
    $appInfo->setLangCode('en');
    $settings->setAppInfo($appInfo);
    return $settings;
}

function getSessionPath(string $phone): string
{
    $safe = preg_replace('/[^0-9]/', '', $phone);
    $dir = __DIR__ . '/storage/app/telegram_sessions';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return $dir . '/session_' . $safe;
}

function cleanupSession(string $phone): void
{
    $sessionPath = getSessionPath($phone);
    if (is_dir($sessionPath)) {
        $items = array_diff(scandir($sessionPath), ['.', '..']);
        foreach ($items as $item) {
            $path = $sessionPath . '/' . $item;
            is_dir($path) ? deleteDir($path) : @unlink($path);
        }
        @rmdir($sessionPath);
    }

    // Also clean up any stale IPC server processes/sockets
    $ipcDir = $sessionPath . '/ipc';
    if (is_dir($ipcDir)) {
        deleteDir($ipcDir);
    }
}

function deleteDir(string $dir): void
{
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        is_dir($path) ? deleteDir($path) : @unlink($path);
    }
    @rmdir($dir);
}

function translateError(string $error): string
{
    // FLOOD_WAIT_X → extract seconds
    if (preg_match('/FLOOD_WAIT[_ ](\d+)/', $error, $m)) {
        $seconds = (int) $m[1];
        $minutes = (int) ceil($seconds / 60);
        return "សូមរង់ចាំ {$minutes} នាទីមុនពេលព្យាយាមម្តងទៀត។ ({$seconds}វិនាទី)";
    }

    $map = [
        'PHONE_CODE_INVALID' => 'កូដមិនត្រឹមត្រូវឡើយ។ សូមព្យាយាមម្តងទៀត។',
        'PHONE_CODE_EXPIRED' => 'កូដបានផុតកំណត់។ សូមស្នើរកូដថ្មី។',
        'PHONE_NUMBER_INVALID' => 'លេខទូរស័ព្ទមិនត្រឹមត្រូវឡើយ។',
        'PHONE_NUMBER_UNOCCUPIED' => 'លេខទូរស័ព្ទនេះមិនទាន់មានគណនី Telegram ឡើយ។',
        'PHONE_NUMBER_BANNED' => 'លេខទូរស័ព្ទនេះត្រូវបានហាមប្រើ។',
        'FLOOD_WAIT' => 'សូមរង់ចាំមួយភ្លែតមុននឹងព្យាយាមម្តងទៀត។',
        'SESSION_PASSWORD_NEEDED' => 'គណនី Telegram នេះមានពាក្យសម្ងាត់ 2FA។ សូមប្រើវិធីផ្សេង។',
    ];
    foreach ($map as $key => $val) {
        if (str_contains($error, $key)) return $val;
    }
    return 'កើតមានបញ្ហា។ សូមព្យាយាមម្តងទៀត។';
}

// Main
$mode = $argv[1] ?? '';
$phone = $argv[2] ?? '';

if (!$mode || !$phone) {
    outputResult(false, 'Missing arguments');
    exit(0);
}

$normalizedPhone = normalizePhone($phone);
$settings = buildSettings();
$sessionFile = getSessionPath($normalizedPhone);

// Force full mode — prevents MadelineProto from starting/connecting IPC server
$_GET['MadelineSelfRestart'] = '1';

if ($mode === 'send') {
    try {
        cleanupSession($normalizedPhone);

        $madeline = new API($sessionFile, $settings);
        $result = $madeline->phoneLogin($normalizedPhone);

        outputResult(true, null, $result['phone_code_hash'] ?? null);
    } catch (RPCErrorException $e) {
        $rawError = $e->rpc . ' ' . $e->getMessage();
        Log::info('Telegram FLOOD_WAIT detail', ['rpc' => $e->rpc, 'msg' => $e->getMessage()]);
        outputResult(false, translateError($rawError));
    } catch (\Throwable $e) {
        outputResult(false, translateError($e->getMessage()));
    }

} elseif ($mode === 'verify') {
    $code = $argv[3] ?? '';
    if (!$code) {
        outputResult(false, 'Missing code');
        exit(0);
    }

    if (!is_dir($sessionFile)) {
        outputResult(false, 'កូដបានផុតកំណត់។ សូមស្នើរកូដថ្មី។');
        exit(0);
    }

    $verified = false;
    try {
        $madeline = new API($sessionFile, $settings);
        $result = $madeline->completePhoneLogin($code);

        // 2FA password required — save hint and return special status
        if (isset($result['_']) && $result['_'] === 'account.password') {
            $hint = $result['hint'] ?? '';
            $hintFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tg_otp_2fa_hint.json';
            file_put_contents($hintFile, json_encode(['hint' => $hint], JSON_UNESCAPED_UNICODE));
            outputResult(false, '2FA_REQUIRED');
            exit(0);
        }

        // Login successful
        try { $madeline->logout(); } catch (\Throwable $e) {}

        $verified = true;
        outputResult(true, 'ផ្ទៀងផ្ទាត់ជោគជ័យ!');
    } catch (RPCErrorException $e) {
        outputResult(false, translateError($e->rpc ?? $e->getMessage()));
    } catch (\Throwable $e) {
        outputResult(false, translateError($e->getMessage()));
    }

    if ($verified) {
        usleep(500000);
        try { cleanupSession($normalizedPhone); } catch (\Throwable $e) {}
    }

} elseif ($mode === 'verify_2fa') {
    $password = $argv[3] ?? '';
    if (!$password) {
        outputResult(false, 'Missing password');
        exit(0);
    }

    if (!is_dir($sessionFile)) {
        outputResult(false, 'Session expired. Please start over.');
        exit(0);
    }

    $verified = false;
    try {
        $madeline = new API($sessionFile, $settings);
        $madeline->complete2faLogin($password);

        try { $madeline->logout(); } catch (\Throwable $e) {}

        $verified = true;
        outputResult(true, 'ផ្ទៀងផ្ទាត់ជោគជ័យ!');
    } catch (RPCErrorException $e) {
        outputResult(false, translateError($e->rpc ?? $e->getMessage()));
    } catch (\Throwable $e) {
        outputResult(false, translateError($e->getMessage()));
    }

    if ($verified) {
        usleep(500000);
        try { cleanupSession($normalizedPhone); } catch (\Throwable $e) {}
    }

} else {
    outputResult(false, 'Unknown mode: ' . $mode);
}

exit(0);