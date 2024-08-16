<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MedicineEntity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Medicine_entityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medicineEntities=MedicineEntity::select('medicines.id',
        'medicines.liname',
        'medicines.nombre_generico',
        'pharmaceutical_forms.formafarmaceutica',
        'medicine_entities.stockmax',
        'medicine_entities.stockmin',
        'medicine_entities.darmax',
        'medicine_entities.darmin',
        'document_types.descripcion as categoria',
        'entities.descripcion as entidad'
        )
        ->join('medicines', 'medicine_entities.medicamento_id', '=', 'medicines.id')
        ->join('pharmaceutical_forms', 'medicines.formafarmaceutica_id', '=', 'pharmaceutical_forms.id')
        ->join('document_types', 'medicines.categoriamed_id', '=', 'document_types.id')
        ->join('entities', 'medicine_entities.entidad_id', '=', 'entities.id')
        ->get();


        if($medicineEntities->count()>0){
         return response()->json([
             'status' => 200,
             'medicines'=>$medicineEntities
         ],200);

        }
        else{
         return response()->json([
             'status' => 404,
             'medicines'=>'No Records Found'
         ],404);
        }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Definición de reglas de validación
        $validator=Validator::make($request->all(),[
            'medicamento_id' => 'required|exists:medicines,id',
            'entidad_id' => 'required|exists:entities,id',
            'stockmax' => 'required|integer|min:0',
            'stockmin' => 'required|integer|min:0',
            'darmax' => 'required|integer|min:0',
            'darmin' => 'required|integer|min:0',


        ]);
         // Manejo de errores de validación
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
               // Creación del nuevo registro en la tabla medicine_entities
            $medicineEntity = MedicineEntity::create([
                'medicamento_id' => $request['medicamento_id'],
                'entidad_id' => $request['entidad_id'],
                'stockmax' => $request['stockmax'],
                'stockmin' => $request['stockmin'],
                'darmax' => $request['darmax'],
                'darmin' => $request['darmin'],
                'usr' => $request['usr'],
                'estado_id' => $request['estado_id']
            ]);

            if ($medicineEntity ){
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

    public function show( $id)
    {
        // Busca el registro por ID
        $medicineEntity = MedicineEntity::find($id);
        // Verifica si el registro fue encontrado
        if ($medicineEntity){
            // Retorna el registro encontrado
            return response()->json([
                'status' => 200,
                'medicineEntity'=>$medicineEntity
            ],200); // Código 200 para éxito
        }else
        {
          return response()->json([
            'status'=>404,
            'message'=>"Registro no encotrado",
        ],404); // Código 404 para recurso no encontrado
        }

    }
    public function edit($id)
    {
       // Busca el registro por ID
        $medicineEntity = MedicineEntity::find($id);
        // Verifica si el registro fue encontrado
        if ($medicineEntity){
               // Retorna el registro encontrado para su edición
            return response()->json([
                'status' => 200,
                'medicineEntity'=>$medicineEntity
            ],200);// Código 200 para éxito
        }else
        {
          return response()->json([
            'status'=>404,
            'message'=>"Registro no encotrado",
        ],404);// Código 404 para recurso no encontrado

        }

    }
    public function update(Request $request, int $id)
    {
        $validator=Validator::make($request->all(),[
            'medicamento_id' => 'required|exists:medicines,id',
            'entidad_id' => 'required|exists:entities,id',
            'stockmax' => 'required|integer|min:0',
            'stockmin' => 'required|integer|min:0',
            'darmax' => 'required|integer|min:0',
            'darmin' => 'required|integer|min:0',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
               // Busca el registro por ID
            $medicineEntity = MedicineEntity::find($id);
            if ($medicineEntity){
                $medicineEntity->update([
                    'medicamento_id' => $request['medicamento_id'],
                    'entidad_id' => $request['entidad_id'],
                    'stockmax' => $request['stockmax'],
                    'stockmin' => $request['stockmin'],
                    'darmax' => $request['darmax'],
                    'darmin' => $request['darmin'],
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


            if ($medicineEntity){
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

        $medicineEntity = MedicineEntity::find($id);
        if($medicineEntity){
            $medicineEntity->delete();

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
