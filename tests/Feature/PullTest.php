<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PullTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_package_pulls_page()
    {
        //1 case a package
        $package = Package::factory()->create();
        //2 when get to the package pulls page
        $response = $this->get(route('package.pulls', ['package' => $package]));
        //3 get 200 ok
        $response->assertStatus(200);
    }
}
