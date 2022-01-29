<?php

namespace Tests\Unit;

use App\Models\Package;
use App\Models\User;
use ChristianKuri\LaravelFavorite\Models\Favorite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;
    public function test_a_package_has_many_stars()
    {
        $package = create(Package::class);

        $user = create(User::class);

        $user->addFavorite($package);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\MorphMany', $package->stars());
    }

    public function test_a_package_can_be_star(){
        $package = create(Package::class);

        $user = create(User::class);

        $package->star($user->id);

        $this->assertEquals(1, $package->stars()->where('user_id', $user->id)->count());
    }

    public function test_a_package_can_be_unStar(){
        $package = create(Package::class);

        $user = create(User::class);

        $package->star($user->id);
        $this->assertEquals(1, $package->stars()->where('user_id', $user->id)->count());
        $package->unStar($user->id);
        $this->assertEquals(0, $package->stars()->where('user_id', $user->id)->count());
    }
}
