<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRestaurantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->increments('id_restaurant')->unsigned();
            $table->string('restaurant_name', 30);
            $table->string('restaurant_slug');
            $table->string('restaurant_owner', 30);
            $table->text('restaurant_address');
            $table->string('restaurant_image');
            $table->string('restaurant_latitude');
            $table->string('restaurant_longitude');
            $table->text('restaurant_description');
            $table->integer('restaurant_user')->unsigned();
            $table->timestamps();

            $table->foreign('restaurant_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurants');
    }
}
