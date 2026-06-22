<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ── Login ────────────────────────────────────────────────────────────────

    public function test_user_can_login_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/schedules');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_error_message_is_generic(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpass',
        ]);

        // Pesan error tidak boleh menyebut "password" atau "email" secara spesifik
        $errors = $response->getSession()->get('errors')->getBag('default');
        $this->assertStringContainsString('salah', strtolower($errors->first('email')));
    }

    public function test_login_does_not_repopulate_password_field(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrongpass',
        ]);

        // onlyInput('email') — pastikan password tidak ada di old input
        $response->assertSessionMissing('_old_input.password');
    }

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $this->get('/schedules')->assertRedirect('/login');
        $this->get('/calendar')->assertRedirect('/login');
        $this->get('/wallet')->assertRedirect('/login');
    }

    public function test_authenticated_user_is_redirected_from_login_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/login')->assertRedirect();
    }

    // ── Logout ───────────────────────────────────────────────────────────────

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    // ── Register ─────────────────────────────────────────────────────────────

    public function test_user_can_register_with_valid_data(): void
    {
        // Stub HIBP API agar uncompromised() tidak hit network
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('NOTREAL000000000000000000000000000000:1', 200)]);

        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'newuser@example.com',
            'password'              => 'StrongP@ss1!',
            'password_confirmation' => 'StrongP@ss1!',
        ]);

        $response->assertRedirect('/schedules');
        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_register_rejects_simple_password(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Test User',
            'email'                 => 'user@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'user@example.com']);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        Http::fake(['api.pwnedpasswords.com/*' => Http::response('NOTREAL:1', 200)]);

        $response = $this->post('/register', [
            'name'                  => 'Another User',
            'email'                 => 'taken@example.com',
            'password'              => 'StrongP@ss1!',
            'password_confirmation' => 'StrongP@ss1!',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_first_registered_user_becomes_admin(): void
    {
        Http::fake(['api.pwnedpasswords.com/*' => Http::response('NOTREAL:1', 200)]);

        $this->post('/register', [
            'name'                  => 'First User',
            'email'                 => 'first@example.com',
            'password'              => 'StrongP@ss1!',
            'password_confirmation' => 'StrongP@ss1!',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'first@example.com', 'role' => 'admin']);
    }
}
