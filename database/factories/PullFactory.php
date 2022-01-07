<?php

namespace Database\Factories;

use App\Models\Pull;
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
        // $table->id();
        // $table->string('title');
        // $table->text('description');
        // $table->unsignedBigInteger('author');
        // $table->unsignedBigInteger('from_package');
        // $table->unsignedBigInteger('from_commit');
        // $table->unsignedBigInteger('to_package');
        // $table->unsignedBigInteger('to_commit');
        // $table->string('status');//open closed
        // $table->timestamps();
        return [
            'title' => 'pull ' + $this->faker->randomNumber(1000),
            'description' => $this->faker->paragraph(30),
            'author_id' => User::factory()->create()->id,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        ];
    }
}
