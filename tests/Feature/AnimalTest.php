<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Animal;
use App\Models\Type;
use App\Models\User;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

class AnimalTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    // public function test_example()
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    /**
     * test check animal view json structure
     * 
     * @return void
     */
    public function testViewAllAnimal()
    {
        //demo client has authorize ,use a model factory create client 
        Passport::actingAsClient(
            Client::factory()->create()
        );

        Type::factory(5)->create();
        User::factory(5)->create();
        Animal::factory(10)->create();

        $response = $this->json('get','api/v1/animals');
        //setting when catch exception warn it
        $this->withoutExceptionHandling();

        $resultStructure = [
            'data' => [ 
                //too much data , use * to check all in table 
                '*' => [
                    "id" , "type_id" , "type_name" , "name" , "birthday" , "age" , "area" , "fix" , "description" , "personality" , "created_at" , "update_at" 
                 ]
            ],

            "links" => [
                "first" , "last" , "prev" , "next"
            ],

            "meta" => [
                    "current_page" , "from" , "last_page" , "path" , "per_page" , "to" , "total"
            ]
        ];

        //assertJsonStructure judge json structure are same or not
        $response->assertStatus(200)->assertJsonStructure($resultStructure);
    }

    /**
     * test create animal
     * 
     * @return void
     */
    public function testCanCreateAnimal()
    {
        $user = User::factory()->create();

        Passport::actingAs(
            $user,['create-animals']//setting user has a scope with create-animals
        );

        $this->withoutExceptionHandling();

        $type = Type::factory()->create();

        $formData = [
            'type_id' => $type->id,
            'name' => 'black',
            'birthday' => '20-02-2020',
            'area' => 'taipei',
            'fix' => '1'
        ];

        $response = $this->json(
            'POST',
            'api/v1/animals',
            $formData
        );

        $response->assertStatus(201)->assertJson(['data' => $formData]);
    }

    public function testCanNotCreateAnimal()
    {
        // ?????????????????????????????????????

        // ????????????????????????
        $type = Type::factory()->create();

        // ????????????? ????????????????????????
        $response = $this->json(
            'POST',
            'api/v1/animals',
            [
                'type_id' => $type->id,
                'name' => '??????',
                'birthday' => '2017-01-01',
                'area' => '?????????',
                'fix' => '1'
            ]
        );

        // ??????? ???????????????????????????token??????????????????401
        $response->assertStatus(401)
            ->assertJson(
                [
                    "message" => "Unauthenticated."
                ]
            );
    }
}
