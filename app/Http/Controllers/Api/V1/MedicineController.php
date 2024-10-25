<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Http\Resources\V1\MedicineResource;
use Illuminate\Support\Facades\Validator;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /*public function index()
    {
       // return  MedicineResource::collection(Medicine::latest()->paginate());

     //   $medicines=Medicine::all();
        $medicines=Medicine::select('medicines.*','form.formafarmaceutica','doc.descripcion')
        ->join('pharmaceutical_forms as form','form.id','=','medicines.formafarmaceutica_id')
        ->join('document_types as doc','doc.id','=','medicines.categoriamed_id')
        ->get();

        if($medicines->count()>0){
         return response()->json([
             'status' => 200,
             'medicines'=>$medicines
         ],200);

        }
        else{
         return response()->json([
             'status' => 404,
             'medicines'=>'No Records Found'
         ],404);
        }


    }*/
    public function index(Request $request)
{
    // Obtener los parámetros de consulta
    $nombre_generico = $request->query('nombre_generico');
    $formafarmaceutica_id = $request->query('formafarmaceutica_id');
    $categoriamed_id = $request->query('categoriamed_id');

    // Construir la consulta base
    $query = Medicine::select('medicines.*', 'form.formafarmaceutica', 'doc.descripcion')
        ->join('pharmaceutical_forms as form', 'form.id', '=', 'medicines.formafarmaceutica_id')
        ->join('document_types as doc', 'doc.id', '=', 'medicines.categoriamed_id');

    // Aplicar filtros según los parámetros
    if ($nombre_generico) {
        $query->where('medicines.nombre_generico', 'like', "%$nombre_generico%");
    }

    if ($formafarmaceutica_id) {
        $query->where('medicines.formafarmaceutica_id', $formafarmaceutica_id);
    }

    if ($categoriamed_id) {
        $query->where('medicines.categoriamed_id', $categoriamed_id);
    }

    // Paginación de resultados
    $medicines = $query->latest()->paginate();

    if ($medicines->count() > 0) {
        return response()->json([
            'status' => 200,
            'medicines' => $medicines
        ], 200);
    } else {
        return response()->json([
            'status' => 404,
            'medicines' => 'No Records Found'
        ], 404);
    }
}


    public function store(Request $request)
    {
     // Definición de reglas de validación
     $validator=Validator::make($request->all(),[
        'liname' => 'required|string|max:255',
        'nombre_generico' => 'required|string|max:255',
        'observaciones' => 'nullable|string',
        'formafarmaceutica_id' => 'required|exists:pharmaceutical_forms,id',
        'categoriamed_id' => 'required|exists:document_types,id',


    ]);
     // Manejo de errores de validación
    if($validator->fails()){
        return response()->json([
            'status'=>422,
            'errors'=>$validator->messages()
        ],422);
    } else{
           // Creación del nuevo registro en la tabla medicine_entities
        $medicine = Medicine::create([
            'liname' => $request['liname'],
            'nombre_generico' => $request['nombre_generico'],
            'observaciones' => $request['observaciones'],
            'formafarmaceutica_id' => $request['formafarmaceutica_id'],
            'categoriamed_id' => $request['categoriamed_id'],
            'usr' => $request['usr'],
            'estado_id' => $request['estado_id']
        ]);

        if ($medicine ){
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
    public function show(Medicine $id)
    {
       // return new MedicineResource($medicine);

        $medicine=Medicine::find($id);
        if ($medicine){
            return response()->json([
                'status' => 200,
                'medicine'=>$medicine
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
       // Busca el registro por ID
        $medicine = Medicine::find($id);
        // Verifica si el registro fue encontrado
        if ($medicine){
               // Retorna el registro encontrado para su edición
            return response()->json([
                'status' => 200,
                'medicine'=>$medicine
            ],200);// Código 200 para éxito
        }else
        {
          return response()->json([
            'status'=>404,
            'message'=>"Registro no encotrado",
        ],404);// Código 404 para recurso no encontrado

        }

    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $validator=Validator::make($request->all(),[
            'liname' => 'required|string|max:255',
            'nombre_generico' => 'required|string|max:255',
            'observaciones' => 'nullable|string',
            'formafarmaceutica_id' => 'required|exists:pharmaceutical_forms,id',
            'categoriamed_id' => 'required|exists:document_types,id',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
               // Busca el registro por ID
            $medicine = Medicine::find($id);
            if ($medicine){
                $medicine->update([
                    'liname' => $request['liname'],
                    'nombre_generico' => $request['nombre_generico'],
                    'observaciones' => $request['observaciones'],
                    'formafarmaceutica_id' => $request['formafarmaceutica_id'],
                    'categoriamed_id' => $request['categoriamed_id'],
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


            if ($medicine){
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
    public function destroy(Medicine $id)
    {

              $medicine=Medicine::find($id);

              if($medicine){
                  $medicine->delete();

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
