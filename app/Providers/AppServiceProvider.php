<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // OWASP A02 — Force HTTPS di production
        if (app()->isProduction()) {
            URL::forceScheme('https');
        }

        // OWASP A07 — Rate limiting
        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // Login: maks 5 percobaan per menit per IP + email (brute-force protection)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->input('email') . '|' . $request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'email' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.',
                    ]);
                });
        });

        // Register: maks 3 akun per jam per IP (cegah spam registrasi)
        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)
                ->by($request->ip())
                ->response(function () {
                    return back()->withErrors([
                        'email' => 'Terlalu banyak registrasi dari IP ini. Coba lagi nanti.',
                    ]);
                });
        });

        // Calendar API: maks 60 request per menit per user (cegah scraping)
        RateLimiter::for('calendar-api', function (Request $request) {
            return Limit::perMinute(60)->by(auth()->id() ?? $request->ip());
        });
    }
}
