<?php

namespace Database\Factories;

use App\Models\Audio;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Factories\Factory;
use Mockery\Undefined;

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
    // $updated_at = $created_at = Carbon::now()->subDay(random_int(1,1000))->subHour(random_int(1,100))->subMinute(random_int(1,100));
    // return [
    //     'name' => $this->faker->sentence(),
    //     'description' => $this->faker->paragraph(30),
    //     'author_id' => User::factory()->create()->id,
    //     'created_at' => $created_at,
    //     'updated_at' => $updated_at,
    // ];
    // $table->id();
    // $table->string('name')->nullable();
    // $table->string('file_name')->nullable();
    // $table->string('book_name')->nullable();
    // $table->string('audio_text')->nullable();
    // $table->integer('size')->nullable();
    // $table->unsignedBigInteger('package_id');
    // $table->unsignedBigInteger('author_id');
    // $table->timestamps();
    public function definition()
    {
        return [
            'name' => $this->faker->numberBetween(1,100000) . '.mp3',
            'file_name' => "audio/2022/01/07/9Vcg82LaICeNdy4wsa3Uox8lA3US5FlvCNH8Momm.mp3",
            'book_name' => 'book-name-' . $this->faker->title(),
            'audio_text' => 'audio-text-' . $this->faker->paragraph(),
            'author_id' => User::factory()->create(),
            'created_at' => now(),
        ];
    }
}
