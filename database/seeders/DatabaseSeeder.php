<?php

namespace Database\Seeders;

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
        // \App\Models\User::factory(10)->create();
        User::factory()->create([
            'name' => 'liulei237136',
            'email' => 'liulei237136@163.com',
            'password' => bcrypt('ll237136'),
            'email_verified_at' => now(),
        ]);
        User::factory()->create([
            'name' => 'liulei',
            'email' => 'liulei237136@gmail.com',
            'password' => bcrypt('ll237136'),
            'email_verified_at' => now(),
        ]);

        Package::factory()->count(16)->create();

        // Package::factory()->count(16)->create();
        // $commits = Commit::factory()->count(20)->create(['package_id' =>$package->id]);
        // $package->commits()->attach($commits);

    }
}
