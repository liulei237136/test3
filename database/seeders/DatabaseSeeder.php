<?php

namespace Database\Seeders;

use App\Models\Commit;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $parent = User::factory()->create([
            'name' => 'li',
            'email' => 'liulei237136@163.com',
            'password' => bcrypt('ll237136'),
            'email_verified_at' => now(),
        ]);
        $child = User::factory()->create([
            'name' => 'il',
            'email' => 'liulei237136@gmail.com',
            'password' => bcrypt('ll237136'),
            'email_verified_at' => now(),
        ]);

        $parentPackage = $parent->package()->create(Package::factory()->make(['title' => 'test'])->toArray());

        $parentPackage->commits()->attach($parentCommit1 = Commit::factory()->create(['author_id' => $parent->id]));

        $childPackage = $parentPackage->clone($child);

        $childPackage->commits()->attach($childCommit1 = Commit::factory()->create(['author_id' => $child->id]));

        $childPackage->commits()->attach($childCommit2 = Commit::factory()->create(['author_id' => $child->id]));
    }
}
