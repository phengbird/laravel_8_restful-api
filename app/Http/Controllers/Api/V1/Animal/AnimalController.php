<?php

namespace App\Http\Controllers\Api\V1\Animal;

use App\http\Controllers\Controller;
use App\Http\Resources\AnimalCollection;
use App\Http\Resources\AnimalResource;
use App\Models\Animal;
use App\Http\Requests\StoreAnimalRequest;
use App\Http\Requests\UpdateAnimalRequest;
use App\Services\AnimalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnimalController extends Controller
{
    private $animalServices;


    public function __construct(AnimalService $animalServices)
    {
        $this->animalServices = $animalServices;

        $this->middleware('scopes:create-animals', ['only' => ['store']]);

        $this->middleware('auth:api', ['except' => ['index', 'show']]);

        $this->middleware('client', ['only' => ['index', 'show']]);
    }

    /**
     * @OA\Get(
     *      path="/api/v1/animals",
     *      operationId="animalIndex",
     *      tags={"Animal"},
     *      summary="取得動物資源列表",
     *      description="查看動物資源列表",
     *      security={
     *         {
     *              "passport": {}
     *         }
     *      },
     *      @OA\Parameter(
     *          name="filters",
     *          description="篩選條件",
     *          required=false,
     *          in="query",
     *          example="name:黑",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="sorts",
     *          description="排序條件",
     *          required=false,
     *          in="query",
     *          example="name:asc,id:desc",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          description="設定回傳資料筆數(預設10筆資料)",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="請求成功",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                      property="data",
     *                      @OA\Items(ref="#/components/schemas/Animal")
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="身分驗證未通過"
     *      ),
     *      @OA\Response(
     *          response=429,
     *          description="請求次數超過限制"
     *      )
     * )
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //show all
        // $animal = Animal::get();

        //use cache to get data
        //get url
        $url = $request->url();
        //get queryparam , example: ?limit=5&page=2
        $queryParams = $request->query();
        //every queryParam will not same , using the first english word of the param to sort
        ksort($queryParams);
        //using http_bild_query way to string with queryParam
        $queryString = http_build_query($queryParams);
        //combine a complete website
        $fullUrl = "{$url}?{$queryString}";
        //use laravel cache to check have record or not
        if (Cache::has($fullUrl)) {
            return Cache::get($fullUrl);
        }

        //use service to filter
        $animal = $this->animalServices->getListData($request);

        //if dont have cache , then set 60sec timer , and named
        return Cache::remember($fullUrl, 60, function () use ($animal) {
            // return response($animal,Response::HTTP_OK);
            return new AnimalCollection($animal);
        });
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * @OA\Post(
     *      path="/api/v1/animals",
     *      operationId="animalStore",
     *      tags={"Animal"},
     *      summary="新增動物資料",
     *      description="新增動物資料",
     *      security={
     *         {
     *              "passport": {}
     *         }
     *      },
     *      @OA\RequestBody(
     *           @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(ref="#/components/schemas/StoreAnimalRequest"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="新增成功",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Animal"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="身分驗證未通過"
     *      )
     * )
     * 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAnimalRequest $request)
    {
        $this->authorize('create',Animal::class);
    
        //using storeAnimalRequest::class
        // $this->validate($request, [
        //     // 'type_id' => 'nullable|integer',
        //     'type_id' => 'nullable|exists:types,id',
        //     'name' => 'required|string|max:255',
        //     'birthday' => 'nullable|date', //use php strtotime check date type
        //     'area' => 'nullable|string|max:255',
        //     'fix' => 'required|boolean',
        //     'description' => 'nullable',
        //     'personality' => 'nullable',
        // ]);
        
        // $request['user_id'] = 1;
        // $animal = Animal::create($request->all());

        try{
            //start dbs
            DB::beginTransaction();

            $animal = auth()->user()->animals()->create($request->all());

            $animal = $animal->refresh();

            //auto sign-in likes table
            $animal->likes()->attach(auth()->user()->id);

            //excute dbs comment
            DB::commit();

            return new AnimalResource($animal);

        } catch(\Exception $e) {
            DB::rollBack();

            //record error log
            $errorMessage = 'MESSAGE: ' . $e.getMessage();
            Log::error($errorMessage);

            return response()->json(['error' => 'Process error'],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     *      path="/api/v1/animals/{id}",
     *      operationId="animalShow",
     *      tags={"Animal"},
     *      summary="查看單一動物資源",
     *      description="查看單一動物資源",
     *      @OA\Parameter(
     *          name="id",
     *          description="Animal id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      security={
     *         {
     *              "passport": {}
     *         }
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="請求成功",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Animal"
     *                 )
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="身分驗證未通過"
     *      ),
     * )
     * Display the specified resource.
     *
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function show(Animal $animal)
    {
        //
        // return response($animal,Response::HTTP_OK);
        return new AnimalResource($animal);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function edit(Animal $animal)
    {
        //
    }

    /**
     * @OA\Patch(
     *      path="/api/v1/animals/{id}",
     *      operationId="animalUpdate",
     *      tags={"Animal"},
     *      summary="更新動物資料",
     *      description="更新動物資料",
     *      @OA\Parameter(
     *          name="id",
     *          description="Animal id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *           @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(ref="#/components/schemas/UpdateAnimalRequest"),
     *          )
     *      ),
     *      security={
     *         {
     *              "passport": {}
     *         }
     *      },
     *      @OA\Response(
     *          response=200,
     *          description="請求成功",
     *          @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                      property="data",
     *                      ref="#/components/schemas/Animal"
     *                 )
     *             )
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="找不到資源"
     *       )
     * )
     * 
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAnimalRequest $request, Animal $animal)
    {
        $this->authorize('update',$animal);
        //using UpdateAnimalRequest
        // $this->validate($request, [
        //     'type_id' => 'nullable|exists:type,id',
        //     'name' => 'string|max:255',
        //     'birthday' => 'nullable|date', //use php strtotime check date type
        //     'area' => 'nullable|string|max:255',
        //     'fix' => 'boolean',
        //     'description' => 'nullable|string',
        //     'personality' => 'nullable|string',
        // ]);

        // $request['user_id'] = 1;
        $animal->update($request->all());

        // return response($animal,Response::HTTP_OK);
        return new AnimalResource($animal);   
    }

    /**
     *   @OA\Delete(
     *      path="/api/v1/animals/{id}",
     *      operationId="animalDelete",
     *      tags={"Animal"},
     *      summary="刪除動物資料",
     *      description="刪除動物資料",
     *      @OA\Parameter(
     *          name="id",
     *          description="Animal id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      security={
     *         {
     *              "passport": {}
     *         }
     *      },
     *      @OA\Response(
     *          response=204,
     *          description="刪除成功回傳空值",
     *          @OA\MediaType(
     *              mediaType="application/json"
     *          )
     *       ),
     *      @OA\Response(
     *          response=404,
     *          description="找不到資源"
     *       )
     * )
     *
     * Remove the specified resource from storage.
     *
     * 
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Animal $animal)
    {
        //
        $animal->delete();
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
