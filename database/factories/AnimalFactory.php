<?php

namespace Database\Factories;

use App\Models\Animal;
use App\Models\User;
use App\models\Type;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Animal::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            // 'type_id' => $this->faker->numberBetween(1,3), //random from 1 to 3
            'type_id' => Type::all()->random()->id,
            'name' => $this->faker->name,
            'birthday' => $this->faker->date(),
            'area' => $this->faker->city,
            'fix' => $this->faker->boolean,
            'description' => $this->faker->realText, //realText can get chinese , for now
            'personality' => $this->faker->text,
            'user_id' => User::all()->random()->id
        ];
    }
}
