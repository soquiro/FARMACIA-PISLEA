<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

use App\Http\Resources\V1\SupplierResource;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return  SupplierResource::collection(Supplier::latest()->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $category=Supplier::create([
            'nombre' => $request['nombre'],
            'nit' => $request['nit'],
            'direccion' => $request['direccion'],
            'telefono' => $request['telefono'],
            'persona_contacto' => $request['persona_contacto'],
            'celular' => $request['celular'],
            'email' => $request['email'],
            'observaciones' => $request['observaciones'],
            'usr' => $request['usr'],
            'estado_id' => $request['estado_id']

        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        // paso 1 eliminar la categoria
        $supplier->delete();

       //paso 2 dar  un feedback
        return response()->json([
            'message'=>'Success'
            ],204);
    }
}
