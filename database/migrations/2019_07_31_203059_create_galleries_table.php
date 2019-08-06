<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('galleries', function (Blueprint $table) {
            $table->increments('id_gallery')->unsigned();
            $table->string('gallery_image');
            $table->text('gallery_info');
            $table->string('gallery_copyright');
            $table->integer('gallery_restaurant')->unsigned();
            $table->timestamps();

            $table->foreign('gallery_restaurant')->references('id_restaurant')->on('restaurants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('galleries');
    }
}
