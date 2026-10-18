<?php

namespace App\Services;

use App\Models\PinAttempt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PinService
{
    public function __construct(
        protected SettingsService $settings
    ) {}

    /**
     * Check if a PIN is trivial/weak.
     */
    public function isTrivialPin(string $pin): bool
    {
        $len = strlen($pin);
        if ($len < 4) {
            return true;
        }

        // Explicit known blacklisted sequences
        $blacklist = [
            '1234', '0000', '1111', '123456', '654321',
            '000000', '111111', '121212', '112233', '12345678', '87654321',
        ];
        if (in_array($pin, $blacklist, true)) {
            return true;
        }

        // All same digits (e.g. 2222, 555555)
        if (preg_match('/^(.)\1+$/', $pin)) {
            return true;
        }

        // Check sequential ascending (e.g. 2345, 345678)
        $isAsc = true;
        $isDesc = true;
        for ($i = 1; $i < $len; $i++) {
            $prev = (int) $pin[$i - 1];
            $curr = (int) $pin[$i];
            if ($curr !== ($prev + 1)) {
                $isAsc = false;
            }
            if ($curr !== ($prev - 1)) {
                $isDesc = false;
            }
        }

        return $isAsc || $isDesc;
    }

    /**
     * Check if client or global system is locked out.
     *
     * @return array{locked: bool, minutes: int, message: string|null}
     */
    public function checkLockout(string $ip, ?string $fingerprint = null): array
    {
        $windowMinutes = (int) $this->settings->get('lockout_window_minutes', 15);
        $maxAttempts = (int) $this->settings->get('lockout_max_attempts', 5);
        $lockoutMinutes = (int) $this->settings->get('lockout_minutes', 15);
        $globalThreshold = (int) $this->settings->get('global_lockout_threshold', 30);

        $windowStart = Carbon::now()->subMinutes($windowMinutes);

        // 1. Global breaker check
        $globalFailures = PinAttempt::where('succeeded', false)
            ->where('created_at', '>=', $windowStart)
            ->count();

        if ($globalFailures >= $globalThreshold) {
            // Find latest failure to determine lockout duration
            $latestGlobal = PinAttempt::where('succeeded', false)->latest('created_at')->first();
            $elapsedMinutes = $latestGlobal ? Carbon::now()->diffInMinutes($latestGlobal->created_at) : 0;
            $remaining = max(1, 30 - $elapsedMinutes);

            return [
                'locked' => true,
                'minutes' => $remaining,
                'message' => "System temporarily locked due to multiple security alerts. Please retry in {$remaining} minutes.",
            ];
        }

        // 2. IP / fingerprint rate limit check
        $query = PinAttempt::where('succeeded', false)
            ->where('created_at', '>=', $windowStart)
            ->where(function ($q) use ($ip, $fingerprint) {
                $q->where('ip', $ip);
                if (! empty($fingerprint)) {
                    $q->orWhere('fingerprint', $fingerprint);
                }
            });

        $clientFailures = $query->count();

        if ($clientFailures >= $maxAttempts) {
            $latestAttempt = (clone $query)->latest('created_at')->first();
            $elapsedMinutes = $latestAttempt ? Carbon::now()->diffInMinutes($latestAttempt->created_at) : 0;
            $remaining = max(1, $lockoutMinutes - $elapsedMinutes);

            return [
                'locked' => true,
                'minutes' => $remaining,
                'message' => "Too many failed attempts. Device locked for {$remaining} minutes.",
            ];
        }

        return [
            'locked' => false,
            'minutes' => 0,
            'message' => null,
        ];
    }

    /**
     * Verify submitted PIN with constant-time execution and security delay.
     *
     * @return array{success: bool, error: string|null}
     */
    public function verify(string $pin, string $ip, ?string $fingerprint = null): array
    {
        // 1. Check lockouts
        $lockout = $this->checkLockout($ip, $fingerprint);
        if ($lockout['locked']) {
            return [
                'success' => false,
                'error' => $lockout['message'] ?? 'Too many attempts. Access locked.',
            ];
        }

        // 2. Retrieve PIN hash or a safe dummy hash so Hash::check is ALWAYS executed
        $storedHash = $this->settings->get('presenter_pin_hash');
        $dummyHash = '$argon2id$v=19$m=65536,t=4,p=1$ZHVtbXlmb3Jjb25zdGFudHRpbWU$0kZJ279zNq4s+U1a5J6p7yH9d8rB5yN+7W2Pq4pM8bA';
        $hashToCompare = ! empty($storedHash) ? (string) $storedHash : $dummyHash;

        $isValid = Hash::check($pin, $hashToCompare);

        // 3. Log attempt
        PinAttempt::create([
            'ip' => $ip,
            'fingerprint' => $fingerprint ? substr($fingerprint, 0, 64) : null,
            'succeeded' => $isValid,
            'created_at' => now(),
        ]);

        if ($isValid) {
            // Regenerate session and set presenter tokens
            session()->regenerate(true);
            session()->put('presenter.unlocked_at', now()->timestamp);
            session()->put('presenter.version', (int) $this->settings->get('presenter_session_version', 1));

            return [
                'success' => true,
                'error' => null,
            ];
        }

        // Failure: 100-300ms random jitter delay to prevent timing attacks
        usleep(random_int(100000, 300000));

        return [
            'success' => false,
            'error' => 'Incorrect PIN. Access denied.',
        ];
    }

    /**
     * Set a new PIN.
     *
     * @throws \InvalidArgumentException
     */
    public function updatePin(string $newPin): void
    {
        if ($this->isTrivialPin($newPin)) {
            throw new \InvalidArgumentException('PIN is too simple or predictable. Please choose a more secure numeric PIN.');
        }

        // Hash with argon2id
        $hash = Hash::make($newPin);

        $this->settings->setMany([
            'presenter_pin_hash' => $hash,
            'pin_length' => strlen($newPin),
        ]);

        // Invalidate active presenter sessions
        $this->settings->bumpSessionVersion();
    }
}
