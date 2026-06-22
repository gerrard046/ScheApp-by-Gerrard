<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleTest extends TestCase
{
    use RefreshDatabase;

    // ── Akses ────────────────────────────────────────────────────────────────

    public function test_authenticated_user_can_view_schedules(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/schedules')->assertOk();
    }

    public function test_guest_cannot_view_schedules(): void
    {
        $this->get('/schedules')->assertRedirect('/login');
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function test_user_can_create_schedule(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/schedules', [
            'activity_name' => 'Belajar Laravel',
            'date'          => now()->format('Y-m-d'),
            'time'          => '09:00',
            'category'      => 'Belajar',
            'priority'      => 'high',
            'group_name'    => 'Personal',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'user_id'       => $user->id,
            'activity_name' => 'Belajar Laravel',
        ]);
    }

    // ── Delete ───────────────────────────────────────────────────────────────

    public function test_user_can_delete_own_schedule(): void
    {
        $user     = User::factory()->create();
        $schedule = Schedule::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/schedules/{$schedule->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }

    public function test_user_cannot_delete_another_users_schedule(): void
    {
        $owner   = User::factory()->create();
        $attacker = User::factory()->create();
        $schedule = Schedule::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($attacker)->delete("/schedules/{$schedule->id}");

        // Harus ditolak — 403 atau redirect dengan error
        $this->assertTrue(
            $response->status() === 403 || $response->isRedirect(),
        );
        $this->assertDatabaseHas('schedules', ['id' => $schedule->id]);
    }

    public function test_admin_can_delete_any_schedule(): void
    {
        $admin    = User::factory()->admin()->create();
        $owner    = User::factory()->create();
        $schedule = Schedule::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($admin)->delete("/schedules/{$schedule->id}")->assertRedirect();
        $this->assertDatabaseMissing('schedules', ['id' => $schedule->id]);
    }

    // ── Calendar API ─────────────────────────────────────────────────────────

    public function test_calendar_events_only_returns_own_schedules(): void
    {
        $user  = User::factory()->create();
        $other = User::factory()->create();

        Schedule::factory()->create([
            'user_id'       => $user->id,
            'activity_name' => 'Jadwal Saya',
        ]);
        Schedule::factory()->create([
            'user_id'       => $other->id,
            'activity_name' => 'Jadwal Orang Lain',
        ]);

        $start = now()->startOfWeek()->toIso8601String();
        $end   = now()->endOfWeek()->toIso8601String();

        $response = $this->actingAs($user)
            ->getJson("/calendar/events?start={$start}&end={$end}");

        $response->assertOk();
        $titles = collect($response->json())->pluck('title');
        $this->assertTrue($titles->contains('Jadwal Saya'));
        $this->assertFalse($titles->contains('Jadwal Orang Lain'));
    }

    public function test_calendar_store_creates_schedule(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/calendar/events', [
            'title'          => 'Meeting Pagi',
            'start_datetime' => now()->addDay()->toIso8601String(),
            'end_datetime'   => now()->addDay()->addHour()->toIso8601String(),
            'is_all_day'     => false,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('schedules', [
            'user_id'       => $user->id,
            'activity_name' => 'Meeting Pagi',
        ]);
    }

    public function test_calendar_update_rejects_other_users_schedule(): void
    {
        $owner   = User::factory()->create();
        $attacker = User::factory()->create();
        $schedule = Schedule::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($attacker)->patchJson("/calendar/events/{$schedule->id}", [
            'start_datetime' => now()->toIso8601String(),
            'end_datetime'   => now()->addHour()->toIso8601String(),
        ]);

        $response->assertForbidden();
    }
}
