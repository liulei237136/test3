<?php

namespace Database\Factories;

use App\Models\Commit;
use App\Models\Package;
use App\Models\Pull;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PullFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pull::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title(),
            'author_id' => User::factory()->create(),
            'parent' => Package::factory()->create(),
            'child' => Package::factory()->create(),
            'status' => rand(0, 9) > 5 ? 'open' : (rand(0, 9) > 5 ? 'closed' : 'merged'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
