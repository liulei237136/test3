<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StarPackagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_may_not_star_or_unstar_packages()
    {

        $package = create(Package::class);

        $this->post(route('star-packages.store',['package' => $package]))
            ->assertRedirect(route('login'));

        $this->delete(route('star-packages.destroy',['package' => $package]))
            ->assertRedirect(route('login'));

        $this->assertEquals(1,1);
    }

    public function test_a_authenticated_user_can_star_public_packages()
    {
        $this->signIn();

        $package = Package::factory()->public()->create();

        $this->post(route('star-packages.store', ['package' => $package]));

        $this->assertCount(1, $package->stars);
    }

    public function test_can_star_only_once()
    {
        $this->signIn();

        $package = Package::factory()->public()->create();

        try{
            $this->post(route('star-packages.store',['package'=>$package]));
            $this->post(route('star-packages.store',['package'=>$package]));
        }catch(\Exception $e){
            $this->fail('Can not star same model twice.');
        }

        $this->assertCount(1, $package->stars);
    }

    public function test_can_not_star_private_packages()
    {
        $this->signIn();

        $package = Package::factory()->private()->create();

        $this->post(route('star-packages.store',['package'=>$package]));

        $this->assertCount(0, $package->stars);
    }

    //unstar

    public function test_a_authenticated_user_can_unstar_public_packages()
    {
        $this->withoutExceptionHandling();
        $this->signIn();

        $package = Package::factory()->public()->create();

        $this->post(route('star-packages.store', ['package' => $package]));
        $this->assertCount(1, $package->stars);
        $this->delete(route('star-packages.store', ['package' => $package]));
        $this->assertCount(0, $package->fresh()->stars);

    }

    public function test_can_unstar_only_once()
    {
        $this->signIn();

        $package = Package::factory()->public()->create();
        $this->post(route('star-packages.store',['package'=>$package]));
        $this->assertCount(1, $package->stars);

        try{
            $this->delete(route('star-packages.store',['package'=>$package]));
            $this->delete(route('star-packages.store',['package'=>$package]));
        }catch(\Exception $e){
            $this->fail('Can not unstar same model twice.');
        }

        $this->assertCount(0, $package->fresh()->stars);
    }

    public function test_can_not_unstar_private_packages()
    {
        $this->signIn();

        $package = Package::factory()->private()->create();

        $package->stars()->create(['user_id' => auth()->id()]);

        $this->assertCount(1, $package->stars);

        $this->delete(route('star-packages.store',['package'=>$package]));

        $this->assertCount(1, $package->fresh()->stars);
    }
}
