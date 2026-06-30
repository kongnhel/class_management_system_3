<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class TelegramClientService
{
    protected string $phpBinary;
    protected string $scriptPath;

    public function __construct()
    {
        $this->phpBinary = PHP_BINARY;
        $this->scriptPath = base_path('telegram_otp.php');
    }

    /**
     * Send a login code to the user's Telegram via Client API.
     */
    public function sendCode(string $phone): array
    {
        return $this->runBridge('send', $phone);
    }

    /**
     * Verify the login code via auth.signIn.
     */
    public function verifyCode(string $phone, string $code): array
    {
        return $this->runBridge('verify', $phone, $code);
    }

    /**
     * Complete 2FA login with password.
     */
    public function verify2fa(string $phone, string $password): array
    {
        return $this->runBridge('verify_2fa', $phone, $password);
    }

    /**
     * Get 2FA hint from temp file.
     */
    public function get2faHint(): string
    {
        $hintFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tg_otp_2fa_hint.json';
        if (file_exists($hintFile)) {
            $data = json_decode(file_get_contents($hintFile), true);
            @unlink($hintFile);
            return $data['hint'] ?? '';
        }
        return '';
    }

    /**
     * Run the CLI bridge script and read result from temp file.
     */
    protected function runBridge(string $mode, string $phone, ?string $code = null): array
    {
        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tg_otp_result.json';

        // Remove old temp file before running
        if (file_exists($tmpFile)) {
            @unlink($tmpFile);
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        // Build command as array to avoid cmd.exe quoting issues on Windows
        $cmd = [$this->phpBinary, $this->scriptPath, $mode, $phone];
        if ($code !== null) {
            $cmd[] = $code;
        }

        Log::info('Telegram CLI bridge', ['mode' => $mode, 'phone' => $phone]);

        $process = proc_open($cmd, $descriptors, $pipes);

        if (! is_resource($process)) {
            return [
                'success' => false,
                'message' => 'មិនអាចចាប់ផ្ដើមដំណើរការបានឡើយ។',
            ];
        }

        // Close stdin
        fclose($pipes[0]);

        // Read stdout and stderr to prevent blocking
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        // Wait for process to finish
        $exitCode = proc_close($process);

        // Read result from temp file
        $result = null;
        if (file_exists($tmpFile)) {
            $raw = file_get_contents($tmpFile);
            $result = json_decode($raw, true);
            @unlink($tmpFile);
        }

        if (! is_array($result) || !isset($result['success'])) {
            Log::error('Telegram CLI bridge: failed to read result', [
                'mode' => $mode,
                'phone' => $phone,
                'exitCode' => $exitCode,
                'tmpFile' => $tmpFile,
                'tmpExists' => file_exists($tmpFile),
                'stdout_tail' => substr(trim($stdout), -200),
                'stderr_tail' => substr(trim($stderr), -200),
            ]);

            return [
                'success' => false,
                'message' => 'កើតមានបញ្ហាក្នុងការទាក់ទង Telegram។ សូមព្យាយាមម្តងទៀត។',
            ];
        }

        Log::info('Telegram CLI bridge result', [
            'mode' => $mode,
            'phone' => $phone,
            'success' => $result['success'],
            'message' => $result['message'] ?? null,
        ]);

        return $result;
    }
}