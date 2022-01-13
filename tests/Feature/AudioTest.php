<?php

namespace Tests\Feature;

use App\Models\Audio;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_can_not_create_audio()
    {
        $response = $this->post(route('audio.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_create_audio_with_only_file()
    {
        $this->actingAs($user = User::factory()->create());

        $public = Storage::fake('public');

        $file = UploadedFile::fake()->create('65001.mp3', 20, 'audio/mpeg');

        $response = $this->post(route('audio.store'), [
            'file' => $file,
        ]);

        $file_path = "audio/" . date('Y/m/d') . '/' . $file->hashName();

        $public->assertExists($file_path);

        $this->assertEquals(Audio::count(), 1);
        $first = Audio::first();
        $this->assertEquals($first->file_name, null);
        $this->assertEquals($first->file_path, $file_path);
        $this->assertEquals($first->file_size, 20 * 1024);
        $this->assertEquals($first->author_id, $user->id);
        $response->assertSessionHasNoErrors();
    }

    public function test_authorized_user_can_create_audio_without_file()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = User::factory()->create());

        $response = $this->post(route('audio.store'), [
            'file_name' => '65001.mp3',
        ]);

        $this->assertEquals(Audio::count(), 1);

        $first = Audio::first();
        $this->assertEquals($first->file_name, '65001.mp3');
        $this->assertEquals($first->file_path, null);
        $this->assertEquals($first->file_size, null);
        $this->assertEquals($first->author_id, $user->id);
        $response->assertSessionHasNoErrors();
    }

    public function test_authorized_user_can_create_audio_from_import()
    {
        $this->withoutExceptionHandling();

        $this->actingAs($user = User::factory()->create());

        $file = UploadedFile::fake()->create('65001.mp3', 20, 'audio/mpeg');
        $file_path = "audio/" . date('Y/m/d') . '/' . $file->hashName();
        $file_size = $file->getSize();
        $response = $this->post(route('audio.store'), [
            'file_path' => $file_path,
            'file_size' => $file_size,

        ]);

        $this->assertEquals(Audio::count(), 1);

        $first = Audio::first();
        $this->assertEquals($first->file_name, null);
        $this->assertEquals($first->file_path, $file_path);
        $this->assertEquals($first->file_size, $file_size);
        $this->assertEquals($first->author_id, $user->id);
        $response->assertSessionHasNoErrors();
    }
}
