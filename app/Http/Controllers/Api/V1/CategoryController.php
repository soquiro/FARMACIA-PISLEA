<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

use App\Http\Resources\V1\CategoryResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // obtien un coleccion por eso usamos collection
        return  CategoryResource::collection(Category::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category=Category::create([
            'descripcion' => $request['descripcion'],

        ]);


    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //






    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // paso 1 eliminar la categoria
        $category->delete();

       //paso 2 dar  un feedback
        return response()->json([
            'message'=>'Success'
            ],204);
    }
}
