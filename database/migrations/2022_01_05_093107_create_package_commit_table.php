<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageCommitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_commit', function (Blueprint $table) {
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('commit_id');

            $table->timestamps();
            $table->unique(['package_id', 'commit_id']);
            $table->foreign('package_id')->references('id')->on('packages')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('commit_id')->references('id')->on('commits')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('package_commit');
    }
}
