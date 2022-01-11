<?php

namespace Database\Factories;

use App\Models\Audio;
use App\Models\Commit;
use App\Models\Package;
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
        // $table->id();
        // $table->unsignedBigInteger('package_id');
        // $table->unsignedBigInteger('author_id');
        // $table->string('title');
        // $table->string('description')->nullable();
        // $table->json('audio')->nullable();
        // $table->json('path')->nullable();
        // $table->timestamps();
        $package = Package::factory()->create();
        $ids = array();
         Audio::factory()->count(2)->create(['author_id' => 1])->each(function($audio) use( &$ids){
            array_push($ids, $audio->id);
         });

        return [
           'package_id' => $package->id,
           'author_id' => $package->author->id,
           'title' => 'commit-title-' . $this->faker->title(),
           'description' => 'commit-description-' . $this->faker->paragraph(),
           'path' => '[]',
           'audio' => json_encode($ids),
        //    'audio' => json_encode('[0]'),
           'created_at' => now(),
        ];
    }
}