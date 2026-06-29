<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah kolom khusus tampilan kalender Arctic Breeze.
     *
     * Tabel lama hanya punya `date` + `time`. Untuk grid jam (tampilan
     * Minggu/Hari) kita butuh tanggal & jam mulai/selesai yang terpisah.
     * Semua kolom dibuat nullable + ada backfill agar data lama tidak rusak.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // event_date = tanggal event (cermin dari kolom `date` yang lama)
            if (!Schema::hasColumn('schedules', 'event_date')) {
                $table->date('event_date')->nullable()->after('date');
            }
            // start_hour / end_hour = posisi blok di grid jam 0-23
            if (!Schema::hasColumn('schedules', 'start_hour')) {
                $table->tinyInteger('start_hour')->nullable()->after('event_date');
            }
            if (!Schema::hasColumn('schedules', 'end_hour')) {
                $table->tinyInteger('end_hour')->nullable()->after('start_hour');
            }
        });

        // --- Backfill data lama agar tidak rusak ---
        if (DB::getDriverName() === 'mysql') {
            // Jalur cepat untuk MySQL (target produksi)
            DB::statement("UPDATE schedules SET event_date = `date` WHERE event_date IS NULL");
            DB::statement("UPDATE schedules SET start_hour = COALESCE(HOUR(`time`), 9) WHERE start_hour IS NULL");
            DB::statement("UPDATE schedules SET end_hour = LEAST(start_hour + 1, 23) WHERE end_hour IS NULL");
        } else {
            // Jalur portable (mis. SQLite saat testing) - diproses per baris di PHP
            DB::table('schedules')->orderBy('id')->each(function ($row) {
                $start = 9;
                if (!empty($row->time)) {
                    $start = (int) substr($row->time, 0, 2);
                }
                DB::table('schedules')->where('id', $row->id)->update([
                    'event_date' => $row->event_date ?? $row->date,
                    'start_hour' => $row->start_hour ?? $start,
                    'end_hour'   => $row->end_hour ?? min($start + 1, 23),
                ]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['event_date', 'start_hour', 'end_hour']);
        });
    }
};
