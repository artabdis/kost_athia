<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RoomBookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_user_can_book_an_available_room_and_see_it_on_their_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->forceFill([
            'role' => 'user',
            'payment_status' => 'approved',
        ])->save();
        $room = $this->makeRoom();

        $this->actingAs($user)
            ->post(route('kamar.booking', $room))
            ->assertRedirect(route('payment.show'))
            ->assertSessionHas('success');

        $booking = Booking::query()->firstOrFail();
        $this->assertSame($user->id, $booking->user_id);
        $this->assertSame($room->id, $booking->room_id);
        $this->assertSame('pending', $booking->status);
        $this->assertSame('tersedia', $room->fresh()->status);
        $this->assertDatabaseHas('tagihans', [
            'user_id' => $user->id,
            'room_id' => $room->id,
            'nominal' => $room->harga,
            'status' => 'belum_bayar',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee($room->kode_kamar)
            ->assertSee('Menunggu konfirmasi');
    }

    public function test_a_room_with_an_active_booking_cannot_be_booked_by_another_user(): void
    {
        $room = $this->makeRoom();
        $firstUser = User::factory()->create(['role' => 'user']);
        $secondUser = User::factory()->create(['role' => 'user']);

        $this->actingAs($firstUser)
            ->post(route('kamar.booking', $room))
            ->assertRedirect(route('payment.show'));

        $this->actingAs($secondUser)
            ->post(route('kamar.booking', $room))
            ->assertRedirect(route('kamar'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $firstUser->id,
            'room_id' => $room->id,
        ]);

        $this->get(route('kamar.detail', $room->id))
            ->assertOk()
            ->assertSee('Kamar Sedang Dipesan');
    }

    public function test_repeating_the_same_users_booking_does_not_create_a_duplicate(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $user->forceFill(['role' => 'user'])->save();
        $room = $this->makeRoom();

        $this->actingAs($user)
            ->post(route('kamar.booking', $room))
            ->assertRedirect(route('payment.show'));

        $this->post(route('kamar.booking', $room))
            ->assertRedirect(route('payment.show'));

        $this->assertDatabaseCount('bookings', 1);
        $this->assertDatabaseCount('tagihans', 1);
    }

    public function test_guest_booking_selection_is_saved_when_the_guest_registers(): void
    {
        User::factory()->create()->forceFill(['role' => 'admin'])->save();
        $room = $this->makeRoom();

        $this->post(route('kamar.booking', $room))
            ->assertRedirect(route('register'))
            ->assertSessionHas('booking_room_id', $room->id);
        $this->get(route('register'))
            ->assertOk()
            ->assertSee($room->kode_kamar)
            ->assertSee('1.000.000');

        Carbon::setTestNow('2026-10-12 09:30:00');

        try {
            $response = $this->post(route('register.store'), [
                'name' => 'Penghuni Booking',
                'email' => 'booking@example.test',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

            $resident = User::query()->where('email', 'booking@example.test')->firstOrFail();
            $bill = Tagihan::query()->firstOrFail();
            $response->assertRedirect(route('tagihan.show', $bill));
            $this->assertDatabaseHas('bookings', [
                'user_id' => $resident->id,
                'room_id' => $room->id,
                'status' => 'pending',
            ]);
            $this->assertDatabaseHas('tagihans', [
                'id' => $bill->id,
                'user_id' => $resident->id,
                'room_id' => $room->id,
                'nominal' => $room->harga,
                'periode' => 'Oktober 2026',
                'jatuh_tempo' => '2026-10-10',
                'status' => 'belum_bayar',
            ]);
            $this->get(route('tagihan.show', $bill))
                ->assertOk()
                ->assertSee('Kamar')
                ->assertSee('Oktober 2026')
                ->assertSee('1.000.000')
                ->assertSee('Upload Bukti Baru');

            $this->post(route('logout'));
            $this->withSession(['booking_room_id' => $room->id])
                ->post(route('login.store'), [
                    'email' => 'booking@example.test',
                    'password' => 'password123',
                ])
                ->assertRedirect(route('tagihan.show', $bill));

            $this->assertDatabaseCount('bookings', 1);
            $this->assertDatabaseCount('tagihans', 1);
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_booking_for_a_room_marked_occupied_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $room = $this->makeRoom('terisi');

        $this->actingAs($user)
            ->post(route('kamar.booking', $room))
            ->assertRedirect(route('kamar'))
            ->assertSessionHas('error');

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_admin_cannot_book_a_room_as_a_resident(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $room = $this->makeRoom();

        $this->actingAs($admin)
            ->post(route('kamar.booking', $room))
            ->assertForbidden();

        $this->assertDatabaseCount('bookings', 0);
    }

    public function test_admin_dashboard_does_not_show_a_separate_booking_list(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $resident = User::factory()->create([
            'email' => 'tenant@example.test',
            'role' => 'user',
        ]);
        $room = $this->makeRoom();
        Booking::create([
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Booking Penghuni')
            ->assertDontSee('id="bookings"', false);
    }

    private function makeRoom(string $status = 'tersedia'): Room
    {
        return Room::create([
            'kode_kamar' => 'BOOKING-01',
            'nama' => 'Kamar Booking',
            'lokasi' => 'Lantai 1',
            'harga' => 1000000,
            'status' => $status,
        ]);
    }
}
