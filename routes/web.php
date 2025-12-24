<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\CentralAuthController;
use App\Http\Controllers\TenantRegisterController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\PreventBackHistory;



Route::get('/test', function () {
    return Inertia::render('Test');
});


Route::domain('localhost')->group(function () {

    // === GUEST (Belum Login) ===
    Route::middleware('guest')->group(function () {
        // Landing Page
        Route::get('/', function () {
            return Inertia::render('Central/Welcome'); 
            
        })->name('central.home'); // Aman, cuma didefinisikan sekali

        // Login & Register Owner
        Route::get('login', [CentralAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [CentralAuthController::class, 'login']);
        Route::get('register', [CentralAuthController::class, 'showRegister'])->name('register');
        Route::post('register', [CentralAuthController::class, 'register']);
    });

    // === AUTH (Owner Sudah Login) ===
        Route::middleware(['auth', 'prevent-back-history'])->group(function () {        // Dashboard buat bikin toko
        Route::get('/dashboard', [TenantRegisterController::class, 'index'])->name('central.dashboard');
        
        // Action Submit Bikin Toko
        Route::post('/register-tenant', [TenantRegisterController::class, 'store'])->name('tenant.register');
        
        // Logout
        Route::post('/logout', [CentralAuthController::class, 'logout'])->name('logout');
    });

});

// Route::post('/register-tenant', [TenantRegisterController::class, 'store'])->name('tenant.register');

// Route::middleware([
//     'web',
//     \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class, // Deteksi subdomain (toko1.app.com)
//     \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
// ])->group(function () {
    


//     // Auth Routes (Login/Register khusus Tenant ini)
//     // require __DIR__.'/auth.php'; // Copy auth.php breeze, sesuaikan dikit

//     // CRUD Product (Cuma bisa diakses Admin Toko)
//     Route::resource('products', ProductController::class)->middleware('auth');

//     // Shopping Cart
// Route::middleware('auth')->group(function () {
//         Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
//         Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
//         Route::delete('/cart/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
//     });
// });