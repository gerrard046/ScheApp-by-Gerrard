<?php

namespace App\Http\Controllers;

use App\Services\GoogleCalendarService;
use Google\Client;
use Illuminate\Http\Request;

class GoogleCalendarController extends Controller
{
    public function __construct(private GoogleCalendarService $gCalService) {}

    // ── Step 1: Redirect ke halaman consent Google ──────────────────────────

    public function redirect()
    {
        $client = $this->makeBaseClient();

        // Paksa munculkan consent screen agar kita dapat refresh_token
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $authUrl = $client->createAuthUrl();
        return redirect()->away($authUrl);
    }

    // ── Step 2: Google mengarahkan balik ke sini setelah user setuju ─────────

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/calendar')->with('error', 'Otorisasi Google dibatalkan: ' . $request->error);
        }

        $code   = $request->query('code');
        $client = $this->makeBaseClient();

        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            return redirect('/calendar')->with('error', 'Gagal mendapatkan token Google: ' . ($token['error_description'] ?? $token['error']));
        }

        $user = auth()->user();
        $user->update([
            'google_access_token'     => encrypt($token['access_token']),
            'google_refresh_token'    => isset($token['refresh_token'])
                ? encrypt($token['refresh_token'])
                : $user->google_refresh_token, // jangan overwrite yang sudah ada
            'google_token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
            'google_calendar_id'      => 'primary',
        ]);

        return redirect('/calendar')->with('success', 'Akun Google berhasil dihubungkan! Jadwal akan tersinkron otomatis.');
    }

    // ── Putus koneksi Google ─────────────────────────────────────────────────

    public function disconnect()
    {
        $user = auth()->user();

        // Revoke token di sisi Google agar izin benar-benar dicabut
        if ($user->google_access_token) {
            try {
                $client = $this->makeBaseClient();
                $client->setAccessToken(['access_token' => decrypt($user->google_access_token)]);
                $client->revokeToken();
            } catch (\Throwable) {
                // Token mungkin sudah tidak valid, tetap lanjutkan
            }
        }

        $user->update([
            'google_access_token'     => null,
            'google_refresh_token'    => null,
            'google_token_expires_at' => null,
        ]);

        return redirect('/calendar')->with('success', 'Koneksi Google Calendar diputus.');
    }

    // ── Tarik (pull) event dari Google Calendar ke database ─────────────────

    public function syncPull()
    {
        $user = auth()->user();

        if (!$user->google_access_token) {
            return redirect('/calendar')->with('error', 'Hubungkan akun Google terlebih dahulu.');
        }

        try {
            $count = $this->gCalService->syncFromGoogle($user);
            return redirect('/calendar')->with('success', "Sinkronisasi selesai! {$count} jadwal baru dari Google Calendar ditambahkan.");
        } catch (\Throwable $e) {
            \Log::error('Google sync failed: ' . $e->getMessage());
            return redirect('/calendar')->with('error', 'Sinkronisasi gagal: ' . $e->getMessage());
        }
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function makeBaseClient(): Client
    {
        $client = new Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->addScope(config('google.scopes'));
        return $client;
    }
}
