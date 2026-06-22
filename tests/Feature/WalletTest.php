<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletTest extends TestCase
{
    use RefreshDatabase;

    // ── View ─────────────────────────────────────────────────────────────────

    public function test_user_can_view_wallet_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/wallet')->assertOk();
    }

    public function test_guest_cannot_access_wallet(): void
    {
        $this->get('/wallet')->assertRedirect('/login');
    }

    // ── Top Up ───────────────────────────────────────────────────────────────

    public function test_user_can_top_up_wallet(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/wallet/topup', [
            'amount'      => 50000,
            'description' => 'Uang jajan',
        ])->assertRedirect();

        $wallet = Wallet::where('user_id', $user->id)->first();
        $this->assertEquals(50000, $wallet->balance);
    }

    public function test_topup_minimum_amount_is_1000(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/wallet/topup', [
            'amount'      => 500,
            'description' => 'Terlalu kecil',
        ]);

        $response->assertSessionHasErrors('amount');
    }

    // ── Income ───────────────────────────────────────────────────────────────

    public function test_user_can_record_income(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/wallet/income', [
            'amount'      => 100000,
            'description' => 'Gaji',
            'category'    => 'Gaji',
        ])->assertRedirect();

        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type'    => 'income',
            'amount'  => 100000,
        ]);
    }

    // ── Expense ──────────────────────────────────────────────────────────────

    public function test_expense_deducts_balance(): void
    {
        $user   = User::factory()->create();
        $wallet = Wallet::create(['user_id' => $user->id, 'balance' => 100000]);

        $this->actingAs($user)->post('/wallet/expense', [
            'amount'      => 30000,
            'description' => 'Makan siang',
            'category'    => 'Makanan',
        ])->assertRedirect();

        $this->assertEquals(70000, $wallet->fresh()->balance);
    }

    public function test_expense_rejected_when_balance_insufficient(): void
    {
        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 5000]);

        $response = $this->actingAs($user)->post('/wallet/expense', [
            'amount'      => 50000,
            'description' => 'Terlalu mahal',
            'category'    => 'Lainnya',
        ]);

        $response->assertSessionHas('error');
    }

    // ── Delete ───────────────────────────────────────────────────────────────

    public function test_user_can_delete_own_transaction(): void
    {
        $user        = User::factory()->create();
        $wallet      = Wallet::create(['user_id' => $user->id, 'balance' => 50000]);
        $transaction = WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'income',
            'amount'      => 50000,
            'description' => 'Test income',
            'category'    => 'Lainnya',
        ]);

        $this->actingAs($user)->delete("/wallet/{$transaction->id}")->assertRedirect();
        $this->assertDatabaseMissing('wallet_transactions', ['id' => $transaction->id]);
    }

    public function test_user_cannot_delete_other_users_transaction(): void
    {
        $owner    = User::factory()->create();
        $attacker = User::factory()->create();

        Wallet::create(['user_id' => $owner->id, 'balance' => 0]);
        $transaction = WalletTransaction::create([
            'user_id'     => $owner->id,
            'type'        => 'income',
            'amount'      => 10000,
            'description' => 'Milik owner',
            'category'    => 'Lainnya',
        ]);

        // destroy() pakai ->where('user_id', $user->id)->firstOrFail()
        // jadi akan 404 jika attacker coba hapus punya orang lain
        $this->actingAs($attacker)
            ->delete("/wallet/{$transaction->id}")
            ->assertStatus(404);
    }
}
