<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
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
                'title' => 'title4',
                'created_at' => now()->subDay(),
                'updated_at' => now()->subDay(),
            ]),
            $this->package5 = create(Package::class, [
                'author_id' => $this->user2->id,
                'title' => 'title5',
                'created_at' => now(),
                'updated_at' => now(),
                'private' => true
            ]),
            $this->package6 =  create(Package::class, [
                'author_id' => $this->user2->id,
                'title' => 'title6',
                'created_at' => now()->addDay(),
                'updated_at' => now()->addDay(),
                'parent_id' => 1
            ]),
        );

        // $this->user1->addFavorite($this->package1);
        DB::table('favorites')->insert([
            'user_id' => $this->user1->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package1->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('favorites')->insert([
            'user_id' => $this->user1->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package2->id,
            'created_at' => now()->addDays(1),
            'updated_at' => now()->addDays(1),
        ]);
        DB::table('favorites')->insert([
            'user_id' => $this->user1->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package3->id,
            'created_at' => now()->addDays(2),
            'updated_at' => now()->addDays(2),
        ]);
        DB::table('favorites')->insert([
            'user_id' => $this->user1->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package4->id,
            'created_at' => now()->addDays(3),
            'updated_at' => now()->addDays(3),
        ]);
        DB::table('favorites')->insert([
            'user_id' => $this->user1->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package6->id,
            'created_at' => now()->addDays(4),
            'updated_at' => now()->addDays(4),
        ]);

        // $this->user2->addFavorite($this->package1);
        // $this->user2->addFavorite($this->package3);
        // $this->user2->addFavorite($this->package4);
        // $this->user2->addFavorite($this->package5);
        // $this->user2->addFavorite($this->package6);
        DB::table('favorites')->insert([
            'user_id' => $this->user2->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package1->id,
            'created_at' => now()->addDays(5),
            'updated_at' => now()->addDays(5),
        ]);

        DB::table('favorites')->insert([
            'user_id' => $this->user2->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package3->id,
            'created_at' => now()->addDays(6),
            'updated_at' => now()->addDays(6),
        ]);
        DB::table('favorites')->insert([
            'user_id' => $this->user2->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package4->id,
            'created_at' => now()->addDays(7),
            'updated_at' => now()->addDays(7),
        ]);
        DB::table('favorites')->insert([
            'user_id' => $this->user2->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package5->id,
            'created_at' => now()->addDays(8),
            'updated_at' => now()->addDays(8),
        ]);
        DB::table('favorites')->insert([
            'user_id' => $this->user2->id,
            'favoriteable_type' => Package::class,
            'favoriteable_id' => $this->package6->id,
            'created_at' => now()->addDays(9),
            'updated_at' => now()->addDays(9),
        ]);
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

    public function test_user_not_the_author_star_packages_which_are_private()
    {
        $this->signIn()->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 4)
            );
    }

    public function test_author_can_view_all_packages_on_star_packages_page()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 5)
            );
    }

    public function test_can_fitler_with_q_with_star_packages_page()
    {
        $this->withoutEvents();
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars', 'q' => '2']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 1)
                    ->where('packages.data.0.title', 'title2')
            );
    }

    public function test_can_filter_with_type_public_with_star_packages_page()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars', 'type' => 'public']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 4)
            );
    }

    public function test_can_filter_with_type_private()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars', 'type' => 'private']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 1)
                    ->where('packages.data.0.title', 'title2')
            );
    }

    public function test_can_filter_with_type_sources()
    {
        $this->signIn($this->user2)->get(route('user.show', ['user' => $this->user2, 'tab' => 'stars', 'type' => 'sources']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 4)
            );
    }

    public function test_can_filter_with_type_clones()
    {
        $this->signIn($this->user2)->get(route('user.show', ['user' => $this->user2, 'tab' => 'stars', 'type' => 'clones']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 1)
                    ->where('packages.data.0.title', 'title6')
            );
    }

    public function test_can_sort_default_by_recently_starred()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 5)
                    ->where('packages.data.0.title', 'title6')
                    ->where('packages.data.1.title', 'title4')
                    ->where('packages.data.2.title', 'title3')
                    ->where('packages.data.3.title', 'title2')
                    ->where('packages.data.4.title', 'title1')
            );
    }

    public function test_can_sort_by_recently_starred()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'stars', 'sort' => 'recently_starred']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Stars')
                    ->has('packages.data', 5)
                    ->has('packages.data', 5)
                    ->where('packages.data.0.title', 'title6')
                    ->where('packages.data.1.title', 'title4')
                    ->where('packages.data.2.title', 'title3')
                    ->where('packages.data.3.title', 'title2')
                    ->where('packages.data.4.title', 'title1')
            );
    }
}
