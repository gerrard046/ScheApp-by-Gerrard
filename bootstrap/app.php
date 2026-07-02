<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // OWASP A05 — Security headers di setiap response
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // WAJIB untuk hosting (Laravel Cloud/Railway/Render dkk): aplikasi
        // berjalan di balik reverse proxy, jadi Laravel harus mempercayai
        // header X-Forwarded-* agar HTTPS terdeteksi benar. Tanpa ini,
        // cookie session/CSRF rusak di production -> error 419.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
