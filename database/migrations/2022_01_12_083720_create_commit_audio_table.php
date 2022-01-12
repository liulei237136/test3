<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommitAudioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('commit_audio', function (Blueprint $table) {
            $table->unsignedBigInteger('commit_id');
            $table->unsignedBigInteger('audio_id');
            $table->unique(['commit_id', 'audio_id']);
            $table->foreign('commit_id')->references('id')->on('commits')
                ->onDelete('cascade');
            $table->foreign('audio_id')->references('id')->on('audio')
                ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('commit_audio');
    }
}
