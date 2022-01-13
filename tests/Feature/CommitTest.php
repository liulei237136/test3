<?php

namespace Tests\Feature;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommitTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_commit()
    {
        $response = $this->post(route('commit.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_authorized_user_can_create_commit()
    {
        $this->withoutExceptionHandling();

        //1 when there is a package
        $package = Package::factory()->create();

        //2 package's author log in
        $this->actingAs($package->author);

        //3 and there some audio
        $audio_ids = array();
        Audio::factory()->count(30)->create()->each(function ($audio) use (&$audio_ids) {
            array_push($audio_ids, $audio->id);
        });
        $audio_ids = json_encode($audio_ids);

        //4 then he create a new commit
        $response = $this->post(route('commit.store'), [
            'package' => $package->id,
            'title' => 'title',
            'description' => 'description',
            'audio_ids' => $audio_ids,
        ]);

        //3 session has no error
        $response->assertSessionHasNoErrors();

        //4 the database has new commit
        $this->assertEquals(Commit::count(), 1);
        $commit = Commit::first();
        $this->assertEquals($commit->title, 'title');
        $this->assertEquals($commit->description, 'description');
        $this->assertEquals($commit->audio_ids, $audio_ids);
        $this->assertEquals($commit->author_id, $package->author->id);
    }
}
