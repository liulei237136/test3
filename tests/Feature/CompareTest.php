<?php

namespace Tests\Feature;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_child_later_than_or_equal_to_parent_return_up_to_date_or_conflict()
    {
        // there is a package
        $parent = Package::factory()->create();

        //and a user
        $this->actingAs($user = User::factory()->create());

        //clone it
        $child = $parent->clone();

        //when parent and child all have no commit
        $response = $this->get(route('compare.package', ['parent' => $parent, 'child' => $child]));

        $response->assertStatus(200);

        $response->assertSee('up_to_date');

        //when parent create a new commit
        $parent->commits()->create(Commit::factory()->make()->toArray());

        $response = $this->get(route('compare.package', ['parent' => $parent, 'child' => $child]));

        $response->assertStatus(200);

        $response->assertSee('up_to_date');

        //when parent create a second commit
        $parent->commits()->create(Commit::factory()->make()->toArray());

        $response = $this->get(route('compare.package', ['parent' => $parent, 'child' => $child]));

        $response->assertStatus(200);

        $response->assertSee('up_to_date');

        //when child create a new commit
        $child->commits()->create(Commit::factory()->make()->toArray());

        $response = $this->get(route('compare.package', ['parent' => $parent, 'child' => $child]));

        $response->assertStatus(200);

        $response->assertSee('conflict');

        //when child create a second commit
        $child->commits()->create(Commit::factory()->make()->toArray());

        $response = $this->get(route('compare.package', ['parent' => $parent, 'child' => $child]));

        $response->assertStatus(200);

        $response->assertSee('conflict');
    }

    public function test_child_longer_than_parent_return_more_commits()
    {
        $this->withoutExceptionHandling();
        // there is a package
        $parentPackage = Package::factory()->create();

        //and a user
        $this->actingAs($user = User::factory()->create());

        //clone it
        $childPackage = $parentPackage->clone();

        //when childPackage create a new commit
        $firstAudio = Audio::factory()->create();

        $firstCommit = Commit::factory()->create(['audio_ids' => '[1]']);

        $childPackage->commits()->attach($firstCommit);

        $response = $this->get(route('compare.package', ['parent' => $parentPackage, 'child' => $childPackage]));

        $response->assertStatus(200);

        $response->assertSee($parentPackage->title);
        $response->assertSee($childPackage->title);
        $response->assertSee($firstCommit->title);
        $response->assertSee($firstAudio->name);

        //when child create a second commit
        $secondAudio = Audio::factory()->create();

        $secondCommit = Commit::factory()->create(['audio_ids' => '[1,2]']);

        $childPackage->commits()->attach($secondCommit);

        $response = $this->get(route('compare.package', ['parent' => $parentPackage, 'child' => $childPackage]));

        $response->assertStatus(200);

        $response->assertSee($parentPackage->title);
        $response->assertSee($childPackage->title);
        $response->assertSee($firstCommit->title);
        $response->assertSee($secondCommit->title);
        $response->assertSee($firstAudio->name);
        $response->assertSee($secondAudio->name);
    }
}
