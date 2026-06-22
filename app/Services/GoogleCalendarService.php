<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    // ── Client Factory ─────────────────────────────────────────────────────

    /**
     * Buat Google_Client yang sudah terautentikasi untuk user tertentu.
     * Token di-decrypt dari database dan di-refresh otomatis jika kadaluwarsa.
     */
    public function getClient(User $user): Client
    {
        $client = new Client();
        $client->setClientId(config('google.client_id'));
        $client->setClientSecret(config('google.client_secret'));
        $client->setRedirectUri(config('google.redirect_uri'));
        $client->addScope(config('google.scopes'));
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        $tokenData = [
            'access_token'  => decrypt($user->google_access_token),
            'token_type'    => 'Bearer',
            'expires_in'    => 3600,
            'created'       => $user->google_token_expires_at
                ? $user->google_token_expires_at->timestamp - 3600
                : (time() - 3601), // paksa refresh jika tidak diketahui
        ];

        if ($user->google_refresh_token) {
            $tokenData['refresh_token'] = decrypt($user->google_refresh_token);
        }

        $client->setAccessToken($tokenData);

        // Auto-refresh jika token sudah kadaluwarsa
        if ($client->isAccessTokenExpired()) {
            if (!$client->getRefreshToken()) {
                throw new \RuntimeException('Google refresh token hilang. Silakan hubungkan ulang akun Google.');
            }

            $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());

            if (isset($newToken['error'])) {
                throw new \RuntimeException('Gagal refresh token Google: ' . $newToken['error_description']);
            }

            $user->update([
                'google_access_token'     => encrypt($newToken['access_token']),
                'google_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
            ]);
        }

        return $client;
    }

    private function service(User $user): Calendar
    {
        return new Calendar($this->getClient($user));
    }

    private function calendarId(User $user): string
    {
        return $user->google_calendar_id ?: 'primary';
    }

    // ── Push: Laravel → Google ─────────────────────────────────────────────

    /**
     * Buat event baru di Google Calendar dan simpan google_event_id ke database.
     */
    public function createEvent(Schedule $schedule): void
    {
        $user    = $schedule->user;
        $service = $this->service($user);
        $calId   = $this->calendarId($user);

        $gEvent  = $this->buildGoogleEvent($schedule);
        $created = $service->events->insert($calId, $gEvent);

        $schedule->update(['google_event_id' => $created->getId()]);
    }

    /**
     * Perbarui event di Google Calendar (misalnya setelah drag & drop).
     */
    public function updateEvent(Schedule $schedule): void
    {
        if (!$schedule->google_event_id) {
            $this->createEvent($schedule);
            return;
        }

        $user    = $schedule->user;
        $service = $this->service($user);
        $calId   = $this->calendarId($user);
        $gEvent  = $this->buildGoogleEvent($schedule);

        try {
            $service->events->update($calId, $schedule->google_event_id, $gEvent);
        } catch (\Google\Service\Exception $e) {
            if ($e->getCode() === 404) {
                // Event tidak ditemukan di Google — buat ulang
                $this->createEvent($schedule);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Hapus event dari Google Calendar.
     */
    public function deleteEvent(Schedule $schedule): void
    {
        if (!$schedule->google_event_id) return;

        $user    = $schedule->user;
        $service = $this->service($user);
        $calId   = $this->calendarId($user);

        try {
            $service->events->delete($calId, $schedule->google_event_id);
        } catch (\Google\Service\Exception $e) {
            // 404 artinya sudah tidak ada di Google, abaikan
            if ($e->getCode() !== 404) throw $e;
        }

        $schedule->update(['google_event_id' => null]);
    }

    // ── Pull: Google → Laravel ─────────────────────────────────────────────

    /**
     * Tarik semua event dari Google Calendar milik user dan simpan ke database.
     * Event yang sudah ada (cocok google_event_id) akan di-update.
     * Rentang default: 30 hari lalu sampai 90 hari ke depan.
     */
    public function syncFromGoogle(User $user, ?string $timeMin = null, ?string $timeMax = null): int
    {
        $service = $this->service($user);
        $calId   = $this->calendarId($user);

        $params = [
            'maxResults'   => 500,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => $timeMin ?? now()->subDays(30)->toRfc3339String(),
            'timeMax'      => $timeMax ?? now()->addDays(90)->toRfc3339String(),
        ];

        $results = $service->events->listEvents($calId, $params);
        $count   = 0;

        foreach ($results->getItems() as $gEvent) {
            $startRaw = $gEvent->getStart()->getDateTime() ?? $gEvent->getStart()->getDate();
            $endRaw   = $gEvent->getEnd()->getDateTime()   ?? $gEvent->getEnd()->getDate();
            $allDay   = !$gEvent->getStart()->getDateTime();

            $start = Carbon::parse($startRaw);
            $end   = Carbon::parse($endRaw);

            // Cek apakah sudah ada di database
            $existing = Schedule::where('user_id', $user->id)
                ->where('google_event_id', $gEvent->getId())
                ->first();

            $data = [
                'user_id'        => $user->id,
                'user_name'      => $user->name,
                'group_name'     => 'Google Calendar',
                'activity_name'  => $gEvent->getSummary() ?: '(Tanpa Judul)',
                'notes'          => $gEvent->getDescription(),
                'start_datetime' => $start,
                'end_datetime'   => $end,
                'is_all_day'     => $allDay,
                'date'           => $start->format('Y-m-d'),
                'time'           => $start->format('H:i:s'),
                'google_event_id'=> $gEvent->getId(),
                'category'       => 'Google Calendar',
                'priority'       => 'low',
            ];

            if ($existing) {
                $existing->update($data);
            } else {
                Schedule::create($data);
                $count++;
            }
        }

        return $count;
    }

    // ── Builder ────────────────────────────────────────────────────────────

    private function buildGoogleEvent(Schedule $schedule): Event
    {
        $gEvent = new Event([
            'summary'     => $schedule->activity_name,
            'description' => $schedule->notes,
        ]);

        if ($schedule->is_all_day) {
            $gEvent->setStart(new EventDateTime([
                'date' => $schedule->start_datetime->format('Y-m-d'),
            ]));
            $gEvent->setEnd(new EventDateTime([
                'date' => $schedule->end_datetime->format('Y-m-d'),
            ]));
        } else {
            $gEvent->setStart(new EventDateTime([
                'dateTime' => $schedule->start_datetime->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Asia/Jakarta'),
            ]));
            $gEvent->setEnd(new EventDateTime([
                'dateTime' => $schedule->end_datetime->toRfc3339String(),
                'timeZone' => config('app.timezone', 'Asia/Jakarta'),
            ]));
        }

        // Warna event (Google menggunakan color ID, bukan hex)
        if ($schedule->color) {
            $gEvent->setColorId($this->hexToGoogleColorId($schedule->color));
        }

        // Recurring event
        if ($schedule->recurrence_rule) {
            $gEvent->setRecurrence(['RRULE:' . $schedule->recurrence_rule]);
        }

        return $gEvent;
    }

    /**
     * Peta warna hex ke Google Calendar color ID (1–11).
     * Referensi: https://developers.google.com/calendar/api/v3/reference/colors
     */
    private function hexToGoogleColorId(string $hex): string
    {
        $map = [
            '#EF4444' => '11', // Tomato
            '#F59E0B' => '5',  // Banana
            '#10B981' => '10', // Sage
            '#6366F1' => '9',  // Blueberry
            '#8B5CF6' => '3',  // Grape
            '#EC4899' => '4',  // Flamingo
            '#14B8A6' => '7',  // Peacock
            '#3B82F6' => '9',  // Blueberry
            '#0EA5E9' => '7',  // Peacock
        ];

        return $map[strtoupper($hex)] ?? $map[$hex] ?? '9';
    }
}
