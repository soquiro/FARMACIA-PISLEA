<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // obtien un coleccion por eso usamos collection
       // return  CategoryResource::collection(Category::latest()->paginate());
       $categories=Category::all();
       if($categories->count()>0){
        return response()->json([
            'status' => 200,
            'categories'=>$categories
        ],200);

       }
       else{
        return response()->json([
            'status' => 404,
            'categories'=>'No Records Found'
        ],404);
       }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {  /* $category=Category::create([
            'descripcion' => $request['descripcion'],

        ]);*/
        $validator=Validator::make($request->all(),[
            'descripcion' =>'required |string|max:300',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
            $category=Category::create([
                'descripcion' => $request['descripcion'],
            ]);

            if ($category){
                return response()->json([
                    'status'=>200,
                    'message'=>"creado exitosamente ",
                ]);
            } else{
                return response()->json([
                    'status'=>500,
                    'message'=>"Algo salio mal ",
                ]);
            }
        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //return new CategoryResource($category);
        $category=Category::find($id);
        if ($category){
            return response()->json([
                'status' => 200,
                'category'=>$category
            ],200);
        }else
        {
          return response()->json([
            'status'=>404,
            'message'=>"Registro no encotrado",
        ],404);

        }

    }
    public function edit($id)
    {
        //return new CategoryResource($category);
        $category=Category::find($id);
        if ($category){
            return response()->json([
                'status' => 200,
                'category'=>$category
            ],200);
        }else
        {
          return response()->json([
            'status'=>404,
            'message'=>"Registro no encotrado",
        ],404);

        }

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {

        $validator=Validator::make($request->all(),[
            'descripcion' =>'required |string|max:300',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
            $category=Category::find($id);

            if ($category){

                $category->update([
                    'descripcion' => $request['descripcion'],
                ]);
                return response()->json([
                    'status'=>200,
                    'message'=>"Registro actualizado exitosamente ",
                ],200);
            } else{
                return response()->json([
                    'status'=>404,
                    'message'=>"Registro no encontrado",
                ],404);
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // paso 1 eliminar la categoria
        $category=Category::find($id);

        if($category){
            $category->delete();

            return response()->json([
                'status'=>200,
                'message'=>'Registro eliminado exitosamente'
                ],200);

        }else{

            return response()->json([
                'status'=>404,
                'message'=>'Registro no encontrado'
                ],404);

        }

       //paso 2 dar  un feedback

    }
}
