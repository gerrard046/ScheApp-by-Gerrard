<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * OWASP A05 — Security Misconfiguration
 * Tambah HTTP security headers ke setiap response.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Cegah clickjacking (OWASP A05)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Cegah MIME-type sniffing (OWASP A05)
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Aktifkan XSS filter browser (legacy, masih berguna untuk IE/Edge lama)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Kontrol referrer info (OWASP A02)
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Batasi akses fitur browser yang tidak dipakai (OWASP A05)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // CSP — izinkan CDN yang dipakai (FullCalendar, Alpine.js, Google Fonts)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net fonts.googleapis.com",
            "font-src 'self' fonts.gstatic.com",
            "img-src 'self' data: blob:",
            "connect-src 'self' https://www.googleapis.com",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        // HSTS — hanya aktifkan di production (OWASP A02)
        if (app()->isProduction()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Cache-Control — cegah browser cache halaman yg mengandung data user
        $contentType = $response->headers->get('Content-Type', '');
        if (str_contains($contentType, 'text/html')) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        // Hapus header yang membocorkan info server
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}
