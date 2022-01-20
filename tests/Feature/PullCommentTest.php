<?php

namespace Tests\Feature;

use App\Models\Commit;
use App\Models\Package;
use App\Models\PullComment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Redirect;
use Tests\TestCase;
use Illuminate\Support\Str;

class PullCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_user_cannot_create_pull_comment()
    {
        $response = $this->post(route('pull.comment.store', ['pull' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_only_child_and_parent_author_can_create_pull_comment()
    {
        // $this->withoutExceptionHandling();

        //prepare a pull
        $parentPackage = Package::factory()->create();
        $childAuthor = User::factory()->create();
        $this->actingAs($childAuthor);
        $childPackage = $parentPackage->clone();
        $childPackage->commits()->attach(Commit::factory()->create(['author_id' => $childAuthor->id]));
        $this->post(route('pull.store'), [
            'child' => $childPackage->id,
            'title' => 'title',
        ]);

        //log in as third user
        $this->actingAs(User::factory()->create());

        //when want to create pull comment;
        $response = $this->post(route('pull.comment.store', ['pull' => 1]), []);
        $response->assertStatus(401);
        $this->assertEquals(PullComment::count(), 0);
        //when login as pull child author
        $this->actingAs($childPackage->author);

        $response = $this->post(route('pull.comment.store', ['pull' => 1]), []);
        $response->assertSessionHasErrors(['content']);
        $response->assertRedirect();
        $this->assertEquals(PullComment::count(), 0);

        $response = $this->post(route('pull.comment.store', ['pull' => 1]), ['content' => Str::random(1025)]);
        $response->assertSessionHasErrors(['content']);
        $response->assertRedirect();
        $this->assertEquals(PullComment::count(), 0);

        $response = $this->post(route('pull.comment.store', ['pull' => 1]), ['content' => 'content']);
        $response->assertSessionHasNoErrors();
        $this->assertEquals(PullComment::count(), 1);
        $comment = PullComment::first();
        $this->assertEquals($comment->content, 'content');
        $this->assertEquals($comment->author->id, $childPackage->author->id);
    }
}
