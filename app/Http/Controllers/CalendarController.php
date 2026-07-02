<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * CalendarController
 *
 * Menyediakan tampilan kalender "Arctic Breeze" (Bulan / Minggu / Hari)
 * beserta operasi CRUD event lewat modal. Controller ini SENGAJA dipisah
 * dari ScheduleController agar tidak mengganggu fitur existing
 * (gamifikasi XP, verifikasi tugas, Zen Mode, Kanban).
 */
class CalendarController extends Controller
{
    // Warna sesuai prioritas (palet Arctic Breeze).
    private const PRIORITY_COLORS = [
        'low'  => '#5BA3E0',
        'med'  => '#F5A623',
        'high' => '#E8576B',
    ];

    /**
     * Ambil schedule milik user login (atau grupnya) lalu kirim ke view
     * sebagai JSON agar bisa dibaca Alpine.js.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Schedule::query();

        // Admin melihat semua; user biasa hanya miliknya atau tugas grupnya.
        if ($user->role !== 'admin') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereHas('group', function ($g) use ($user) {
                      $g->whereHas('members', function ($m) use ($user) {
                          $m->where('users.id', $user->id);
                      });
                  });
            });
        }

        $events = $query->get()->map(fn ($s) => $this->toEvent($s))->values();

        return view('schedules.calendar', [
            'events' => $events,
        ]);
    }

    /**
     * Buat event baru dari modal kalender.
     * Kolom `date` & `time` ikut diisi agar fitur lama (XP/streak yang
     * menghitung dari date+time) tetap berfungsi normal.
     */
    public function store(Request $request)
    {
        $data = $this->validateEvent($request);

        $schedule = Schedule::create([
            'user_id'       => auth()->id(),
            'user_name'     => auth()->user()->name,
            'group_name'    => ($data['group_name'] ?? null) ?: 'Pribadi',
            'activity_name' => $data['activity_name'],
            'category'      => 'General',
            'notes'         => $data['notes'] ?? null,
            'priority'      => $data['priority'],
            'event_date'    => $data['event_date'],
            'start_hour'    => $data['start_hour'],
            'end_hour'      => $data['end_hour'],
            // Sinkron ke kolom lama untuk kompatibilitas fitur existing
            'date'          => $data['event_date'],
            'time'          => sprintf('%02d:00', $data['start_hour']),
            'is_completed'  => false,
        ]);

        return response()->json([
            'success' => true,
            'event'   => $this->toEvent($schedule),
        ]);
    }

    /**
     * Update event existing (hanya pemilik atau admin).
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        if (!$this->canManage($schedule)) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $data = $this->validateEvent($request);

        $schedule->update([
            'activity_name' => $data['activity_name'],
            'notes'         => $data['notes'] ?? null,
            'priority'      => $data['priority'],
            'event_date'    => $data['event_date'],
            'start_hour'    => $data['start_hour'],
            'end_hour'      => $data['end_hour'],
            // Jaga konsistensi kolom lama
            'date'          => $data['event_date'],
            'time'          => sprintf('%02d:00', $data['start_hour']),
        ]);

        return response()->json([
            'success' => true,
            'event'   => $this->toEvent($schedule->fresh()),
        ]);
    }

    /**
     * Hapus event (hanya pemilik atau admin).
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);

        if (!$this->canManage($schedule)) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $schedule->delete();

        return response()->json(['success' => true]);
    }

    // ============================================================
    // Helpers
    // ============================================================

    /**
     * Validasi input dari modal + normalisasi jam mulai/selesai.
     */
    private function validateEvent(Request $request): array
    {
        $validated = $request->validate([
            'activity_name' => 'required|string|min:3|max:255',
            'event_date'    => 'required|date',
            'start_hour'    => 'required|integer|min:0|max:23',
            'end_hour'      => 'required|integer|min:0|max:23',
            'priority'      => 'required|in:low,med,high',
            'notes'         => 'nullable|string|max:1000',
            'group_name'    => 'nullable|string|max:255',
        ]);

        // Pastikan jam selesai minimal 1 jam setelah jam mulai.
        if ($validated['end_hour'] <= $validated['start_hour']) {
            $validated['end_hour'] = min($validated['start_hour'] + 1, 23);
        }

        return $validated;
    }

    /**
     * User boleh kelola jika pemilik event atau admin.
     */
    private function canManage(Schedule $schedule): bool
    {
        $user = auth()->user();
        return $schedule->user_id === $user->id || $user->role === 'admin';
    }

    /**
     * Ubah model Schedule menjadi struktur event ringkas untuk kalender.
     * Memakai fallback agar baris lama (yang mungkin event_date/start_hour
     * masih null) tetap tampil dengan benar.
     */
    private function toEvent(Schedule $s): array
    {
        // Tanggal: pakai event_date, fallback ke kolom `date` lama.
        $date = $s->event_date ?? $s->date;
        $date = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');

        // Jam mulai: pakai start_hour, fallback dari kolom `time`, default 9.
        $start = $s->start_hour;
        if ($start === null) {
            $start = $s->time ? (int) Carbon::parse($s->time)->format('H') : 9;
        }
        $start = max(0, min(23, (int) $start));

        // Jam selesai: pakai end_hour, fallback start+1 (maks 23).
        $end = $s->end_hour;
        if ($end === null || $end <= $start) {
            $end = min($start + 1, 23);
        }
        $end = max(0, min(23, (int) $end));

        $priority = $s->priority ?: 'med';

        return [
            'id'            => $s->id,
            'title'         => $s->activity_name,
            'date'          => $date,
            'start_hour'    => $start,
            'end_hour'      => $end,
            'priority'      => $priority,
            'color'         => self::PRIORITY_COLORS[$priority] ?? self::PRIORITY_COLORS['med'],
            'notes'         => $s->notes,
            'group_name'    => $s->group_name,
            'is_completed'  => (bool) $s->is_completed,
            'editable'      => $this->canManage($s),
        ];
    }
}
