<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\models\User;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints(); //取消外键约束
        Animal::truncate(); //clear animal database , id start from 0
        User::truncate(); //

        User::factory(5)->create();
        Animal::factory(10000)->create();
        Schema::enableForeignKeyConstraints();
    }
}
