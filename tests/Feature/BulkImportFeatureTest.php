<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkImportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_bulk_import(): void
    {
        $response = $this->get(route('admin.bulk-import'));
        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_access_bulk_import_page(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bulk-import'));
        $response->assertOk();
        $response->assertSee('CSV Bulk Import');
        $response->assertSee('Products Bulk Import');
        $response->assertSee('Companies Bulk Import');
    }

    public function test_admin_can_download_products_sample_csv(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bulk-import.sample', ['type' => 'products']));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition', 'attachment; filename="kemin_products_sample.csv"');
    }

    public function test_admin_can_download_companies_sample_csv(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bulk-import.sample', ['type' => 'companies']));
        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertHeader('content-disposition', 'attachment; filename="kemin_companies_sample.csv"');
    }
}
