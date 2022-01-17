<?php

namespace Tests\Feature;

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
        $child = $parent->clone($user);

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
        // there is a package
        $parent = Package::factory()->create();

        //and a user
        $this->actingAs($user = User::factory()->create());

        //clone it
        $child = $parent->clone($user);

        //when child create a new commit
        $commit = Commit::factory()->make()->toArray();

        $child->commits()->create($commit);

        $response = $this->get(route('compare.package', ['parent' => $parent, 'child' => $child]));

        $response->assertStatus(200);

        $response->assertSee($commit['title']);

        $response->assertSee($commit['description']);

        //when child create a second commit
        $commit = Commit::factory()->make()->toArray();

        $child->commits()->create($commit);

        $response = $this->get(route('compare.package', ['parent' => $parent, 'child' => $child]));

        $response->assertStatus(200);

        $response->assertSee($commit['title']);

        $response->assertSee($commit['description']);
    }
}
