<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\V1\SupplierResource;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       // return  SupplierResource::collection(Supplier::latest()->paginate());

        $suppliers=Supplier::all();
        if($suppliers->count()>0){
         return response()->json([
             'status' => 200,
             'suppliers'=>$suppliers
         ],200);

        }
        else{
         return response()->json([
             'status' => 404,
             'suppliers'=>'No Records Found'
         ],404);
        }
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'nombre' =>'required |string|max:300',
            'nit' =>'required |number|max:18',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
            $supplier=Supplier::create([
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

            if ($supplier){
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
    public function show( $id)
    {
        $supplier=Supplier::find($id);
        if ($supplier){
            return response()->json([
                'status' => 200,
                'supplier'=>$supplier
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
        $supplier=Supplier::find($id);
        if ($supplier){
            return response()->json([
                'status' => 200,
                'supplier'=>$supplier
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
            'nombre' =>'required |string|max:300',
          //  'nit' =>'required |number|max:18',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
            $supplier=Supplier::find($id);
            if ($supplier){
                $supplier->update([
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
                return response()->json([
                    'status'=>200,
                    'message'=>"Registro actualizado exitosamente ",
                ],200);
            }
            else{
                return response()->json([
                    'status'=>404,
                    'message'=>"Registro no encontrado",
                ],404);
            }


            if ($supplier){
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
       // paso 1 eliminar la categoria
       $supplier=Supplier::find($id);

       if($supplier){
            $supplier->delete();

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
    }

}
