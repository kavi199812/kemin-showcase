<?php

use App\Models\User;
use App\Services\PinService;
use App\Services\SettingsService;

beforeEach(function () {
    app(SettingsService::class)->clearCache();
    app(PinService::class)->updatePin('8391');
});

test('guest cannot access showcase without unlocking presenter pin', function () {
    $response = $this->get('/showcase');
    $response->assertRedirect('/');

    $response = $this->get('/showcase/products');
    $response->assertRedirect('/');

    $response = $this->get('/showcase/companies');
    $response->assertRedirect('/');
});

test('presenter with valid session can access showcase pages', function () {
    $version = (int) app(SettingsService::class)->get('presenter_session_version', 1);

    $response = $this->withSession([
        'presenter.unlocked_at' => now()->timestamp,
        'presenter.version' => $version,
    ])->get('/showcase');

    $response->assertOk();
    $response->assertSee('Showcase');
});

test('authenticated admin passes through presenter middleware automatically', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/showcase');
    $response->assertOk();
});

test('locking presenter session terminates access and redirects to landing', function () {
    $version = (int) app(SettingsService::class)->get('presenter_session_version', 1);

    $response = $this->withSession([
        'presenter.unlocked_at' => now()->timestamp,
        'presenter.version' => $version,
    ])->post('/lock');

    $response->assertRedirect('/');
    $response->assertSessionMissing('presenter.unlocked_at');
});
