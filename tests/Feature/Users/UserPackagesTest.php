<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

class UserPackagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = create(User::class);
        $this->user2 =  create(User::class);

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
                'title' => 'title4',
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
                'private' => true
            ]),
            $this->package5 =  create(Package::class, [
                'author_id' => $this->user2->id,
                'title' => 'title5',
                'created_at' => now(),
                'updated_at' => now(),
                'parent_id' => 1
            ]),
        );
    }

    public function test_guest_can_not_see_private_packages()
    {
        $this->get(route('user.show', ['user' => $this->user1, 'tab' => 'packages']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 1)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 1)
                            ->where('title', 'title1')
                            ->where('updated_at', $this->package1->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_user_not_the_author_can_not_see_private_package()
    {
        $this->signIn()->get(route('user.show', ['user' => $this->user1, 'tab' => 'packages']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 1)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 1)
                            ->where('title', 'title1')
                            ->where('updated_at', $this->package1->updated_at->toDatetimeString())
                    )
            );
    }

    //q type sort
    public function test_can_fitler_with_q()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'packages', 'q' => '2']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 1)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 2)
                            ->where('title', 'title2')
                            ->where('updated_at', $this->package2->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_author_can_filter_without_type()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'packages']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 2)
            );
    }

    public function test_can_filter_with_type_public()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'packages', 'type' => 'public']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 1)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 1)
                            ->where('title', 'title1')
                            ->where('updated_at', $this->package1->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_can_filter_with_type_private()
    {
        $this->signIn($this->user1)->get(route('user.show', ['user' => $this->user1, 'tab' => 'packages', 'type' => 'private']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 1)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 2)
                            ->where('title', 'title2')
                            ->where('updated_at', $this->package2->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_can_filter_with_type_sources()
    {
        $this->signIn($this->user2)->get(route('user.show', ['user' => $this->user2, 'tab' => 'packages', 'type' => 'sources']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 2)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 4)
                            ->where('title', 'title4')
                            ->where('updated_at', $this->package4->updated_at->toDatetimeString())
                    )
                    ->has(
                        'packages.data.1',
                        fn (Assert $assert) => $assert
                            ->where('id', 3)
                            ->where('title', 'title3')
                            ->where('updated_at', $this->package3->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_can_filter_with_type_clones()
    {
        $this->signIn($this->user2)->get(route('user.show', ['user' => $this->user2, 'tab' => 'packages', 'type' => 'clones']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 1)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 5)
                            ->where('title', 'title5')
                            ->where('updated_at', $this->package5->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_can_sort_default_by_last_updated()
    {
        $this->signIn($this->user2)->get(route('user.show', ['user' => $this->user2, 'tab' => 'packages']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 3)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 5)
                            ->where('title', 'title5')
                            ->where('updated_at', $this->package5->updated_at->toDatetimeString())
                    )
                    ->has(
                        'packages.data.1',
                        fn (Assert $assert) => $assert
                            ->where('id', 4)
                            ->where('title', 'title4')
                            ->where('updated_at', $this->package4->updated_at->toDatetimeString())
                    )
                    ->has(
                        'packages.data.2',
                        fn (Assert $assert) => $assert
                            ->where('id', 3)
                            ->where('title', 'title3')
                            ->where('updated_at', $this->package3->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_can_sort_by_last_updated()
    {
        $this->signIn($this->user2)->get(route('user.show', ['user' => $this->user2, 'tab' => 'packages','sort'=>'last_updated']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 3)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 5)
                            ->where('title', 'title5')
                            ->where('updated_at', $this->package5->updated_at->toDatetimeString())
                    )
                    ->has(
                        'packages.data.1',
                        fn (Assert $assert) => $assert
                            ->where('id', 4)
                            ->where('title', 'title4')
                            ->where('updated_at', $this->package4->updated_at->toDatetimeString())
                    )
                    ->has(
                        'packages.data.2',
                        fn (Assert $assert) => $assert
                            ->where('id', 3)
                            ->where('title', 'title3')
                            ->where('updated_at', $this->package3->updated_at->toDatetimeString())
                    )
            );
    }

    public function test_can_sort_by_title()
    {
        $this->signIn($this->user2)->get(route('user.show', ['user' => $this->user2, 'tab' => 'packages','sort'=>'title']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 3)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', 3)
                            ->where('title', 'title3')
                            ->where('updated_at', $this->package3->updated_at->toDatetimeString())
                    )
                    ->has(
                        'packages.data.1',
                        fn (Assert $assert) => $assert
                            ->where('id', 4)
                            ->where('title', 'title4')
                            ->where('updated_at', $this->package4->updated_at->toDatetimeString())
                    )
                    ->has(
                        'packages.data.2',
                        fn (Assert $assert) => $assert
                            ->where('id', 5)
                            ->where('title', 'title5')
                            ->where('updated_at', $this->package5->updated_at->toDatetimeString())
                    )
            );
    }
}
