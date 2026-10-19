<?php

use App\Livewire\Admin\Activity\ActivityViewer;
use App\Livewire\Admin\Categories\CategoryManager;
use App\Livewire\Admin\Companies\CompanyForm;
use App\Livewire\Admin\Companies\CompanyList;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Products\ProductForm;
use App\Livewire\Admin\Products\ProductList;
use App\Livewire\Admin\Settings\SettingsManager;
use App\Livewire\Auth\Landing;
use App\Livewire\Showcase\CompanyIndex;
use App\Livewire\Showcase\CompanyShow;
use App\Livewire\Showcase\Home;
use App\Livewire\Showcase\ProductIndex;
use App\Livewire\Showcase\ProductShow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — Kemin Exports Showcase
|--------------------------------------------------------------------------
*/

// Guest landing (Video + Brand Logo + Argon2id Segmented PIN)
Route::get('/', Landing::class)->name('landing');

// End Presenter Session & Logout
Route::post('/lock', function (Request $request) {
    if (Auth::check()) {
        Auth::guard('web')->logout();
    }
    $request->session()->forget(['presenter.unlocked_at', 'presenter.version']);
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('landing')->with('status', 'Logged out and showcase session locked.');
})->name('lock');

Route::post('/logout', function (Request $request) {
    if (Auth::check()) {
        Auth::guard('web')->logout();
    }
    $request->session()->forget(['presenter.unlocked_at', 'presenter.version']);
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('landing')->with('status', 'Logged out and showcase session locked.');
})->name('logout');

// Named route alias for admin login
Route::get('/admin/login-redirect', function () {
    return redirect()->route('login');
})->name('admin.login');

// Presenter Routes (Secured by EnsurePresenterUnlocked or Authenticated Admin)
Route::middleware(['presenter'])->prefix('showcase')->name('showcase.')->group(function () {
    Route::get('/', Home::class)->name('home');
    Route::get('/products', ProductIndex::class)->name('products.index');
    Route::get('/products/{slug}', ProductShow::class)->name('products.show');
    Route::get('/companies', CompanyIndex::class)->name('companies.index');
    Route::get('/companies/{slug}', CompanyShow::class)->name('companies.show');
});

// Admin Routes (Secured by EnsureIsAdmin middleware)
Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');

    // Products CRUD
    Route::get('/products', ProductList::class)->name('products.index');
    Route::get('/products/create', ProductForm::class)->name('products.create');
    Route::get('/products/{id}/edit', ProductForm::class)->name('products.edit');

    // Companies CRUD
    Route::get('/companies', CompanyList::class)->name('companies.index');
    Route::get('/companies/create', CompanyForm::class)->name('companies.create');
    Route::get('/companies/{id}/edit', CompanyForm::class)->name('companies.edit');

    // Categories Management
    Route::get('/categories', CategoryManager::class)->name('categories.index');

    // Settings & Security PIN (password.confirm when applicable)
    Route::get('/settings', SettingsManager::class)->name('settings.index');

    // Activity Audit Log
    Route::get('/activity', ActivityViewer::class)->name('activity.index');

    // Sign out
    Route::post('/logout', function (Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

// Fallback route to serve uploaded public storage files when symlink is not present on production/shared hosting
Route::get('/storage/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(404);
    }

    $filePath = storage_path('app/public/'.$path);
    if (! is_file($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*')->name('storage.fallback');

// Hostinger / Shared hosting helper to create symlink, clear cache, and report diagnostics
Route::get('/link-storage', function () {
    $messages = [];

    // 1. Try creating symlink if permitted by host
    try {
        if (! file_exists(public_path('storage'))) {
            if (function_exists('symlink')) {
                @symlink(storage_path('app/public'), public_path('storage'));
                $messages[] = 'Symlink: Created via native symlink().';
            } else {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
                $messages[] = 'Symlink: Created via Artisan storage:link.';
            }
        } else {
            $messages[] = 'Symlink: public/storage already exists.';
        }
    } catch (\Throwable $e) {
        $messages[] = 'Symlink: Hostinger disabled exec()/symlink(). (Laravel Fallback Route will serve images directly!)';
    }

    // 2. Clear all Laravel caches
    try {
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        $messages[] = 'Cache: Config, cache, routes, and views cleared successfully.';
    } catch (\Throwable $e) {
        $messages[] = 'Cache notice: '.$e->getMessage();
    }

    // 3. Storage Diagnostics
    $storagePublicPath = storage_path('app/public');
    $messages[] = 'Storage App Public Path: '.$storagePublicPath.' (Exists: '.(is_dir($storagePublicPath) ? 'Yes' : 'No').')';
    $files = is_dir($storagePublicPath) ? array_diff(scandir($storagePublicPath), ['.', '..']) : [];
    $messages[] = 'Items in storage/app/public: '.count($files).' ['.implode(', ', $files).']';
    $messages[] = 'Public Disk URL: '.config('filesystems.disks.public.url');

    // Inspect files in folders 4, 5, 6
    foreach ([4, 5, 6] as $dirId) {
        $dirPath = $storagePublicPath.'/'.$dirId;
        if (is_dir($dirPath)) {
            $subFiles = array_diff(scandir($dirPath), ['.', '..']);
            $messages[] = "Folder {$dirId} files: [".implode(', ', $subFiles).']';
            if (is_dir($dirPath.'/conversions')) {
                $convFiles = array_diff(scandir($dirPath.'/conversions'), ['.', '..']);
                $messages[] = "Folder {$dirId}/conversions: [".implode(', ', $convFiles).']';
            } else {
                $messages[] = "Folder {$dirId}/conversions does NOT exist!";
            }
        } else {
            $messages[] = "Folder {$dirId} does NOT exist!";
        }
    }

    // Inspect first product media URLs
    $product = \App\Models\Product::with('media')->first();
    $testImageUrl = '';
    if ($product) {
        $messages[] = "Product: {$product->name}";
        $mediaItems = $product->getMedia('gallery');
        $messages[] = 'Gallery Media Count: '.count($mediaItems);
        foreach ($mediaItems as $idx => $m) {
            $messages[] = "Media #{$m->id}: file_name={$m->file_name}, disk={$m->disk}";
            $messages[] = "  getUrl(): ".$m->getUrl();
            $messages[] = "  getUrl('card'): ".$m->getUrl('card');
            $messages[] = "  hasGeneratedConversion('card'): ".($m->hasGeneratedConversion('card') ? 'true' : 'false');
            if ($idx === 0) {
                $testImageUrl = $m->hasGeneratedConversion('card') ? $m->getUrl('card') : $m->getUrl();
            }
        }
    }

    $textOutput = implode("\n", $messages);
    $html = "<!DOCTYPE html><html><body style='font-family: monospace; background: #111; color: #eee; padding: 20px;'>";
    $html .= "<pre>".e($textOutput)."</pre>";
    if ($testImageUrl) {
        $html .= "<h3>Direct Image Test (Attempting to load: {$testImageUrl}):</h3>";
        $html .= "<div style='border: 2px solid #555; display: inline-block; padding: 10px; background: #222;'>";
        $html .= "<img src='{$testImageUrl}' style='max-width: 400px; max-height: 300px; display: block;' onerror=\"this.outerHTML='<b style=color:red>IMAGE FAILED TO LOAD! Check browser console (F12) Network tab for HTTP status (403, 404, etc.)</b>'\">";
        $html .= "</div>";
    }
    $html .= "</body></html>";

    return response($html, 200)->header('Content-Type', 'text/html');
});

