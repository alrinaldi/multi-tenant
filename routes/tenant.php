<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Inertia\Inertia;
use App\Models\Product;
use App\Http\Controllers\TenantAuthController;  // Auth Customer
use App\Http\Controllers\TenantProductController; // CRUD Produk (Pastiin nama controllernya bener)
use App\Http\Controllers\CartController;        // Shopping Cart
use App\Http\Controllers\Tenant\HomeController;        // Home Controller

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

// Route::middleware([
//     'web',
//     InitializeTenancyByDomain::class,
//     PreventAccessFromCentralDomains::class,
// ])->group(function () {
//     // Route::get('/', function () {
//     //     return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     // });
//     Route::get('/', function () {
//     return Inertia::render('Tenant/Home', [
//         'storeName' => tenant('store_name'),
//         // Ambil produk dari database tenant ini
//         'products' => Product::latest()->get() 
//     ]);
// });
// });

Route::middleware([
    'web',
    \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
    \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
])->group(function () {

    // 1. PUBLIC ROUTES (Bisa Diakses Siapa Aja)
    // Route::get('/', function () {
    //     return Inertia::render('Tenant/Home', [
    //         'storeName' => tenant('store_name'),
    //         'products' => Product::latest()->get(),
    //         'auth' => ['user' => auth()->user()] // Kirim status login user
    //     ]);
    // })->name('tenant.home');

    Route::get('/', [HomeController::class, 'index'])->name('tenant.home');

    // 2. AUTH CUSTOMER (Guest Only)
    Route::middleware('guest')->group(function () {
        Route::get('login', [TenantAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [TenantAuthController::class, 'login']);
        Route::get('register', [TenantAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [TenantAuthController::class, 'register']);
    });

    // 3. PROTECTED ROUTES (Harus Login)
    Route::middleware('auth')->group(function () {
        // Logout
        Route::post('logout', [TenantAuthController::class, 'logout'])->name('logout');

        Route::middleware('tenant.admin')->group(function () {
                Route::get('/admin/products', [TenantProductController::class, 'index'])->name('tenant.products.index');
                Route::post('/admin/products', [TenantProductController::class, 'store'])->name('tenant.products.store');
                Route::put('/admin/products/{id}', [TenantProductController::class, 'update'])->name('tenant.products.update');
                Route::delete('/admin/products/{id}', [TenantProductController::class, 'destroy'])->name('tenant.products.destroy');
            });
        // Customer: Shopping Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
    });

});