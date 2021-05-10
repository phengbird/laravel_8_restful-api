<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnimalUserLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('animal_user_likes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('animal_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('animal_id')->references('id')->on('animals')->onDelete(null);
            $table->foreign('user_id')->references('id')->on('users')->onDelete(null);
        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('animal_user_likes', function (Blueprint $table) {
            //delete foreign
            $table->dropForeign('animal_user_likes_user_id_foreign');
            $table->dropForeign('animal_user_likes_animal_id_foreign');
        });
        Schema::dropIfExists('animal_user_likes');
    }
}
