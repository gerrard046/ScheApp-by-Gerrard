<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthResponse()
    {
        $user = User::factory()->create();
        return $this->actingAs($user)->get('/schedules');
    }

    private function getGuestResponse()
    {
        // Login page tidak butuh auth
        return $this->get('/login');
    }

    // ── Security Headers ─────────────────────────────────────────────────────

    public function test_x_frame_options_header_is_present(): void
    {
        $response = $this->getGuestResponse();
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_x_content_type_options_header_is_present(): void
    {
        $response = $this->getGuestResponse();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_xss_protection_header_is_present(): void
    {
        $response = $this->getGuestResponse();
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    public function test_referrer_policy_header_is_present(): void
    {
        $response = $this->getGuestResponse();
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_csp_header_is_present(): void
    {
        $response = $this->getGuestResponse();
        $this->assertNotEmpty($response->headers->get('Content-Security-Policy'));
    }

    public function test_permissions_policy_header_is_present(): void
    {
        $response = $this->getGuestResponse();
        $this->assertNotEmpty($response->headers->get('Permissions-Policy'));
    }

    public function test_server_header_is_removed(): void
    {
        $response = $this->getGuestResponse();
        $this->assertNull($response->headers->get('Server'));
    }

    public function test_x_powered_by_header_is_removed(): void
    {
        $response = $this->getGuestResponse();
        $this->assertNull($response->headers->get('X-Powered-By'));
    }

    // ── Cache Control ────────────────────────────────────────────────────────

    public function test_html_responses_have_no_store_cache_control(): void
    {
        $response = $this->getAuthResponse();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
    }

    public function test_login_page_has_no_store_cache_control(): void
    {
        $response = $this->getGuestResponse();
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
    }

    // ── CSP Content ──────────────────────────────────────────────────────────

    public function test_csp_blocks_external_frames(): void
    {
        $response = $this->getGuestResponse();
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);
    }

    public function test_csp_restricts_form_action_to_self(): void
    {
        $response = $this->getGuestResponse();
        $csp = $response->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("form-action 'self'", $csp);
    }
}
