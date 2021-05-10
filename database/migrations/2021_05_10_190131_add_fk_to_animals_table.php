<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFkToAnimalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('animals', function (Blueprint $table) {
            //animals['user_id']->users['id'](link),when users['id'] deleted will sync
            $table->foreign('user_id')->references('id')->on('users')->onDelete(null);

            //animals['type_id']->types['id'](link),when types['id'] deleted will sync
            $table->foreign('type_id')->references('id')->on('types')->onDelete(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('animals', function (Blueprint $table) {
            //delete foreign
            $table->dropForeign('animals_user_id_foreign');
            $table->dropForeign('animals_type_id_foreign');
        });
    }
}
