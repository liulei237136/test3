<?php

namespace Tests\Feature;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompareTest extends TestCase
{
    use RefreshDatabase;


    protected function prepare($prepareACommit = false)
    {
        $parentPackage = Package::factory()->create();

        if ($prepareACommit) {
            $commit = Commit::factory()->create(['author_id' => $parentPackage->author->id]);
            $parentPackage->commits()->attach($commit);
        }

        $this->actingAs($child = User::factory()->create());

        $childPackage = $parentPackage->clone();

        $data = compact('parentPackage', 'childPackage');

        if (isset($commit)) {
            $commit['commit'] = $commit;
        };

        return $data;
    }

    public function test_compare_result_identical()
    {
        $empty = $this->prepare();

        $response = $this->get(route('compare.package', ['parent' => $empty['parentPackage'], 'child' => $empty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('identical');

        $notEmpty = $this->prepare(true);

        $response = $this->get(route('compare.package', ['parent' => $notEmpty['parentPackage'], 'child' => $notEmpty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('identical');
    }

    public function test_parent_package_ahead_compare_result_hehind()
    {
        $empty = $this->prepare();

        //parent add a commit
        $commit = Commit::factory()->create(['author_id' => $empty['parentPackage']->author->id]);
        $empty['parentPackage']->commits()->attach($commit);

        $response = $this->get(route('compare.package', ['parent' => $empty['parentPackage'], 'child' => $empty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('behind');

        $notEmpty = $this->prepare(true);

        $commit = Commit::factory()->create(['author_id' => $notEmpty['parentPackage']->author->id]);

        $notEmpty['parentPackage']->commits()->attach($commit);

        $response = $this->get(route('compare.package', ['parent' => $empty['parentPackage'], 'child' => $empty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('behind');
    }

    public function test_child_package_ahead_compare_result_before()
    {
        $empty = $this->prepare();

        //child add a commit
        $commit = Commit::factory()->create(['author_id' => $empty['childPackage']->author->id]);
        $empty['childPackage']->commits()->attach($commit);

        $response = $this->get(route('compare.package', ['parent' => $empty['parentPackage'], 'child' => $empty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('before');

        $notEmpty = $this->prepare(true);

        //child add a commit
        $commit = Commit::factory()->create(['author_id' => $empty['childPackage']->author->id]);
        $empty['childPackage']->commits()->attach($commit);

        $response = $this->get(route('compare.package', ['parent' => $empty['parentPackage'], 'child' => $empty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('before');
    }

    public function test_compare_result_conflict()
    {
        $empty = $this->prepare();

        //parent add a commit
        $commit = Commit::factory()->create(['author_id' => $empty['parentPackage']->author->id]);
        $empty['parentPackage']->commits()->attach($commit);

        //child add a commit
        $commit = Commit::factory()->create(['author_id' => $empty['childPackage']->author->id]);
        $empty['childPackage']->commits()->attach($commit);

        $response = $this->get(route('compare.package', ['parent' => $empty['parentPackage'], 'child' => $empty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('conflict');

        $notEmpty = $this->prepare(true);

        //parent add a commit
        $commit = Commit::factory()->create(['author_id' => $notEmpty['parentPackage']->author->id]);
        $notEmpty['parentPackage']->commits()->attach($commit);

        //child add a commit
        $commit = Commit::factory()->create(['author_id' => $empty['childPackage']->author->id]);
        $empty['childPackage']->commits()->attach($commit);

        $response = $this->get(route('compare.package', ['parent' => $empty['parentPackage'], 'child' => $empty['childPackage']]));

        $response->assertSessionDoesntHaveErrors();

        $response->assertSee('conflict');
    }
}
