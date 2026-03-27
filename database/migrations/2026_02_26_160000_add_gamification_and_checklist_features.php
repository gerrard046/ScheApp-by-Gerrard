<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('schedules', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('is_completed');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'total_early_completions')) {
                $table->integer('total_early_completions')->default(0)->after('streak');
            }
            if (!Schema::hasColumn('users', 'badges')) {
                $table->json('badges')->nullable()->after('total_early_completions');
            }
            if (!Schema::hasColumn('users', 'title')) {
                $table->string('title')->default('Pemula')->after('level');
            }
            if (!Schema::hasColumn('users', 'combo_count')) {
                $table->integer('combo_count')->default(0)->after('streak');
            }
            if (!Schema::hasColumn('users', 'highest_combo')) {
                $table->integer('highest_combo')->default(0)->after('combo_count');
            }
            if (!Schema::hasColumn('users', 'total_xp_earned')) {
                $table->integer('total_xp_earned')->default(0)->after('xp');
            }
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['completed_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['total_early_completions', 'badges', 'title', 'combo_count', 'highest_combo', 'total_xp_earned']);
        });
    }
};
