<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_room_without_bookings(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->post(route('admin.rooms.store'), [
                'kode_kamar' => 'C01',
                'nama' => 'Kamar C01',
                'lokasi' => 'Lantai 3',
                'harga' => 1250000,
                'status' => 'tersedia',
            ])
            ->assertRedirect(route('admin.rooms.index'));

        $room = Room::query()->where('kode_kamar', 'C01')->firstOrFail();

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'harga' => 1250000,
        ]);

        $this->put(route('admin.rooms.update', $room), [
            'kode_kamar' => 'C01',
            'nama' => 'Kamar C01 Diperbarui',
            'lokasi' => 'Lantai 3 Barat',
            'harga' => 1300000,
            'status' => 'terisi',
        ])->assertRedirect(route('admin.rooms.index'));

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'kode_kamar' => 'C01',
            'nama' => 'Kamar C01 Diperbarui',
            'lokasi' => 'Lantai 3 Barat',
            'harga' => 1300000,
            'status' => 'terisi',
        ]);

        $this->delete(route('admin.rooms.destroy', $room))
            ->assertRedirect(route('admin.rooms.index'));

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }

    public function test_room_management_is_restricted_to_admins(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.rooms.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('admin.rooms.store'), [
                'kode_kamar' => 'C01',
                'nama' => 'Kamar C01',
                'lokasi' => 'Lantai 3',
                'harga' => 1000000,
                'status' => 'tersedia',
            ])
            ->assertForbidden();
    }

    public function test_create_and_update_validate_unique_codes_and_nonnegative_prices(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $room = Room::create([
            'kode_kamar' => 'C01',
            'nama' => 'Kamar C01',
            'lokasi' => 'Lantai 3',
            'harga' => 1000000,
            'status' => 'tersedia',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.rooms.create'))
            ->post(route('admin.rooms.store'), [
                'kode_kamar' => 'C01',
                'nama' => 'Kamar Duplikat',
                'lokasi' => 'Lantai 3',
                'harga' => 1000000,
                'status' => 'tersedia',
            ])
            ->assertSessionHasErrors('kode_kamar');

        $this->from(route('admin.rooms.create'))
            ->post(route('admin.rooms.store'), [
                'kode_kamar' => 'C02',
                'nama' => 'Kamar C02',
                'lokasi' => 'Lantai 3',
                'harga' => -1,
                'status' => 'tersedia',
            ])
            ->assertSessionHasErrors('harga');

        $this->put(route('admin.rooms.update', $room), [
            'kode_kamar' => 'C01',
            'nama' => 'Kamar C01',
            'lokasi' => 'Lantai 3',
            'harga' => 1000000,
            'status' => 'tersedia',
        ])->assertRedirect(route('admin.rooms.index'));

        $this->assertDatabaseCount('rooms', 5);
    }

    public function test_room_with_any_booking_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $user = User::factory()->create();
        $room = Room::create([
            'kode_kamar' => 'C01',
            'nama' => 'Kamar C01',
            'lokasi' => 'Lantai 3',
            'harga' => 1000000,
            'status' => 'tersedia',
        ]);
        $booking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.rooms.destroy', $room))
            ->assertRedirect(route('admin.rooms.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('rooms', ['id' => $room->id]);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }

    public function test_vip_is_reserved_and_cannot_be_deleted_or_renamed(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $vip = Room::query()->where('kode_kamar', 'VIP')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.rooms.update', $vip), [
                'kode_kamar' => 'VIP-UTAMA',
                'nama' => $vip->nama,
                'lokasi' => $vip->lokasi,
                'harga' => $vip->harga,
                'status' => $vip->status,
            ])
            ->assertSessionHasErrors('kode_kamar');

        $this->delete(route('admin.rooms.destroy', $vip))
            ->assertRedirect(route('admin.rooms.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('rooms', ['id' => $vip->id, 'kode_kamar' => 'VIP']);
    }

    public function test_vip_page_uses_the_vip_room_record_and_booking_targets_that_room(): void
    {
        $vip = Room::query()->where('kode_kamar', 'VIP')->firstOrFail();
        Room::create([
            'kode_kamar' => 'A01',
            'nama' => 'Kamar A01 Reguler',
            'lokasi' => 'Lantai 1',
            'harga' => 750000,
            'status' => 'tersedia',
        ]);

        $this->get(route('kamar.vip'))
            ->assertOk()
            ->assertSee('Rp1.500.000')
            ->assertSee('Lantai Utama (Akses Eksklusif)');

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('kamar.vip.booking'))
            ->assertRedirect(route('payment.show'))
            ->assertSessionHas('booking_room_id', $vip->id);
    }

    public function test_admin_can_view_paginated_room_list(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        foreach (range(1, 16) as $number) {
            Room::create([
                'kode_kamar' => sprintf('P%02d', $number),
                'nama' => sprintf('Kamar P%02d', $number),
                'lokasi' => 'Lantai 1',
                'harga' => 1000000,
                'status' => 'tersedia',
            ]);
        }

        $this->actingAs($admin)
            ->get(route('admin.rooms.index'))
            ->assertOk()
            ->assertSee('Data Kamar')
            ->assertSee('Tambah Kamar');
    }
}
