<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'notes')) {
                $table->text('notes')->nullable()->after('category');
            }
            if (!Schema::hasColumn('schedules', 'priority')) {
                $table->string('priority')->default('med')->after('category');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'avatar_color')) {
                $table->string('avatar_color')->default('#1E88E5')->after('email');
            }
            if (!Schema::hasColumn('users', 'bio')) {
                $table->string('bio')->nullable()->after('avatar_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['notes']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_color', 'bio']);
        });
    }
};
