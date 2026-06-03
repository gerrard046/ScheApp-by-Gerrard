<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dateTime('start_datetime')->nullable()->after('time');
            $table->dateTime('end_datetime')->nullable()->after('start_datetime');
            $table->boolean('is_all_day')->default(false)->after('end_datetime');
            // Hex color untuk blok kalender (#RRGGBB)
            $table->char('color', 7)->nullable()->after('is_all_day');
            // RRULE string (RFC 5545), contoh: FREQ=WEEKLY;BYDAY=MO,WE,FR
            $table->text('recurrence_rule')->nullable()->after('color');
            $table->string('google_event_id', 255)->nullable()->after('recurrence_rule');
        });

        // Migrasi data lama: populasi start/end dari kolom date + time yang sudah ada
        DB::table('schedules')->whereNotNull('date')->orderBy('id')->chunk(200, function ($rows) {
            foreach ($rows as $row) {
                $timeStr = $row->time ?? '09:00:00';
                try {
                    $start = Carbon::createFromFormat('Y-m-d H:i:s', $row->date . ' ' . $timeStr);
                } catch (\Throwable) {
                    $start = Carbon::parse($row->date)->setTimeFromTimeString('09:00:00');
                }
                $end = (clone $start)->addHour();

                DB::table('schedules')->where('id', $row->id)->update([
                    'start_datetime' => $start->format('Y-m-d H:i:s'),
                    'end_datetime'   => $end->format('Y-m-d H:i:s'),
                ]);
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->index(['start_datetime', 'end_datetime'], 'schedules_datetime_range_idx');
            $table->index('google_event_id', 'schedules_google_event_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('schedules_datetime_range_idx');
            $table->dropIndex('schedules_google_event_id_idx');
            $table->dropColumn([
                'start_datetime', 'end_datetime', 'is_all_day',
                'color', 'recurrence_rule', 'google_event_id',
            ]);
        });
    }
};
