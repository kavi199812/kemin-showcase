<?php

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Services\SettingsService;

beforeEach(function () {
    app(SettingsService::class)->clearCache();

    $version = (int) app(SettingsService::class)->get('presenter_session_version', 1);
    session([
        'presenter.unlocked_at' => now()->timestamp,
        'presenter.version' => $version,
    ]);
});

test('products showcase displays active products and filters by category', function () {
    $catA = Category::create(['name' => 'Rigid Boxes', 'slug' => 'rigid-boxes', 'sort_order' => 1]);
    $catB = Category::create(['name' => 'Cartons', 'slug' => 'cartons', 'sort_order' => 2]);

    $prodA = Product::create([
        'category_id' => $catA->id,
        'name' => 'Luxury Rigid Box',
        'slug' => 'luxury-rigid-box',
        'is_active' => true,
    ]);

    $prodB = Product::create([
        'category_id' => $catB->id,
        'name' => 'Corrugated Master Box',
        'slug' => 'corrugated-master-box',
        'is_active' => true,
    ]);

    $response = $this->get('/showcase/products');
    $response->assertOk();
    $response->assertSee('Luxury Rigid Box');
    $response->assertSee('Corrugated Master Box');

    // Filter category
    $response = $this->get('/showcase/products?category=rigid-boxes');
    $response->assertOk();
    $response->assertSee('Luxury Rigid Box');
});

test('product detail page shows technical specs and partner brands', function () {
    $category = Category::create(['name' => 'Tea Packaging', 'slug' => 'tea-packaging', 'sort_order' => 1]);
    $company = Company::create(['name' => 'Dilmah Tea', 'slug' => 'dilmah-tea', 'is_active' => true]);

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Gold Leaf Tea Box',
        'slug' => 'gold-leaf-tea-box',
        'short_description' => '350gsm premium food grade board',
        'specs' => [
            ['key' => 'Board', 'value' => '350gsm Virgin SBS'],
        ],
        'is_active' => true,
    ]);

    $product->companies()->attach($company->id, [
        'relationship_note' => 'Exclusive Jubilee Line',
        'since_year' => 2020,
        'sort_order' => 0,
    ]);

    $response = $this->get("/showcase/products/{$product->slug}");
    $response->assertOk();
    $response->assertSee('Gold Leaf Tea Box');
    $response->assertSee('350gsm Virgin SBS');
    $response->assertSee('Dilmah Tea');
    $response->assertSee('Exclusive Jubilee Line');
});

test('company detail page displays products supplied to that brand', function () {
    $company = Company::create([
        'name' => 'Spa Ceylon Luxury',
        'slug' => 'spa-ceylon-luxury',
        'industry' => 'Wellness',
        'is_active' => true,
    ]);

    $product = Product::create([
        'name' => 'Ayurvedic Gift Sleeve',
        'slug' => 'ayurvedic-gift-sleeve',
        'is_active' => true,
    ]);

    $company->products()->attach($product->id, [
        'relationship_note' => 'Royal Gift Range',
        'since_year' => 2019,
        'sort_order' => 0,
    ]);

    $response = $this->get("/showcase/companies/{$company->slug}");
    $response->assertOk();
    $response->assertSee('Spa Ceylon Luxury');
    $response->assertSee('Ayurvedic Gift Sleeve');
});
