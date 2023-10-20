<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Document_type;
use Illuminate\Http\Request;

use App\Http\Resources\V1\Document_typeResource;

class Document_typeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  Document_typeResource::collection(Document_type::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Document_type $document_type)
    {
        return new Document_typeResource($document_type);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Document_type $document_type)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Document_type $document_type)
    {
          // paso 1 eliminar la categoria
          $document_type->delete();

          //paso 2 dar  un feedback
           return response()->json([
               'message'=>'Success'
               ],204);
    }
}
