<?php

namespace Database\Factories;

use App\Models\Commit;
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
        $to_commit = Commit::factory()->create();
        $to_package = $to_commit->package;
        $to_package->commits()->attach($to_commit);


        $from_package = $to_package->clone();
        $from_commit = $from_package->commits->first();

        return [
            'title' => $this->faker->title(),
            'description' => $this->faker->paragraph(),
            'author_id' => $from_package->author,
            'from_package' => $from_package,
            'from_commit' => $from_commit,
            'to_package' => $to_package,
            'to_commit' => $to_commit,
            'status' => rand(0,9) > 5 ? 'open' : 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
