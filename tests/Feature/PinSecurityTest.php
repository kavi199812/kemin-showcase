<?php

use App\Models\PinAttempt;
use App\Models\Setting;
use App\Services\PinService;
use App\Services\SettingsService;

beforeEach(function () {
    app(SettingsService::class)->clearCache();
});

test('pin service rejects trivial pins', function () {
    $pinService = app(PinService::class);

    $trivialPins = [
        '1234', '0000', '1111', '123456', '654321',
        '000000', '111111', '121212', '112233', '222222', '345678',
    ];

    foreach ($trivialPins as $pin) {
        expect($pinService->isTrivialPin($pin))->toBeTrue("Failed asserting that {$pin} is trivial");
    }

    expect($pinService->isTrivialPin('8391'))->toBeFalse();
    expect($pinService->isTrivialPin('839162'))->toBeFalse();
    expect($pinService->isTrivialPin('741953'))->toBeFalse();
});

test('pin service verifies valid pin using argon2id and generates presenter session', function () {
    $settings = app(SettingsService::class);
    $pinService = app(PinService::class);

    $pin = '8391';
    $pinService->updatePin($pin);

    $storedHash = Setting::where('key', 'presenter_pin_hash')->value('value');
    expect($storedHash)->not->toBeEmpty();
    expect(str_starts_with($storedHash, '$argon2id$'))->toBeTrue();

    $result = $pinService->verify($pin, '127.0.0.1');

    expect($result['success'])->toBeTrue();
    expect(session('presenter.unlocked_at'))->not->toBeNull();
    expect(session('presenter.version'))->toBe((int) $settings->get('presenter_session_version'));

    expect(PinAttempt::where('ip', '127.0.0.1')->where('succeeded', true)->exists())->toBeTrue();
});

test('pin verification fails with incorrect pin and logs attempt', function () {
    $pinService = app(PinService::class);
    $pinService->updatePin('8391');

    $result = $pinService->verify('9876', '127.0.0.2');

    expect($result['success'])->toBeFalse();
    expect(PinAttempt::where('ip', '127.0.0.2')->where('succeeded', false)->exists())->toBeTrue();
});

test('rate limiting locks out device after max failed attempts', function () {
    $pinService = app(PinService::class);
    $pinService->updatePin('8391');

    $ip = '192.168.1.100';

    for ($i = 0; $i < 5; $i++) {
        $pinService->verify('9876', $ip);
    }

    $lockout = $pinService->checkLockout($ip);
    expect($lockout['locked'])->toBeTrue();
    expect($lockout['message'])->toContain('Device locked');

    // Subsequent verification is blocked by lockout
    $res = $pinService->verify('839162', $ip);
    expect($res['success'])->toBeFalse();
});

test('updating pin invalidates active presenter sessions by bumping session version', function () {
    $settings = app(SettingsService::class);
    $pinService = app(PinService::class);

    $oldVersion = (int) $settings->get('presenter_session_version', 1);

    $pinService->updatePin('741953');

    $newVersion = (int) $settings->get('presenter_session_version', 1);
    expect($newVersion)->toBe($oldVersion + 1);
});
