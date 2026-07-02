<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbaiki kolom `group_name` yang semula NOT NULL tanpa default.
     *
     * Akibat lama: menyimpan jadwal pribadi (tanpa grup) gagal dengan error
     * "NOT NULL constraint failed: schedules.group_name" -> respons 500.
     * Solusi: jadikan nullable + default 'Pribadi' agar SEMUA jalur simpan
     * (form utama, API, sinkron Google, kalender) aman.
     */
    public function up(): void
    {
        // Isi dulu baris lama yang mungkin kosong agar konsisten.
        DB::table('schedules')->whereNull('group_name')->update(['group_name' => 'Pribadi']);

        Schema::table('schedules', function (Blueprint $table) {
            $table->string('group_name')->nullable()->default('Pribadi')->change();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->string('group_name')->nullable(false)->default(null)->change();
        });
    }
};
