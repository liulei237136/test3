<?php

namespace Database\Seeders;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
use App\Models\Pull;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        Pull::factory()->count(10)->create();

        $package = Package::factory()->create();
        $commits = Commit::factory()->count(20)->create(['package_id' =>$package->id]);
        $package->commits()->attach($commits);

    }
}
