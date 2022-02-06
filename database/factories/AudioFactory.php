<?php

namespace Database\Factories;

use App\Models\Audio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AudioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Audio::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'file_name' => $this->faker->numberBetween(1, 100000) . '.mp3',
            'file_path' => "audio/2022/01/29/1",
            'book_name' => $this->faker->sentence(),
            'original_text' => $this->faker->paragraph(),
            'author_id' => User::factory()->create(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
