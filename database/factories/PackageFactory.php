<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $updated_at = $created_at = Carbon::now()->subDay(random_int(1,1000))->subHour(random_int(1,100))->subMinute(random_int(1,100));
        return [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(30),
            'author_id' => User::factory()->create(),
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
