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
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('payment.show'))
            ->assertSessionHas('info');

        $this->get(route('payment.show'))
            ->assertOk()
            ->assertSee('Dashboard dapat diakses setelah admin memverifikasi pembayaran Anda.');
    }

    public function test_unverified_user_login_redirects_to_payment(): void
    {
        $user = User::factory()->create([
            'email' => 'unverified@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
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
        $user->forceFill([
            'role' => 'user',
            'payment_status' => 'approved',
        ])->save();

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
            ->assertSee('Verifikasi Pembayaran Awal')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee('Belum ada akun penghuni.');
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
            'kode_kamar' => 'DASH-STATS-A01',
            'nama' => 'Kamar DASH-STATS-A01',
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

        $resident = User::factory()->create(['role' => 'user']);
        $resident->forceFill(['payment_status' => 'approved'])->save();
        $pending = User::factory()->create(['role' => 'user']);
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
            ->assertSee('Kamar DASH-STATS-A01')
            ->assertSee('Kamar A02')
            ->assertSee($pending->name);
    }

    public function test_user_only_sees_bookings_connected_to_their_account(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->forceFill(['payment_status' => 'approved'])->save();
        $otherUser = User::factory()->create(['role' => 'user']);
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

        $user = User::factory()->create(['role' => 'user']);
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
        $this->assertDatabaseCount('tagihans', 0);
    }

    public function test_admin_initial_payment_table_only_offers_verification_for_submitted_proofs(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $unsubmitted = User::factory()->create([
            'name' => 'Belum Upload',
            'role' => 'user',
        ]);
        $submitted = User::factory()->create([
            'name' => 'Menunggu Verifikasi',
            'role' => 'user',
        ]);
        $submitted->forceFill([
            'payment_status' => 'submitted',
            'payment_proof' => 'payment-proofs/submitted.png',
        ])->save();
        Storage::disk('local')->put('payment-proofs/submitted.png', 'proof');

        $rejected = User::factory()->create([
            'name' => 'Pembayaran Ditolak',
            'role' => 'user',
        ]);
        $rejected->forceFill([
            'payment_status' => 'rejected',
            'payment_proof' => 'payment-proofs/rejected.png',
            'payment_note' => 'Bukti tidak terbaca.',
        ])->save();
        Storage::disk('local')->put('payment-proofs/rejected.png', 'proof');

        $approved = User::factory()->create([
            'name' => 'Pembayaran Disetujui',
            'role' => 'user',
        ]);
        $approved->forceFill([
            'payment_status' => 'approved',
            'payment_proof' => 'payment-proofs/approved.png',
        ])->save();
        Storage::disk('local')->put('payment-proofs/approved.png', 'proof');

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Belum mengunggah')
            ->assertSee('Menunggu verifikasi')
            ->assertSee('Ditolak')
            ->assertSee('Bukti tidak terbaca.')
            ->assertSee('Disetujui')
            ->assertSee('Sudah diproses')
            ->assertSee(route('admin.payment.proof', $submitted), false)
            ->assertSee(route('admin.payment.proof', $rejected), false)
            ->assertSee(route('admin.payment.proof', $approved), false)
            ->assertDontSee(route('admin.payment.proof', $unsubmitted), false);

        $html = $response->getContent();
        $submittedForm = 'action="'.route('admin.payment.verify', $submitted).'"';
        $approvedForm = 'action="'.route('admin.payment.verify', $approved).'"';
        $unsubmittedForm = 'action="'.route('admin.payment.verify', $unsubmitted).'"';

        $this->assertStringContainsString($submittedForm, $html);
        $this->assertStringNotContainsString($approvedForm, $html);
        $this->assertStringNotContainsString($unsubmittedForm, $html);
    }

    public function test_approved_resident_session_can_refresh_dashboard_after_database_status_changes(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $resident = User::factory()->create(['role' => 'user']);
        $resident->forceFill([
            'payment_status' => 'submitted',
            'payment_proof' => 'payment-proofs/session-proof.png',
        ])->save();
        Storage::disk('local')->put('payment-proofs/session-proof.png', 'proof');

        $this->actingAs($admin)
            ->post(route('admin.payment.verify', $resident), ['status' => 'approved'])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Selamat datang')
            ->assertSee('Disetujui');

        $this->assertAuthenticatedAs($resident);
        $this->get(route('payment.show'))->assertRedirect(route('dashboard'));

        $this->post(route('logout'))
            ->assertRedirect(route('login'));
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('login.store'), [
            'email' => $resident->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))->assertOk();
    }

    public function test_rejected_initial_payment_can_be_uploaded_again_without_changing_rent_bills(): void
    {
        Storage::fake('local');
        $user = User::factory()->create(['role' => 'user']);
        $user->forceFill([
            'payment_status' => 'rejected',
            'payment_proof' => 'payment-proofs/old.png',
            'payment_note' => 'Bukti tidak jelas.',
        ])->save();
        Storage::disk('local')->put('payment-proofs/old.png', 'old proof');

        $this->actingAs($user)
            ->get(route('payment.show'))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Bukti tidak jelas.')
            ->assertSee('Kirim Bukti Pembayaran');

        $this->post(route('payment.upload'), [
            'payment_proof' => UploadedFile::fake()->image('new-proof.png')->size(100),
        ])->assertRedirect(route('payment.show'));

        $user->refresh();
        $this->assertSame('submitted', $user->payment_status);
        $this->assertNull($user->payment_note);
        Storage::disk('local')->assertExists($user->payment_proof);
        Storage::disk('local')->assertMissing('payment-proofs/old.png');
        $this->assertDatabaseCount('tagihans', 0);
    }

    public function test_admin_must_provide_a_reason_when_rejecting_payment(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create(['role' => 'user']);
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
        $user = User::factory()->create(['role' => 'user']);
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
        $user = User::factory()->create(['role' => 'user']);

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
        $user = User::factory()->create(['role' => 'user']);
        $payer = User::factory()->create(['role' => 'user']);
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
        $user = User::factory()->create(['role' => 'user']);
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
