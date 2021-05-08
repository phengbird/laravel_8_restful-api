<?php

namespace App\Http\Controllers;

use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use phpDocumentor\Reflection\Types\Nullable;
use Symfony\Component\HttpFoundation\Response;

class AnimalController extends Controller
{
    /**
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
        if(Cache::has($fullUrl)){
            return Cache::get($fullUrl);
        }

        //giving default value
        $limit = $request->limit ?? 10;

        //create search dbs , 
        $query = Animal::query();

        //filter way
        if(isset($request->filters)){
            $filters = explode(',',$request->filters);
            foreach($filters as $key => $filter){
                list($key,$value) = explode(':',$filter);
                $query->where($key,'like',"%$value%");
            }
        }

        //sort way
        if(isset($request->sorts)){
            $sorts = explode(',',$request->sorts);
            foreach($sorts as $key => $sort){
                list($key , $value) = explode(':',$sort);
                if($value == 'asc' || $value == 'desc'){
                    $query->orderBy($key,$value);
                }
            }
        } else {
            $query->orderBy('id','desc');
        }

        //show out with desc
        $animal = $query->orderBy('id','desc')
            ->paginate($limit) //using page to show how many by $limit
            ->appends($request->query());
        
        //if dont have cache , then set 60sec timer , and named
        return Cache::remember($fullUrl,60,function() use ($animal){
            return response($animal,Response::HTTP_OK);    
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request,[
            'type_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date', //use php strtotime check date type
            'area' => 'nullable|string|max:255',
            'fix' => 'required|boolean',
            'description' => 'nullable',
            'personality' => 'nullable',
        ]);

        $request['user_id'] = 1;
        $animal = Animal::create($request->all());
        $animal = $animal->refresh();
        return response($animal,Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function show(Animal $animal)
    {
        //
        return response($animal,Response::HTTP_OK);
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Animal $animal)
    {
        //
        $this->validate($request,[
            'type_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'birthday' => 'nullable|date', //use php strtotime check date type
            'area' => 'nullable|string|max:255',
            'fix' => 'boolean',
            'description' => 'nullable',
            'personality' => 'nullable',
        ]);

        $request['user_id'] = 1;
        $animal->update($request->all());
        return response($animal,Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Animal  $animal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Animal $animal)
    {
        //
        $animal->delete();
        return response(null,Response::HTTP_NO_CONTENT);
    }
}
