<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePullCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pull_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pull_id');
            $table->text('content');
            $table->unsignedBigInteger('author_id');
            $table->timestamps();

            $table->foreign('pull_id')->references('id')->on('pulls')
                ->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('users')
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
        Schema::dropIfExists('pull_comments');
    }
}
