<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryRestaurantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_restaurant', function (Blueprint $table) {
            $table->increments('id_category_restaurant')->unsigned();
            $table->integer('id_category')->unsigned();
            $table->integer('id_restaurant')->unsigned();

            $table->foreign('id_category')->references('id_category')->on('categories');
            $table->foreign('id_restaurant')->references('id_restaurant')->on('restaurants');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_restaurant');
    }
}
