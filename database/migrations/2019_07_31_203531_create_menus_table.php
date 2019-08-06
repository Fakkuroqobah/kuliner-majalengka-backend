<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id_menu')->unsigned();
            $table->string('menu_name', 30);
            $table->string('menu_slug');
            $table->integer('menu_price');
            $table->string('menu_image');
            $table->string('menu_info');
            $table->boolean('menu_favorite')->default(0);
            $table->integer('menu_restaurant')->unsigned();
            $table->timestamps();

            $table->foreign('menu_restaurant')->references('id_restaurant')->on('restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menus');
    }
}
