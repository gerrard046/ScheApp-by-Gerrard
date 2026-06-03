<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'user_name',
        'activity_name',
        'category',
        'notes',
        'date',
        'time',
        'group_name',
        'priority',
        'is_completed',
        'completed_at',
        'is_verified',
        'proof_image',
        'attachment_file',
        'attachment_type',
        'dependency_id',
        // --- Kolom Time-Block baru ---
        'start_datetime',
        'end_datetime',
        'is_all_day',
        'color',
        'recurrence_rule',
        'google_event_id',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'is_all_day'     => 'boolean',
        'is_completed'   => 'boolean',
        'is_verified'    => 'boolean',
        'completed_at'   => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subTasks()
    {
        return $this->hasMany(SubTask::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function parentTask()
    {
        return $this->belongsTo(Schedule::class, 'dependency_id');
    }

    public function dependentTasks()
    {
        return $this->hasMany(Schedule::class, 'dependency_id');
    }

    // ─── FullCalendar Event Format ─────────────────────────────────────────────

    /**
     * Konversi record ke format event object yang dimengerti FullCalendar.js.
     * Warna diprioritaskan: custom color > status-based > priority-based.
     */
    public function toCalendarEvent(): array
    {
        $color = $this->resolveEventColor();

        // Fallback ke kolom date+time lama jika start_datetime belum di-populate
        $startDt = $this->start_datetime
            ?? Carbon::parse($this->date . ' ' . ($this->time ?? '09:00:00'));
        $endDt = $this->end_datetime
            ?? (clone $startDt)->addHour();

        return [
            'id'              => $this->id,
            'title'           => $this->activity_name,
            'start'           => $startDt->toIso8601String(),
            'end'             => $endDt->toIso8601String(),
            'allDay'          => $this->is_all_day,
            'backgroundColor' => $color,
            'borderColor'     => $color,
            'textColor'       => '#ffffff',
            'classNames'      => array_filter([
                'fc-event-arctic',
                $this->is_completed ? 'event-completed' : null,
            ]),
            'extendedProps'   => [
                'category'        => $this->category,
                'priority'        => $this->priority,
                'notes'           => $this->notes,
                'is_completed'    => (bool) $this->is_completed,
                'is_verified'     => (bool) $this->is_verified,
                'group_id'        => $this->group_id,
                'group_name'      => $this->group?->name,
                'recurrence_rule' => $this->recurrence_rule,
                'google_event_id' => $this->google_event_id,
                'color'           => $this->color,
            ],
        ];
    }

    private function resolveEventColor(): string
    {
        // Urutan prioritas: custom color → early-completed → on-time → missed → priority
        if ($this->color) {
            return $this->color;
        }

        if ($this->is_completed) {
            if ($this->completed_at && $this->start_datetime
                && $this->completed_at->lt($this->start_datetime->addHours(1))) {
                return '#F59E0B'; // gold — selesai lebih awal
            }
            return '#10B981'; // hijau — selesai
        }

        if ($this->end_datetime && $this->end_datetime->isPast()) {
            return '#EF4444'; // merah — terlewat
        }

        return match ($this->priority) {
            'high'  => '#EF4444',
            'med'   => '#F59E0B',
            default => '#6366F1', // ungu — default
        };
    }
}
