<?php

use App\Models\User;
use App\Services\SettingsService;

beforeEach(function () {
    app(SettingsService::class)->clearCache();
});

test('unauthenticated users cannot access admin dashboard', function () {
    $response = $this->get('/admin');
    $response->assertRedirect('/admin/login');
});

test('non-admin users are denied access to admin routes', function () {
    $regularUser = User::factory()->create([
        'is_admin' => false,
    ]);

    $response = $this->actingAs($regularUser)->get('/admin');
    $response->assertForbidden();
});

test('admin can access dashboard and administration routes', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
    ]);

    $response = $this->actingAs($admin)->get('/admin');
    $response->assertOk();
    $response->assertSee('Showcase Operations');

    $response = $this->actingAs($admin)->get('/admin/products');
    $response->assertOk();

    $response = $this->actingAs($admin)->get('/admin/companies');
    $response->assertOk();

    $response = $this->actingAs($admin)->get('/admin/categories');
    $response->assertOk();

    $response = $this->actingAs($admin)->get('/admin/settings');
    $response->assertOk();

    $response = $this->actingAs($admin)->get('/admin/activity');
    $response->assertOk();
});

test('security headers are present on all responses', function () {
    $response = $this->get('/');

    $response->assertHeader('X-Frame-Options', 'DENY');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('X-Robots-Tag', 'noindex, nofollow');
});
