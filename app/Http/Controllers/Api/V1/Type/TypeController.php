<?php

namespace App\Http\Controllers\Api\V1\Type;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypeCollection;
use App\Http\Resources\TypeResource;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class TypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except' => ['index','show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        //
        // $type = Type::get();
        // $type = Type::find('1'); //search with id=1
        
        $types = Type::select('id','name','sort')->get();
        return new TypeCollection($types);
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
        $this->validate($request , [
            'name' => [
                'required' ,
                'max:50',
                Rule::unique('types','name') //check unique name
            ],
            'sort' => 'nullable|integer'
        ]);

        if(!isset($request->sort)) {
            $max = Type::max('sort');
            $request['sort'] = $max + 1;
        }

        $type = Type::create($request->all());

        return response([
            'data' => $type
        ] , Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
    * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function show(Type $type)
    {
        //
        return response()->json(['data'=>$type],Response::HTTP_OK); //this is btr way to do response json
        // return response([
        //     'data' => $type
        // ] , Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function edit(Type $type)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Type $type)
    {
        //
        $this->validate($request , [
            'name' => [
                'max:50',
                Rule::unique('types','name')->ignore($type->name,'name') //to check same to old or unique
            ],
            'sort' => 'nullable|integer'
        ]);

        $type->update($request->all());
        return new TypeResource($type);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Type  $type
     * @return \Illuminate\Http\Response
     */
    public function destroy(Type $type)
    {
        //
        $type->delete();
        return response(null,Response::HTTP_NO_CONTENT);
    }
}
