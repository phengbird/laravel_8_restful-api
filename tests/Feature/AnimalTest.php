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

        $response = $this->json('get','api/animals');
        //setting when catch exception warn it
        $this->withExceptionHandling();

        $resultStructure = [
            'data' => [ 
                //too much data , use * to check all in table 
                '*' => [
                    "id" , "type_id" , "type_name" , "name" , "birthday" , "age" , "area" , "fix" , "description" , "personality" , "created_at"
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

        $this->withExceptionHandling();

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
            'api/animals',
            $formData
        );

        $response->assertStatus(201)->assertJson(['data' => $formData]);
    }

    public function testCanNotCreateAnimal()
    {
        // 誰?沒有模擬會員權限的程式

        // 建立一個分類資料
        $type = Type::factory()->create();

        // 做什麼事? 請求時並傳入資料
        $response = $this->json(
            'POST',
            'api/animals',
            [
                'type_id' => $type->id,
                'name' => '大黑',
                'birthday' => '2017-01-01',
                'area' => '台北市',
                'fix' => '1'
            ]
        );

        // 結果? 檢查返回資料，沒有token，狀態碼回應401
        $response->assertStatus(401)
            ->assertJson(
                [
                    "message" => "Unauthenticated."
                ]
            );
    }
}
