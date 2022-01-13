<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guset_user_cannot_create_package()
    {
        $response = $this->post(route('package.store'), []);

        $response->assertRedirect(route('login'));
    }

    public function test_logged_in_user_can_create_package()
    {
        // $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post(route('package.store'), [
            'title' => 'title',
            'description' => 'description',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertEquals(Package::count(), 1);
        $package = Package::first();
        $this->assertEquals($package->title, 'title');
        $this->assertEquals($package->description, 'description');
        $this->assertEquals($package->author_id, $user->id);
    }

    public function test_guest_user_can_view_package_list_page()
    {
        //1 case there is a package
        $package = Package::factory()->create();
        //2 and a guest user route to package list page
        $response = $this->get(route('package.index'));
        // then
        $response->assertStatus(200);

        $response->assertSee($package->title);
    }

    public function test_guest_user_can_view_the_16th_package_on_2th_page()
    {
        $this->withoutExceptionHandling();

        $latestTitle = Package::factory()->create()->title;
        Package::factory()->count(14)->create(['created_at' => now()->subDay(1)]);
        $oldestTitle = Package::factory()->create(['created_at' => now()->subDay(2)])->title;

        //when nav to the first page
        $response = $this->get(route('package.index'));
        //should see the latest but not the oldest
        $response->assertStatus(200);

        $response->assertSee($latestTitle);

        $response->assertDontSee($oldestTitle);

        //when nav to the last page
        $response = $this->get(route('package.index', ['page' => '2']));
        //should see the latest but not the oldest
        $response->assertStatus(200);

        $response->assertSee($oldestTitle);

        $response->assertDontSee($latestTitle);
    }

    // public function test_user_can_view_package_if_it_exist(){
    //     $package = Package::factory()->create();

    //     $response = $this->get("/packages");

    //     $response->assertStatus(200);
    //     $response->assertSee($package->title);
    // }

    // public function test_unauthenticated_user_can_not_view_package_create_page(){
    //     $response = $this->get("/packages/create");

    //     $response->assertRedirect(route('login'));
    // }

    // public function test_authenticated_user_can_view_package_create_page(){
    //     $this->actingAs(User::factory()->create());

    //     $response = $this->get(route('package.create'));

    //     $response->assertStatus(200);
    // }

    // public function test_authenticated_user_can_create_package(){
    // $this->actingAs(User::factory()->create());

    // $package = Package::factory()->make()->toArray();

    // $response = $this->post(route('package.store'), $package);

    // $response->assertRedirect(route('package.audio.edit', ['package' => '1']));

    // $this->assertEquals(1, Package::all()->count());
    // }

}
