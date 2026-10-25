<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_room_listing_and_details_without_logging_in(): void
    {
        $this->get('/kamar')->assertOk();
        $this->get('/kamar/vip')
            ->assertOk()
            ->assertSee('Buat akun untuk booking');
        $this->get('/kamar/A01')
            ->assertOk()
            ->assertSee('Kamar A01')
            ->assertDontSee('photo-1598928506311');
        $this->get('/kamar/B01')->assertOk()->assertSee('Kamar B01');
        $this->get('/kamar/B02')->assertOk()->assertSee('Kamar B02');
    }

    public function test_room_catalog_detail_links_target_the_matching_database_records(): void
    {
        $roomA01 = Room::query()->where('kode_kamar', 'A01')->firstOrFail();
        $roomB01 = Room::query()->where('kode_kamar', 'B01')->firstOrFail();
        $roomB02 = Room::query()->where('kode_kamar', 'B02')->firstOrFail();

        $this->get(route('kamar'))
            ->assertOk()
            ->assertSee(route('kamar.detail', $roomA01->id))
            ->assertSee(route('kamar.detail', $roomB01->id))
            ->assertSee(route('kamar.detail', $roomB02->id));
    }

    public function test_numeric_room_detail_uses_database_record_and_its_booking_route(): void
    {
        $room = Room::create([
            'kode_kamar' => 'Q01',
            'nama' => 'Kamar Q01 Database',
            'lokasi' => 'Lantai 1',
            'harga' => 800000,
            'status' => 'tersedia',
        ]);

        $this->actingAs(User::factory()->create())
            ->get(route('kamar.detail', $room->id))
            ->assertOk()
            ->assertSee('Kamar Q01 Database')
            ->assertSee('Rp800.000')
            ->assertSee(route('kamar.booking', $room->id));
    }

    public function test_unconfirmed_catalog_room_cannot_be_booked(): void
    {
        $room = Room::query()->where('kode_kamar', 'A01')->firstOrFail();
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('kamar.booking', $room->id))
            ->assertRedirect(route('kamar'))
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('bookings', [
            'user_id' => $user->id,
            'room_id' => $room->id,
        ]);
    }

    public function test_guest_is_redirected_to_login_when_booking_a_room(): void
    {
        $this->post('/kamar/vip/booking')
            ->assertRedirect(route('register'))
            ->assertSessionHas('booking_room_id');
    }
}
