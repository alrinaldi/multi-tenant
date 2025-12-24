<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit; // <--- JANGAN LUPA IMPORT
use Illuminate\Http\Request;             // <--- JANGAN LUPA IMPORT
use Illuminate\Support\Facades\RateLimiter; // <--- JANGAN LUPA IMPORT
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. LIMIT API (Standar 60x per menit)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // 2. LIMIT LOGIN (Khusus Login Page - Agak ketat dikit gapapa)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->email . $request->ip());
        });

        // 3. LIMIT GLOBAL / WEB (INI KUNCINYA CUK!)
        // Kita kasih 1000 request per menit.
        // Jadi user bolak-balik dashboard - login sampe jempol keriting gak bakal kena blokir.
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(1000)->by($request->user()?->id ?: $request->ip());
        });
    }
}