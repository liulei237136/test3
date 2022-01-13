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

    public function test_authorized_user_can_create_audio()
    {
        $this->actingAs($user = User::factory()->create());

        // $table->string('file_name')->nullable();
        // $table->string('file_path')->nullable();
        // $table->string('book_name')->nullable();
        // $table->text('original_text')->nullable();;
        // $table->integer('file_size')->nullable();
        // $table->unsignedBigInteger('author_id');
        // $table->timestamps();
        Storage::fake('audio');

        $file = UploadedFile::fake()->create('65001.mp3', 20, 'audio/mpeg');

        $response = $this->post(route('audio.store'), [
            'file' => $file,
        ]);

        Storage::disk('audio')->assertExists($file->hashName());

        //3 the database has new commit
        $this->assertEquals(Audio::count(), 1);
        $first = Audio::first();
        $this->assertEquals($first->file_name, '65001.mp3');
        $this->assertEquals($first->file_path, "audio/" . date('Y/m/d') . $file->hashName());
        $this->assertEquals($first->file_size, 20 * 1024);
        $this->assertEquals($first->author_id, $user->id);

        //4 session has no error
        $response->assertSessionHasNoErrors();
    }
}
