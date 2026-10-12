<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_room_listing_and_details_without_logging_in(): void
    {
        $this->get('/kamar')->assertOk();
        $this->get('/kamar/vip')->assertOk();
        $this->get('/kamar/1')->assertOk();
    }

    public function test_guest_is_redirected_to_login_when_booking_a_room(): void
    {
        $this->get('/kamar/vip/booking')->assertRedirect(route('login'));
    }
}
