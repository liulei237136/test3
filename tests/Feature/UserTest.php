<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;


    protected function setUp(): void
    {
        parent::setUp();

        $this->userOne = create(User::class);
        $this->userTwo =  create(User::class);

        $this->userOne->package()->create(
            $this->publicPackage = make(Package::class)->toArray(),
            $this->privatePackage = make(Package::class, ['private' => true])->toArray(),
            $this->clonePackage =  make(Package::class, ['parent_id' => 1])->toArray(),
        );
        $this->publicTitle = $this->publicPackage['title'];
        $this->privateTitle = $this->privatePackage['title'];
        $this->cloneTitle = $this->clonePackage['title'];
    }

    public function test_guest_can_see_user_page()
    {
        $response = $this->get(route('user.show', ['user' => $this->userOne->id]));

        $response->assertStatus(200);
    }

    public function test_user_package_page_with_none_queryparam()
    {
        //when as guest
        $response = $this->get(route('user.show', ['user' => $this->userOne, 'tab' => 'packages']));
        $response->assertStatus(200);
        $response->assertSee($this->publicTitle, $this->cloneTitle);
        $response->assertDontSee($this->privateTitle);
        //when login as author
        $this->actingAs($this->userOne);
        $response = $this->get(route('user.show', ['user' => $this->userOne, 'tab' => 'packages']));
        $response->assertStatus(200);
        $response->assertSee($this->publicTitle, $this->privateTitle, $this->cloneTitle);
        //when login as another man
        $this->actingAs($this->userTwo);
        $response = $this->get(route('user.show', ['user' => $this->userOne, 'tab' => 'packages']));
        $response->assertStatus(200);
        $response->assertSee($this->publicTitle, $this->cloneTitle);
        $response->assertDontSee($this->privateTitle);
    }

    // public function test_user_package_queryparam_q()
    // {
    //     //when q euqal publicTitle
    //     $response = $this->get(route('user.show', ['user' => $this->userOne, 'tab' => 'packages', 'q' => $this->publicTitle]));
    //     $response->assertStatus(200);
    //     $response->assertSee($this->publicTitle);
    //     $response->assertDontSee($this->privateTitle, $this->cloneTitle);
    //     //when q euqal privateTitle
    //     $response = $this->get(route('user.show', ['user' => $this->userOne, 'tab' => 'packages', 'q' => $this->privateTitle]));
    //     $response->assertStatus(200);
    //     $response->assertDontSee($this->privateTitle, $this->privateTitle, $this->cloneTitle);
    // }

    // public function test_user_package_queryparam_type_all()
    // {
    //     $user = User::factory()->create();
    //     $antherUser = User::factory()->create();

    //     $user->package()->create(
    //         $private = Package::factory()->make()->toArray(),
    //         $public = Package::factory()->make(['private' => true])->toArray(),
    //         $clone = Package::factory()->make(['parent_id' => 1])->toArray(),
    //     );
    //     //when not login
    //     $response = $this->get(route('user.show', ['user' => $user, 'tab' => 'packages']));
    //     $response->assertSee($public['title'], $clone['title']);
    //     $response->assertDontSee($private['title']);
    // }
}
