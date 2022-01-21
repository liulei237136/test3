<?php

namespace Tests\Feature;

use App\Models\Commit;
use App\Models\Package;
use App\Models\Pull;
use App\Models\PullComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PullTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_cannot_create_pull()
    {
        $response = $this->post(route('pull.store', ['child' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_can_create_pull_if_has_no_open_pull()
    {
        //there is a parent package
        $parentPackage = Package::factory()->create();
        //a user clone it
        $childAuthor = User::factory()->create();
        $this->actingAs($childAuthor);
        $childPackage = $parentPackage->clone();

        //child package create  a commit
        $childPackage->commits()->attach(Commit::factory()->create(['author_id' => $childAuthor->id]));

        //child package create a pull
        $response = $this->post(route('pull.store'), [
            'child' => $childPackage->id,
            'title' => 'title',
        ]);

        $response->assertSessionDoesntHaveErrors();
        $this->assertEquals(Pull::count(), 1);
        $firstPull = Pull::first();
        $this->assertEquals($firstPull->child, $childPackage->author->id);
        $this->assertEquals($firstPull->title, 'title');
        $this->assertEquals($firstPull->parent, $parentPackage->author->id);
        $this->assertEquals($firstPull->status, 'open');
        $response->assertRedirect(route('pull.show', ['package' => $parentPackage, 'pull' => $firstPull]));

        // when want to create another pull should reject
        $response = $this->post(route('pull.store'), [
            'child' => $childPackage->id,
            'title' => 'title',
        ]);
        $response->assertStatus(403);
        $this->assertEquals(Pull::count(), 1);

        // when want to close the first pull
        $this->post(route('pull.close', ['pull' => $firstPull]));
        //and then create another pull should pass
        $response = $this->post(route('pull.store'), [
            'child' => $childPackage->id,
            'title' => 'title_second',
        ]);
        $response->assertSessionDoesntHaveErrors();
        $this->assertEquals(Pull::count(), 2);
        $secondPull = Pull::all()->last();
        $this->assertEquals($secondPull->child, $childPackage->author->id);
        $this->assertEquals($secondPull->title, 'title_second');
        $this->assertEquals($secondPull->parent, $parentPackage->author->id);
        $this->assertEquals($secondPull->status, 'open');
        $response->assertRedirect(route('pull.show', ['package' => $parentPackage, 'pull' => $secondPull]));
    }

    public function test_other_user_can_not_close_or_open_pull()
    {
        //there is a parent package
        $parentPackage = Package::factory()->create();
        //a user clone it
        $this->actingAs($user = User::factory()->create());
        $childPackage = $parentPackage->clone();

        //child package create  a commit
        $childPackage->commits()->attach(Commit::factory()->create(['author_id' => $childPackage->id]));

        //child package create a pull
        $response = $this->post(route('pull.store'), [
            'child' => $childPackage->id,
            'title' => 'title',
        ]);

        //login as anther man
        $this->actingAs(User::factory()->create());
        //when want to close the pull
        $pull = Pull::first();
        $response = $this->post(route('pull.close', ['pull' => $pull]));
        $response->assertStatus(401);

        //when login as author
        $this->actingAs($user);
        $response = $this->post(route('pull.close', ['pull' => $pull]));
        $response->assertSessionHasNoErrors();
        $pull = Pull::first();
        $this->assertEquals($pull->status, 'closed');

        //login as anther man
        $this->actingAs(User::factory()->create());
        //when want to close the pull
        $pull = Pull::first();
        $response = $this->post(route('pull.open', ['pull' => $pull]));
        $response->assertStatus(401);

        //when login as author
        $this->actingAs($user);
        $response = $this->post(route('pull.open', ['pull' => $pull]));
        $response->assertSessionHasNoErrors();
        $pull = Pull::first();
        $this->assertEquals($pull->status, 'open');
    }

    public function test_create_pull_with_commment()
    {
        //there is a parent package
        $parentPackage = Package::factory()->create();
        //a user clone it
        $this->actingAs($child = User::factory()->create());
        $childPackage = $parentPackage->clone();

        //child package create  a commit
        $childPackage->commits()->attach(Commit::factory()->create(['author_id' => $child->id]));

        //child package create a pull
        $response = $this->post(route('pull.store'), [
            'child' => $childPackage->id,
            'title' => 'title',
            'comment' => 'comment',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertEquals(PullComment::count(), 1);
    }
}
