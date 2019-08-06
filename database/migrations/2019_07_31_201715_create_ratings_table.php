<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->increments('id_rating')->unsigned();
            $table->tinyInteger('rating_value')->default(0);
            $table->string('rating_comment', 255);
            $table->integer('rating_restaurant')->unsigned();
            $table->integer('rating_user')->unsigned();
            $table->timestamps();

            $table->foreign('rating_restaurant')->references('id_restaurant')->on('restaurants')->onDelete('cascade');
            $table->foreign('rating_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
