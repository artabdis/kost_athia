<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_sees_user_dashboard_on_the_shared_dashboard_route(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('payment.show'));

        $this->get(route('payment.show'))
            ->assertOk()
            ->assertSee('Dashboard dapat dibuka setelah admin memverifikasi pembayaran Anda.');
    }

    public function test_verified_user_can_open_the_shared_dashboard_route(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['payment_status' => 'approved'])->save();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Pengguna')
            ->assertDontSee('Dashboard Administrator');
    }

    public function test_admin_sees_admin_dashboard_on_the_shared_dashboard_route(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Administrator')
            ->assertSee('Verifikasi Pembayaran')
            ->assertSee('Belum ada pembayaran yang menunggu verifikasi.');
    }

    public function test_admin_can_approve_a_submitted_payment(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $user = User::factory()->create();
        $user->forceFill([
            'payment_status' => 'submitted',
            'payment_proof' => 'payment-proofs/receipt.png',
        ])->save();

        $this->actingAs($admin)
            ->post(route('admin.payment.verify', $user), ['status' => 'approved'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'payment_status' => 'approved',
        ]);
    }

    public function test_user_can_submit_payment_proof_for_review(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('payment.upload'), [
                'payment_proof' => UploadedFile::fake()->image('receipt.png'),
            ])
            ->assertRedirect(route('payment.show'));

        $user->refresh();
        $this->assertSame('submitted', $user->payment_status);
        Storage::disk('local')->assertExists($user->payment_proof);
    }

    public function test_non_admin_cannot_verify_a_payment(): void
    {
        $user = User::factory()->create();
        $payer = User::factory()->create();
        $payer->forceFill([
            'payment_status' => 'submitted',
            'payment_proof' => 'payment-proofs/receipt.png',
        ])->save();

        $this->actingAs($user)
            ->post(route('admin.payment.verify', $payer), ['status' => 'approved'])
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $payer->id,
            'payment_status' => 'submitted',
        ]);
    }

    public function test_first_registered_account_is_admin_and_later_accounts_are_users(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Admin Pertama',
            'email' => 'first-admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('login'));

        $firstUser = User::where('email', 'first-admin@example.com')->firstOrFail();
        $this->assertSame('admin', $firstUser->role);

        $this->post(route('register.store'), [
            'name' => 'Pengguna Kedua',
            'email' => 'second-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertRedirect(route('login'));

        $secondUser = User::where('email', 'second-user@example.com')->firstOrFail();
        $this->assertSame('user', $secondUser->role);
    }
}
