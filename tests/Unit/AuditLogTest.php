<?php

namespace Tests\Unit;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_record_creates_entry(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::record('test_event', 'Ini test', ['key' => 'value']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id'     => $user->id,
            'event'       => 'test_event',
            'description' => 'Ini test',
        ]);
    }

    public function test_audit_log_works_without_authenticated_user(): void
    {
        // Harus tidak throw meskipun tidak ada user yang login
        AuditLog::record('guest_event', 'Tanpa auth');

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => null,
            'event'   => 'guest_event',
        ]);
    }

    public function test_audit_log_does_not_throw_on_failure(): void
    {
        // record() harus catch Throwable — tidak boleh crash request utama
        $this->expectNotToPerformAssertions();

        // Panggil record() berkali-kali tanpa masalah
        AuditLog::record('event_1');
        AuditLog::record('event_2', null, ['extra' => 'data']);
        AuditLog::record('event_3', 'dengan deskripsi');
    }

    public function test_audit_log_stores_metadata_as_json(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AuditLog::record('login', 'Login berhasil', ['email' => 'test@mail.com', 'ip' => '127.0.0.1']);

        $log = AuditLog::where('event', 'login')->first();
        $this->assertIsArray($log->metadata);
        $this->assertEquals('test@mail.com', $log->metadata['email']);
    }

    public function test_audit_log_truncates_long_user_agent(): void
    {
        $longAgent = str_repeat('A', 600);
        request()->headers->set('User-Agent', $longAgent);

        AuditLog::record('test_ua');

        $log = AuditLog::where('event', 'test_ua')->first();
        $this->assertLessThanOrEqual(500, strlen($log->user_agent));
    }
}
