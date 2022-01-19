<?php

namespace Tests\Feature;

use App\Models\Commit;
use App\Models\Package;
use App\Models\Pull;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PullTest extends TestCase
{
    use RefreshDatabase;

    // public function test_guest_can_see_package_pulls_page()
    // {
    //     //1 case a package
    //     $package = Package::factory()->create();
    //     //2 when get to the package pulls page
    //     $response = $this->get(route('package.pulls', ['package' => $package]));
    //     //3 get 200 ok
    //     $response->assertStatus(200);
    // }
    public function test_guest_user_cannot_create_pull()
    {
        $response = $this->post(route('pull.store', ['child' => 1]));

        $response->assertRedirect(route('login'));
    }

    public function test_can_create_pull_if_has_no_open_pull()
    {
        $this->withoutExceptionHandling();
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

        // $pull = Pull::find();

        // $this->assertEquals($pull->child, $childPackage->author->id);
        // $this->assertEquals($pull->title, 'title');
        // $this->assertEquals($pull->parent, $parentPackage->author->id);
        // $this->assertEquals($pull->status, 'open');
        // // $response->assertRedirect(route('pull.show', ['pull']))


        //when want to create anthor pull

    }

    // public function test_cannot_create_or_open_pull_if_has_one_open_pull()
    // {
    // }
}
