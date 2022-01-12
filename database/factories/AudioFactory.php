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
            'name' => $this->faker->numberBetween(1, 100000) . '.mp3',
            'file_name' => "audio/2022/01/07/9Vcg82LaICeNdy4wsa3Uox8lA3US5FlvCNH8Momm.mp3",
            'book_name' => $this->faker->sentence(),
            'audio_text' => $this->faker->paragraph(),
            'author_id' => User::factory()->create(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
