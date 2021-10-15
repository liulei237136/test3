<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_package_list_page(){
        $response = $this->get("/packages");

        $response->assertStatus(200);
    }

    public function test_user_can_view_package_if_it_exist(){
        $package = Package::factory()->create();

        $response = $this->get("/packages");

        $response->assertStatus(200);
        $response->assertSee($package->title);
    }
}
