<?php

namespace Tests\Feature;

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
        //1 when there is a package
        $package = Package::factory()->create();

        //2 package's author log in
        $this->actingAs($package->author);

        $response = $this->post(route('commit.store'), [
            'package' => $package->id,
            'title' => 'title',
            'description' => 'description',
        ]);

        //3 the database has new commit
        $this->assertEquals(Commit::count(), 1);
        $first = Commit::first();
        $this->assertEquals($first->title, 'title');
        $this->assertEquals($first->description, 'description');

        //4 session has no error
        $response->assertSessionHasNoErrors();
    }
}
