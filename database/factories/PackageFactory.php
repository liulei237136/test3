<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\User;
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
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'author_id' => User::factory()->create(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function private()
    {
        return $this->state(function (array $attributes) {
            return [
                'private' => true
            ];
        });
    }

    public function public()
    {
        return $this->state(function (array $attributes) {
            return [
                'private' => null
            ];
        });
    }
}
