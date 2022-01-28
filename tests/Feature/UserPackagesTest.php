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

        $this->userOne = create(User::class);
        $this->userTwo =  create(User::class);

        $this->userOne->package()->create(
            $this->publicPackageOne = make(Package::class)->toArray(),
            $this->privatePackageOne = make(Package::class, ['private' => true])->toArray(),
        );
        $this->userTwo->package()->create(
            $this->publicPackageTwo = make(Package::class)->toArray(),
            $this->privatePackageTwo = make(Package::class, ['private' => true])->toArray(),
            $this->clonePackageTwo =  make(Package::class, ['parent_id' => $this->publicPackageOne->id])->toArray(),
        );
        // $this->publicTitle = $this->publicPackage['title'];
        // $this->privateTitle = $this->privatePackage['title'];
        // $this->cloneTitle = $this->clonePackage['title'];
    }

    public function test_guest_can_see_public_packages_without_any_filter()
    {
        $this->get(route('user.show', ['user' => $this->userOne, 'tab' => 'packages']))
            ->assertInertia(
                fn (Assert $assert) => $assert
                    ->component('User/Packages')
                    ->has('packages.data', 1)
                    ->has(
                        'packages.data.0',
                        fn (Assert $assert) => $assert
                            ->where('id', $this->publicPackageOne->id)
                    )
            );
    }
}
