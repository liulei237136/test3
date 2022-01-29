<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserStarsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = create(User::class);
        // $this->user2 =  create(User::class);
        // $this->user3 = create(User::class);

        $this->user1->package()->save(
            $this->package1 = create(Package::class, [
                'author_id' => $this->user1->id,
                'title' => 'title1',
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4)
            ]),
            $this->package2 = create(Package::class, [
                'author_id' => $this->user1->id,
                'title' => 'title2',
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
                'private' => true
            ]),
        );
        $this->user2->package()->save(
            $this->package3 = create(Package::class, [
                'author_id' => $this->user2->id,
                'title' => 'title3',
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ]),
            $this->package4 = create(Package::class, [
                'author_id' => $this->user2->id,
                'title' => 'title3',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]),
            $this->package5 = create(Package::class, [
                'author_id' => $this->user2->id,
                'title' => 'title4',
                'created_at' => now(),
                'updated_at' => now(),
                'private' => true
            ]),
            $this->package6 =  create(Package::class, [
                'author_id' => $this->user2->id,
                'title' => 'title5',
                'created_at' => now()->addDay(),
                'updated_at' => now()->addDay(),
                'parent_id' => 1
            ]),
        );

        $this->user1->addFavorite($this->package3);
        $this->user1->addFavorite($this->package4);
        $this->user1->addFavorite($this->package6);

        $this->user2->addFavorite($this->package1);

    }

    // public function test_guest_can_not_see_private()
    // {
    //     $this->get(route('user.show', ['user' => $this->user2, 'tab' => 'stars']))
    //         ->assertInertia(
    //             fn (Assert $assert) => $assert
    //                 ->component('User/Stars')
    //                 ->has('packages.data', 1)
    //                 ->has(
    //                     'packages.data.0',
    //                     fn (Assert $assert) => $assert
    //                         ->where('id', 1)
    //                         ->where('title', 'title1')
    //                         ->where('updated_at', $this->package1->updated_at->toDatetimeString())
    //                 )
    //         );
    // }

}
