<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TagihanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_bill_for_an_eligible_resident_and_it_appears_on_their_dashboard(): void
    {
        $admin = $this->makeAdmin();
        [$resident, $room] = $this->makeEligibleResident();

        $this->actingAs($admin)
            ->get(route('admin.tagihan.create'))
            ->assertOk()
            ->assertSee($resident->email)
            ->assertSee($room->kode_kamar);

        $this->post(route('admin.tagihan.store'), [
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'jenis_pembayaran' => 'Sewa Kamar',
            'periode' => '2026-10',
            'nominal' => 1250000,
            'jatuh_tempo' => '2026-10-10',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $bill = Tagihan::query()->firstOrFail();
        $this->assertDatabaseHas('tagihans', [
            'id' => $bill->id,
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'jenis_pembayaran' => 'Sewa Kamar',
            'periode' => 'Oktober 2026',
            'nominal' => 1250000,
            'status' => 'belum_bayar',
        ]);

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Oktober 2026')
            ->assertSee('<td>#'.$bill->id.'</td>', false);
    }

    public function test_resident_cannot_open_or_submit_admin_bill_creation_routes(): void
    {
        $resident = User::factory()->create();

        $this->actingAs($resident)
            ->get(route('admin.tagihan.create'))
            ->assertForbidden();

        $this->post(route('admin.tagihan.store'), [])
            ->assertForbidden();

        $this->assertDatabaseCount('tagihans', 0);
    }

    public function test_admin_dashboard_displays_all_bills_and_their_statuses(): void
    {
        $admin = $this->makeAdmin();
        $lastBill = null;

        foreach (range(1, 11) as $number) {
            $resident = User::factory()->create([
                'email' => "resident{$number}@example.test",
            ]);
            $room = $this->makeRoom(sprintf('BILL-%02d', $number), 'terisi');
            $statuses = ['belum_bayar', 'menunggu_verifikasi', 'ditolak', 'lunas'];
            $lastBill = $this->makeBill($resident, $room, [
                'status' => $statuses[($number - 1) % count($statuses)],
            ]);
        }

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<td>#'.$lastBill->id.'</td>', false)
            ->assertSee('resident11@example.test')
            ->assertSee('Menunggu verifikasi')
            ->assertSee('Status Penghuni')
            ->assertSee('Menunggu persetujuan')
            ->assertSee('Ditolak')
            ->assertSee('Catatan Admin')
            ->assertSee('Lunas')
            ->assertSee(route('admin.tagihan.create'));
    }

    public function test_admin_cannot_create_a_duplicate_bill_for_the_same_resident_type_and_period(): void
    {
        $admin = $this->makeAdmin();
        [$resident, $room] = $this->makeEligibleResident();
        $this->makeBill($resident, $room);

        $this->actingAs($admin)
            ->from(route('admin.tagihan.create'))
            ->post(route('admin.tagihan.store'), [
                'user_id' => $resident->id,
                'room_id' => $room->id,
                'jenis_pembayaran' => 'Sewa Kamar',
                'periode' => '2026-10',
                'nominal' => 1250000,
                'jatuh_tempo' => '2026-10-10',
            ])
            ->assertSessionHasErrors('periode');

        $this->assertDatabaseCount('tagihans', 1);
    }

    public function test_resident_dashboard_shows_only_their_own_bills(): void
    {
        [$resident, $room] = $this->makeEligibleResident();
        [$otherResident, $otherRoom] = $this->makeEligibleResident('other@example.test');
        $ownBill = $this->makeBill($resident, $room, ['periode' => 'Oktober 2026']);
        $otherBill = $this->makeBill($otherResident, $otherRoom, ['periode' => 'November 2026']);

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<td>#'.$ownBill->id.'</td>', false)
            ->assertSee('Oktober 2026')
            ->assertDontSee('<td>#'.$otherBill->id.'</td>', false)
            ->assertDontSee('November 2026');
    }

    public function test_resident_can_view_their_own_bill_but_not_another_residents_bill(): void
    {
        [$resident, $room] = $this->makeEligibleResident();
        [$otherResident, $otherRoom] = $this->makeEligibleResident('other@example.test');
        $ownBill = $this->makeBill($resident, $room);
        $otherBill = $this->makeBill($otherResident, $otherRoom);

        $this->actingAs($resident)
            ->get(route('tagihan.show', $ownBill))
            ->assertOk()
            ->assertSee('#'.$ownBill->id);

        $this->get(route('tagihan.show', $otherBill))->assertForbidden();
    }

    public function test_resident_can_upload_payment_proof_and_tagihan_waits_for_verification(): void
    {
        Storage::fake('local');
        [$resident, $room] = $this->makeEligibleResident();
        $bill = $this->makeBill($resident, $room);

        $this->actingAs($resident)
            ->post(route('tagihan.bayar', $bill), [
                'bukti_pembayaran' => UploadedFile::fake()->image('bukti.png')->size(100),
            ])
            ->assertRedirect(route('tagihan.show', $bill))
            ->assertSessionHas('success');

        $bill->refresh();
        $this->assertSame('menunggu_verifikasi', $bill->status);
        $this->assertNotEmpty($bill->bukti_pembayaran);
        Storage::disk('local')->assertExists($bill->bukti_pembayaran);

        $admin = $this->makeAdmin();
        $this->actingAs($admin)
            ->get(route('admin.tagihan.proof', $bill))
            ->assertOk();
    }

    public function test_payment_proof_upload_rejects_non_image_files(): void
    {
        Storage::fake('local');
        [$resident, $room] = $this->makeEligibleResident();
        $bill = $this->makeBill($resident, $room);

        $this->actingAs($resident)
            ->from(route('tagihan.show', $bill))
            ->post(route('tagihan.bayar', $bill), [
                'bukti_pembayaran' => UploadedFile::fake()->create('bukti.txt', 10, 'text/plain'),
            ])
            ->assertSessionHasErrors('bukti_pembayaran');

        $this->assertSame('belum_bayar', $bill->fresh()->status);
    }

    public function test_payment_proof_upload_rejects_files_larger_than_five_megabytes(): void
    {
        Storage::fake('local');
        [$resident, $room] = $this->makeEligibleResident();
        $bill = $this->makeBill($resident, $room);

        $this->actingAs($resident)
            ->from(route('tagihan.show', $bill))
            ->post(route('tagihan.bayar', $bill), [
                'bukti_pembayaran' => UploadedFile::fake()->image('bukti.png')->size(5121),
            ])
            ->assertSessionHasErrors('bukti_pembayaran');

        $this->assertSame('belum_bayar', $bill->fresh()->status);
    }

    public function test_approving_a_tagihan_also_approves_the_resident_but_rejection_does_not_revert_them(): void
    {
        Storage::fake('local');
        $admin = $this->makeAdmin();
        [$resident, $room] = $this->makeEligibleResident();
        $approvedBill = $this->makeBill($resident, $room, [
            'status' => 'menunggu_verifikasi',
            'bukti_pembayaran' => 'tagihan-proofs/approved.png',
        ]);
        $rejectedBill = $this->makeBill($resident, $room, [
            'periode' => 'November 2026',
            'status' => 'menunggu_verifikasi',
            'bukti_pembayaran' => 'tagihan-proofs/rejected.png',
        ]);
        Storage::disk('local')->put($approvedBill->bukti_pembayaran, 'proof');
        Storage::disk('local')->put($rejectedBill->bukti_pembayaran, 'proof');
        $resident->forceFill(['payment_status' => 'pending'])->save();

        $this->actingAs($admin)
            ->post(route('admin.tagihan.verify', $approvedBill), ['status' => 'lunas'])
            ->assertRedirect(route('dashboard'));

        $this->post(route('admin.tagihan.verify', $rejectedBill), [
            'status' => 'ditolak',
            'catatan' => 'Bukti tidak terbaca.',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tagihans', [
            'id' => $approvedBill->id,
            'status' => 'lunas',
        ]);
        $this->assertSame('approved', $resident->fresh()->payment_status);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'status' => 'approved',
        ]);
        $this->assertNotNull($approvedBill->fresh()->tanggal_bayar);
        $this->assertDatabaseHas('tagihans', [
            'id' => $rejectedBill->id,
            'status' => 'ditolak',
            'catatan' => 'Bukti tidak terbaca.',
        ]);
        $this->assertNull($rejectedBill->fresh()->tanggal_bayar);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Lunas')
            ->assertSee('Ditolak');

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Status penghuni')
            ->assertSee('Disetujui')
            ->assertSee('Lunas')
            ->assertSee('Ditolak');
    }

    public function test_rejecting_a_rent_bill_does_not_change_initial_payment_status(): void
    {
        $admin = $this->makeAdmin();
        [$resident, $room] = $this->makeEligibleResident();
        $resident->forceFill(['payment_status' => 'pending'])->save();
        $bill = $this->makeBill($resident, $room, [
            'status' => 'menunggu_verifikasi',
            'bukti_pembayaran' => 'tagihan-proofs/rejected-initial.png',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.tagihan.verify', $bill), [
                'status' => 'ditolak',
                'catatan' => 'Bukti tidak terbaca.',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertSame('ditolak', $bill->fresh()->status);
        $this->assertSame('pending', $resident->fresh()->payment_status);
        $this->assertSame('Bukti tidak terbaca.', $bill->fresh()->catatan);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);
    }

    public function test_registration_creates_a_room_bill_and_uses_the_bill_payment_flow(): void
    {
        Storage::fake('local');
        Carbon::setTestNow('2026-10-12 09:30:00');
        config(['tagihan.tanggal_jatuh_tempo' => 10]);
        $this->makeAdmin();
        $room = $this->makeRoom('REG-01', 'tersedia');

        try {
            $response = $this->withSession(['booking_room_id' => $room->id])
                ->post(route('register.store'), [
                    'name' => 'Penghuni Baru',
                    'email' => 'new-resident@example.test',
                    'password' => 'password123',
                    'password_confirmation' => 'password123',
                ]);

            $resident = User::query()->where('email', 'new-resident@example.test')->firstOrFail();
            $bill = Tagihan::query()->firstOrFail();
            $response->assertRedirect(route('tagihan.show', $bill));
            $this->assertSame('pending', $resident->payment_status);
            $this->assertSame(1, $resident->bookings()->count());
            $this->assertDatabaseHas('tagihans', [
                'id' => $bill->id,
                'user_id' => $resident->id,
                'room_id' => $room->id,
                'periode' => 'Oktober 2026',
                'nominal' => $room->harga,
                'jatuh_tempo' => '2026-10-10',
                'status' => 'belum_bayar',
            ]);
            $this->get(route('tagihan.show', $bill))
                ->assertOk()
                ->assertSee('Oktober 2026')
                ->assertSee('1.250.000')
                ->assertSee('Upload Bukti Baru');

            $this->post(route('tagihan.bayar', $bill), [
                'bukti_pembayaran' => UploadedFile::fake()->image('initial-proof.png')->size(100),
            ])->assertRedirect(route('tagihan.show', $bill));

            $resident->refresh();
            $this->assertSame('pending', $resident->payment_status);
            $this->assertDatabaseHas('tagihans', [
                'id' => $bill->id,
                'status' => 'menunggu_verifikasi',
            ]);
            $this->assertSame('2026-10-10', $bill->jatuh_tempo->toDateString());
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_resident_cannot_verify_a_bill(): void
    {
        [$resident, $room] = $this->makeEligibleResident();
        $bill = $this->makeBill($resident, $room, [
            'status' => 'menunggu_verifikasi',
            'bukti_pembayaran' => 'tagihan-proofs/pending.png',
        ]);

        $this->actingAs($resident)
            ->post(route('admin.tagihan.verify', $bill), [
                'status' => 'lunas',
            ])
            ->assertForbidden();

        $this->assertSame('menunggu_verifikasi', $bill->fresh()->status);
    }

    public function test_resident_can_upload_new_proof_after_rejection_and_sees_status_and_note_in_history(): void
    {
        Storage::fake('local');
        [$resident, $room] = $this->makeEligibleResident();
        $bill = $this->makeBill($resident, $room, [
            'status' => 'ditolak',
            'bukti_pembayaran' => 'tagihan-proofs/old.png',
            'catatan' => 'Bukti sebelumnya buram.',
        ]);
        Storage::disk('local')->put($bill->bukti_pembayaran, 'old proof');

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Bukti sebelumnya buram.');

        $this->actingAs($resident)
            ->post(route('tagihan.bayar', $bill), [
                'bukti_pembayaran' => UploadedFile::fake()->image('bukti-baru.webp')->size(100),
            ])
            ->assertRedirect(route('tagihan.show', $bill));

        $bill->refresh();
        $this->assertSame('menunggu_verifikasi', $bill->status);
        $this->assertNull($bill->catatan);
        Storage::disk('local')->assertExists('tagihan-proofs/old.png');
        Storage::disk('local')->assertExists($bill->bukti_pembayaran);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<td>#'.$bill->id.'</td>', false)
            ->assertSee('Sewa Kamar')
            ->assertSee('Oktober 2026')
            ->assertSee('Menunggu verifikasi')
            ->assertSee('1.250.000')
            ->assertDontSee('Periode tidak tersedia')
            ->assertDontSee('Tidak dicatat');
    }

    public function test_legacy_payment_verification_cannot_bypass_an_existing_tagihan(): void
    {
        Storage::fake('local');
        $admin = $this->makeAdmin();
        [$resident, $room] = $this->makeEligibleResident();
        $bill = $this->makeBill($resident, $room, [
            'status' => 'belum_bayar',
        ]);
        $resident->forceFill([
            'payment_status' => 'submitted',
            'payment_proof' => 'payment-proofs/initial-payment.png',
        ])->save();
        Storage::disk('local')->put('payment-proofs/initial-payment.png', 'proof');

        $this->actingAs($admin)
            ->post(route('admin.payment.verify', $resident), ['status' => 'approved'])
            ->assertStatus(409);

        $this->assertSame('submitted', $resident->fresh()->payment_status);
        $this->assertSame('belum_bayar', $bill->fresh()->status);
        $this->assertNull($bill->fresh()->tanggal_bayar);
    }

    public function test_resident_with_a_tagihan_is_redirected_to_its_payment_detail(): void
    {
        Storage::fake('local');
        [$resident, $room] = $this->makeEligibleResident();
        $resident->forceFill(['payment_status' => 'pending'])->save();
        $bill = $this->makeBill($resident, $room);

        $this->actingAs($resident)
            ->get(route('dashboard'))
            ->assertRedirect(route('payment.show'));

        $this->get(route('payment.show'))
            ->assertRedirect(route('tagihan.show', $bill));

        $this->post(route('payment.upload'), [
            'payment_proof' => UploadedFile::fake()->image('legacy-proof.png'),
        ])
            ->assertRedirect(route('tagihan.show', $bill));

        $this->assertSame('pending', $resident->fresh()->payment_status);
        $this->assertNull($resident->fresh()->payment_proof);
        $this->assertSame('belum_bayar', $bill->fresh()->status);
    }

    public function test_rejected_legacy_initial_payment_can_be_uploaded_again(): void
    {
        Storage::fake('local');
        [$resident, $room] = $this->makeEligibleResident();
        $resident->forceFill([
            'payment_status' => 'rejected',
            'payment_proof' => 'payment-proofs/old-initial.png',
            'payment_note' => 'Foto bukti terpotong.',
        ])->save();
        Storage::disk('local')->put('payment-proofs/old-initial.png', 'old proof');

        $this->actingAs($resident)
            ->get(route('payment.show'))
            ->assertOk()
            ->assertSee('Ditolak')
            ->assertSee('Foto bukti terpotong.');

        $this->post(route('payment.upload'), [
            'payment_proof' => UploadedFile::fake()->image('new-initial.png')->size(100),
        ])->assertRedirect(route('payment.show'));

        $resident->refresh();
        $this->assertSame('submitted', $resident->payment_status);
        $this->assertNull($resident->payment_note);
        Storage::disk('local')->assertExists($resident->payment_proof);
        Storage::disk('local')->assertMissing('payment-proofs/old-initial.png');
    }

    public function test_monthly_command_creates_one_bill_and_skips_unqualified_residents_and_duplicates(): void
    {
        Carbon::setTestNow('2026-10-01 00:05:00');
        config(['tagihan.tanggal_jatuh_tempo' => 10]);

        try {
            [$resident, $room] = $this->makeEligibleResident();
            [$unqualifiedResident, $unqualifiedRoom] = $this->makeEligibleResident(
                'unqualified@example.test',
                'tersedia'
            );
            Booking::query()
                ->where('user_id', $unqualifiedResident->id)
                ->update(['status' => 'pending']);
            $unqualifiedResident->forceFill(['payment_status' => 'pending'])->save();

            $this->artisan('tagihan:generate-bulanan')->assertExitCode(0);
            $this->artisan('tagihan:generate-bulanan')->assertExitCode(0);

            $this->assertDatabaseCount('tagihans', 1);
            $this->assertDatabaseHas('tagihans', [
                'user_id' => $resident->id,
                'room_id' => $room->id,
                'periode' => 'Oktober 2026',
                'nominal' => $room->harga,
                'status' => 'belum_bayar',
            ]);
            $this->assertSame(
                '2026-10-10',
                Tagihan::query()->firstOrFail()->jatuh_tempo->toDateString()
            );
            $this->assertDatabaseMissing('tagihans', ['user_id' => $unqualifiedResident->id]);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_monthly_command_skips_a_resident_with_multiple_eligible_bookings(): void
    {
        Carbon::setTestNow('2026-10-01 00:05:00');

        try {
            [$resident, $firstRoom] = $this->makeEligibleResident();
            $secondRoom = $this->makeRoom('TEST-02', 'terisi');
            Booking::create([
                'user_id' => $resident->id,
                'room_id' => $secondRoom->id,
                'status' => 'pending',
            ]);

            $this->artisan('tagihan:generate-bulanan')->assertExitCode(0);
            $this->assertDatabaseCount('tagihans', 0);
        } finally {
            Carbon::setTestNow();
        }
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        return $admin;
    }

    /**
     * @return array{0: User, 1: Room}
     */
    private function makeEligibleResident(string $email = 'resident@example.test', string $roomStatus = 'terisi'): array
    {
        $resident = User::factory()->create(['email' => $email]);
        $resident->forceFill([
            'role' => 'user',
            'payment_status' => 'approved',
            'payment_proof' => 'registration-proof.png',
        ])->save();
        $room = $this->makeRoom('TEST-'.substr(md5($email), 0, 6), $roomStatus);

        Booking::create([
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);

        return [$resident, $room];
    }

    private function makeRoom(string $code, string $status): Room
    {
        return Room::create([
            'kode_kamar' => $code,
            'nama' => 'Kamar '.$code,
            'lokasi' => 'Lantai 1',
            'harga' => 1250000,
            'status' => $status,
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeBill(User $resident, Room $room, array $attributes = []): Tagihan
    {
        return Tagihan::create(array_merge([
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'jenis_pembayaran' => 'Sewa Kamar',
            'periode' => 'Oktober 2026',
            'nominal' => 1250000,
            'jatuh_tempo' => '2026-10-10',
            'status' => 'belum_bayar',
        ], $attributes));
    }
}
