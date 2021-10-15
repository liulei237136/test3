<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    public function test_unautheticated_user_can_not_view_media_index_page(){
        $response = $this->get('/media');

        $response->assertRedirect(route('login'));
    }

    public function test_autheticated_user_can_view_media_index_page(){
        $this->actingAs(User::factory()->create());

        $response = $this->get('/media');

        $response->assertStatus(200);
    }

    public function test_autheticated_user_can_view_media_of_his(){
        $this->actingAs($user = User::factory()->create());

        $media = Media::factory()->create(['author_id' => $user->id]);

        $response = $this->get('/media');

        $response->assertStatus(200);

        $response->assertSee($media->name);
    }
}
