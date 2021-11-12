<?php

namespace Database\Seeders;

use App\Models\Package;
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

        // Package::factory()->count(100)->create();

    }
}
