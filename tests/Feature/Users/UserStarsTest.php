<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

class UserStarsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = create(User::class);
        $this->user2 =  create(User::class);
        // $this->user3 = create(User::class);

        $this->user1->packages()->save(
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
        $this->user2->packages()->save(
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

        $this->user1->addFavorite($this->package1);
        $this->user1->addFavorite($this->package2);
        $this->user1->addFavorite($this->package3);
        $this->user1->addFavorite($this->package4);
        $this->user1->addFavorite($this->package6);

        $this->user2->addFavorite($this->package1);
        $this->user2->addFavorite($this->package3);
        $this->user2->addFavorite($this->package4);
        $this->user2->addFavorite($this->package5);
        $this->user2->addFavorite($this->package6);
    }

    public function test_guest_can_not_see_star_packages_which_are_private()
    {
        $this->withoutExceptionHandling();
        $this->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 4)
            );
    }
}
