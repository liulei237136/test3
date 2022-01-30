<?php

namespace Tests\Unit;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_have_many_stared_packages()
    {
        $this->withoutExceptionHandling();
        $user = create(User::class);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Relations\HasMany', $user->starredPackages());
    }
}
