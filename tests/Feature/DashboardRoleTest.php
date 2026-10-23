<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DashboardRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_unverified_user_is_kept_on_the_payment_page_until_approval(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('payment.show'))
            ->assertSessionHas('info');

        $this->get(route('payment.show'))
            ->assertOk()
            ->assertSee('Dashboard dapat diakses setelah pembayaran disetujui');
    }

    public function test_unverified_user_login_redirects_to_payment(): void
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->post(route('login.store'), [
            'email' => 'unverified@example.com',
            'password' => 'password123',
        ])->assertRedirect(route('payment.show'));

        $this->get(route('dashboard'))
            ->assertRedirect(route('payment.show'));
    }

    public function test_verified_user_can_open_the_shared_dashboard_route(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['payment_status' => 'approved'])->save();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Selamat datang')
            ->assertSee('Penghuni')
            ->assertDontSee('Dashboard Administrator');
    }

    public function test_admin_sees_admin_dashboard_on_the_shared_dashboard_route(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Administrator')
            ->assertSee('Pembayaran terbaru')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Belum ada bukti pembayaran.');
    }

    public function test_admin_dashboard_requires_opening_room_management_for_crud_actions(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $room = Room::create([
            'kode_kamar' => 'DASH-ACTION-01',
            'nama' => 'Kamar DASH-ACTION-01',
            'lokasi' => 'Lantai 1',
            'harga' => 1500000,
            'status' => 'tersedia',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kelola Data Kamar')
            ->assertSee($room->kode_kamar)
            ->assertSee('Kelola Data Penghuni')
            ->assertSee('/admin/residents', false)
            ->assertDontSee(route('admin.rooms.create'), false)
            ->assertDontSee(route('admin.rooms.edit', $room), false)
            ->assertDontSee(route('admin.rooms.destroy', $room), false);
    }

    public function test_admin_dashboard_statistics_use_database_records(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        Room::create([
            'kode_kamar' => 'A01',
            'nama' => 'Kamar A01',
            'lokasi' => 'Lantai 1',
            'harga' => 1500000,
            'status' => 'tersedia',
        ]);
        Room::create([
            'kode_kamar' => 'A02',
            'nama' => 'Kamar A02',
            'lokasi' => 'Lantai 1',
            'harga' => 1500000,
            'status' => 'terisi',
        ]);

        $resident = User::factory()->create();
        $resident->forceFill(['payment_status' => 'approved'])->save();
        $pending = User::factory()->create();
        $pending->forceFill([
            'payment_status' => 'submitted',
            'payment_proof' => 'payment-proofs/receipt.png',
        ])->save();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Kamar')
            ->assertSee('Kamar Tersedia')
            ->assertSee('Penghuni Terverifikasi')
            ->assertSee('Kamar A01')
            ->assertSee('Kamar A02')
            ->assertSee($pending->name);
    }

    public function test_user_only_sees_bookings_connected_to_their_account(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $room = Room::create([
            'kode_kamar' => 'U01',
            'nama' => 'Kamar Milik Saya',
            'lokasi' => 'Lantai 1',
            'harga' => 1200000,
            'status' => 'tersedia',
        ]);
        $otherRoom = Room::create([
            'kode_kamar' => 'U02',
            'nama' => 'Kamar Rahasia',
            'lokasi' => 'Lantai 2',
            'harga' => 1300000,
            'status' => 'tersedia',
        ]);
        Booking::create(['user_id' => $user->id, 'room_id' => $room->id, 'status' => 'pending']);
        Booking::create(['user_id' => $otherUser->id, 'room_id' => $otherRoom->id, 'status' => 'pending']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Kamar Milik Saya')
            ->assertDontSee('Kamar Rahasia');
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

    public function test_admin_must_provide_a_reason_when_rejecting_payment(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();
        $user->forceFill([
            'payment_status' => 'submitted',
            'payment_proof' => 'payment-proofs/receipt.png',
        ])->save();

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->post(route('admin.payment.verify', $user), ['status' => 'rejected'])
            ->assertSessionHasErrors('note');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'payment_status' => 'submitted',
        ]);

        $this->post(route('admin.payment.verify', $user), [
            'status' => 'rejected',
            'note' => 'Bukti tidak terbaca.',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'payment_status' => 'rejected',
            'payment_note' => 'Bukti tidak terbaca.',
        ]);
    }

    public function test_admin_can_still_view_payment_proof_after_verification(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('payment-proofs/receipt.png', 'proof');
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();
        $user->forceFill([
            'payment_status' => 'approved',
            'payment_proof' => 'payment-proofs/receipt.png',
        ])->save();

        $this->actingAs($admin)
            ->get(route('admin.payment.proof', $user))
            ->assertOk();
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

    public function test_already_approved_payment_cannot_be_verified_again(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();
        $user->forceFill([
            'payment_status' => 'approved',
            'payment_proof' => 'payment-proofs/receipt.png',
        ])->save();

        $this->actingAs($admin)
            ->post(route('admin.payment.verify', $user), ['status' => 'approved'])
            ->assertNotFound();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'payment_status' => 'approved',
        ]);
    }

    public function test_first_registered_account_is_admin_and_later_accounts_are_users(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Admin Pertama',
            'email' => 'first-admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $firstUser = User::where('email', 'first-admin@example.com')->firstOrFail();
        $this->assertSame('admin', $firstUser->role);

        $this->post(route('register.store'), [
            'name' => 'Pengguna Kedua',
            'email' => 'second-user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ])->assertRedirect(route('payment.show'));

        $secondUser = User::where('email', 'second-user@example.com')->firstOrFail();
        $this->assertSame('user', $secondUser->role);
    }
}
