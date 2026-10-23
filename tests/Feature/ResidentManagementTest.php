<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResidentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_view_update_and_delete_a_resident(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $this->actingAs($admin)
            ->post(route('admin.residents.store'), [
                'name' => 'Penghuni Uji',
                'email' => 'resident@example.test',
                'password' => 'password123',
                'payment_status' => 'pending',
            ])
            ->assertRedirect(route('admin.residents.index'));

        $resident = User::query()->where('email', 'resident@example.test')->firstOrFail();
        $this->assertSame('user', $resident->role);
        $this->assertTrue(Hash::check('password123', $resident->password));

        $this->get(route('admin.residents.index'))
            ->assertOk()
            ->assertSee('Penghuni Uji')
            ->assertSee('Detail');

        $this->get(route('admin.residents.show', $resident))
            ->assertOk()
            ->assertSee('Penghuni Uji')
            ->assertSee('Jumlah booking');

        $this->put(route('admin.residents.update', $resident), [
            'name' => 'Penghuni Diperbarui',
            'email' => 'resident@example.test',
            'password' => '',
            'payment_status' => 'approved',
        ])->assertRedirect(route('admin.residents.index'));

        $this->assertDatabaseHas('users', [
            'id' => $resident->id,
            'name' => 'Penghuni Diperbarui',
            'payment_status' => 'approved',
        ]);

        $this->delete(route('admin.residents.destroy', $resident))
            ->assertRedirect(route('admin.residents.index'));

        $this->assertDatabaseMissing('users', ['id' => $resident->id]);
    }

    public function test_non_admin_cannot_access_resident_management(): void
    {
        $resident = User::factory()->create();

        $this->actingAs($resident)
            ->get(route('admin.residents.index'))
            ->assertForbidden();
    }

    public function test_resident_with_booking_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        $resident = User::factory()->create();
        $room = Room::query()->firstOrFail();
        $booking = Booking::create([
            'user_id' => $resident->id,
            'room_id' => $room->id,
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.residents.destroy', $resident))
            ->assertRedirect(route('admin.residents.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $resident->id]);
        $this->assertDatabaseHas('bookings', ['id' => $booking->id]);
    }
}
