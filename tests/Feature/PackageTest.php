<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    // public function test_user_can_view_package_list_page(){
    //     $response = $this->get("/packages");

    //     $response->assertStatus(200);
    // }

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

    public function test_authenticated_user_can_create_package(){
        // $this->actingAs(User::factory()->create());

        // $package = Package::factory()->make()->toArray();

        // $response = $this->post(route('package.store'), $package);

        // $response->assertRedirect(route('package.audio.edit', ['package' => '1']));

        // $this->assertEquals(1, Package::all()->count());
    }


}
