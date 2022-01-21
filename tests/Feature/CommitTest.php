<?php

namespace Tests\Feature;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use App\Models\User;
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
        // $this->withoutExceptionHandling();

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

    public function test_guest_user_can_view_commit_index_page_of_a_package_without_commit()
    {
        //there is a package with no commit
        $package = Package::factory()->create();

        $response = $this->get(route('package.commit.index', ['package' => $package->id]));

        $response->assertStatus(200);
    }

    public function test_guest_user_can_view_all_commits_on_commit_index_page_of_a_package()
    {
        //a package
        $package = Package::factory()->create();
        //two commits
        $package->commits()->attach($commit1 = Commit::factory()->create(['author_id' => $package->author->id]));

        $package->commits()->attach($commit2 = Commit::factory()->create(['author_id' => $package->author->id]));
        //when nav to commits index
        $response = $this->get(route('package.commit.index', ['package' => $package->id]));

        $response->assertStatus(200);

        $response->assertSee($commit1->title);

        $response->assertSee($commit1->title);
    }

    public function test_guest_user_can_view_commits_of_one_contributer_with_query_params_author_and_user_id()
    {
        //a package
        $package = Package::factory()->create();

        //two user
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        //two commits
        $package->commits()->attach($commit1 = Commit::factory()->create(['author_id' => $user1->id]));
        $package->commits()->attach($commit2 = Commit::factory()->create(['author_id' => $user2->id]));

        //when nav to commits index with user1 param
        $response = $this->get(route('package.commit.index', ['package' => $package->id, 'author' => $user1->id]));
        //only user1 commit
        $response->assertStatus(200);
        $response->assertSee($commit1->title);
        $response->assertDontSee($commit2->title);

        //when nav to commits index with user2 param
        $response = $this->get(route('package.commit.index', ['package' => $package->id, 'author' => $user2->id]));
        //only user1 commit
        $response->assertStatus(200);
        $response->assertSee($commit2->title);
        $response->assertDontSee($commit1->title);
    }

    public function test_commit_audio_list_is_act_as_it_should_be()
    {
        //there is a package
        $package = Package::factory()->create();

        //and two audio;
        $firstAudio = Audio::factory()->create();
        $secondAudio = Audio::factory()->create();

        //log in as the author of package
        $this->actingAs($package->author);

        //creata a commit
        $commit = Commit::factory()->create([
            'audio_ids' => json_encode(array($firstAudio->id, $secondAudio->id)),
        ]);

        //the commit audio_list
        $this->assertEquals($commit->audioList->first()['file_name'], $firstAudio->file_name);
        $this->assertEquals($commit->audioList->last()['file_name'], $secondAudio->file_name);
    }

    public function test_nav_to_package_audio_page_correct()
    {
        //there is a package
        $package = Package::factory()->create();
        //and the author login
        $this->actingAs($package->author);
        //and creata two commit
        $commit1 = $package->commits()->create(Commit::factory()->make(['created_at' => now()->subDay(1)])->toArray());
        $commit2 = $package->commits()->create(Commit::factory()->make()->toArray());
        //when nav to package.audio without commit
        $response = $this->get(route('package.audio', ['package' => $package->id]));
        $response->assertStatus(200);
        $response->assertSee($commit2->description);
        $response->assertDontSee($commit1->description);
        //when nav to package.audio with commit1 id
        $response = $this->get(route('package.audio', ['package' => $package->id, 'commit' => $commit1->id]));
        $response->assertStatus(200);
        $response->assertSee($commit1->description);
        $response->assertDontSee($commit2->description);
        //when nav to package.audio with commit2 id
        $response = $this->get(route('package.audio', ['package' => $package->id, 'commit' => $commit2->id]));
        $response->assertStatus(200);
        $response->assertSee($commit2->description);
        $response->assertDontSee($commit1->description);
    }
}
