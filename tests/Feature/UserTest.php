<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_user_overview_page()
    {

        $user = User::factory()->create();

        $response = $this->get(route('user.show', ['user' => $user->id]));

        $response->assertStatus(200);
    }
}
