<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Http\Resources\V1\MedicineResource;

class MedicineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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


    }


    public function store(Request $request)
    {
        //
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Medicine $medicine)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $id)
    {
        /* // paso 1 eliminar el medicamento
         $medicine->delete();

         //paso 2 dar  un feedback
          return response()->json([
              'message'=>'Success'
              ],204);*/
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
