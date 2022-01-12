<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_can_not_create_audio()
    {
        $response = $this->post(route('audio.store'), []);

        $response->assertStatus(200);
    }
}
