<?php

namespace Database\Factories;

use App\Models\Audio;
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
        $audio_ids = array();
        Audio::factory()->count(rand(5, 50))->create()->each(function ($audio) use (&$audio_ids) {
            array_push($audio_ids, $audio->id);
        });
        $audio_ids = json_encode($audio_ids);

        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'author_id' => User::factory()->create(),
            'audio_ids' => $audio_ids,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
