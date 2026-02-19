<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk membuat tabel.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('user_name')->default('Guest'); // Memberi default agar tidak error jika kosong
            $table->string('group_name');
            $table->string('activity_name');
            $table->string('category')->default('General');
            $table->string('priority')->default('med'); // Menambahkan kolom priority untuk Android
            $table->date('date');
            $table->time('time')->nullable();
            $table->boolean('is_completed')->default(false); // Penting untuk fitur Checklist/Verifikasi
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi (Hapus tabel).
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};