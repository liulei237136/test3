<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiffsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('diffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('base_commit');
            $table->unsignedBigInteger('from_commit');
            $table->json('from_deletes');
            $table->json('from_inserts');
            $table->unsignedBigInteger('to_commit');
            $table->json('to_deletes');
            $table->json('to_inserts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('diffs');
    }
}
