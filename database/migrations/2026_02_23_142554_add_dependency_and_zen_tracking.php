<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('dependency_id')->nullable()->constrained('schedules')->onDelete('set null');
        });

        Schema::create('zen_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->integer('duration_minutes');
            $table->date('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zen_sessions');
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['dependency_id']);
            $table->dropColumn('dependency_id');
        });
    }
};
