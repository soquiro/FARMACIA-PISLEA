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
        //return  Document_typeResource::collection(Document_type::latest()->paginate());
        $documentTypes = Document_Type::with('category:id,descripcion')
            ->select('id', 'categoria_id', 'descripcion', 'cod_servicio', 'usr', 'estado_id')
            ->get()
            ->map(function ($documentType) {
                return [
                    'id' => $documentType->id,
                    'categoria_id' => $documentType->categoria_id,
                    'categoria' => $documentType->category ? $documentType->category->descripcion : null,
                    'descripcion' => $documentType->descripcion,
                    'cod_servicio' => $documentType->cod_servicio,
                    'usr' => $documentType->usr,
                    'estado_id' => $documentType->estado_id,
                ];
            });


        if($documentTypes->count()>0){
            return response()->json([
                'status' => 200,
                'documentTypes'=>$documentTypes
            ],200);

           }
           else{
            return response()->json([
                'status' => 404,
                'documentTypes'=>'No Records Found'
            ],404);
           }

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
