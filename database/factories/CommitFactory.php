<?php

namespace Database\Factories;

use App\Models\Commit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommitFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Commit::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title(),
            'description' => $this->faker->paragraph(),
            'author_id' => User::factory()->create(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
