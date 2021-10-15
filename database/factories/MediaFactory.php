<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\User;
use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class MediaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Media::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    // Schema::create('media', function (Blueprint $table) {
    //     $table->id();
    //     $table->string('name');
    //     $table->string('file_name');
    //     $table->string('mime_type');
    //     $table->integer('size');
    //     $table->unsignedBigInteger('author_id');
    //     $table->timestamps();

    //     $table->foreign('author_id')->references('id')->on('users');
    // });
    // 'name' => $this->faker->name(),
    // 'email' => $this->faker->unique()->safeEmail(),
    // 'email_verified_at' => now(),
    // 'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    // 'remember_token' => Str::random(10),
    public function definition()
    {
        $mime_type = collect(['.mp3','.dab'])->random();
        $name = $this->faker->name().$mime_type;
        $id = (string)rand();

        $now = now()->format('Y').DIRECTORY_SEPARATOR.now()->format('m').DIRECTORY_SEPARATOR.now()->format('d');
        $rand_str = Str::random(10);
        $author_id = rand(1,1000);
        $file_name = storage_path('media'.DIRECTORY_SEPARATOR.$now.DIRECTORY_SEPARATOR.$author_id.DIRECTORY_SEPARATOR.$rand_str.$mime_type);
        return [
            'name' => $name,
            'file_name' => $file_name,
            'mime_type' => $mime_type,
            'size' => (string)rand(0, 1000),
            'author_id' => $author_id,
        ];
    }
}
