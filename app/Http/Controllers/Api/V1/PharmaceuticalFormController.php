<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\PharmaceuticalFormResource;
use App\Models\PharmaceuticalForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PharmaceuticalFormController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pharmaceuticalForms=PharmaceuticalForm::all();
        if($pharmaceuticalForms->count()>0){
         return response()->json([
             'status' => 200,
             'pharmaceuticalForms'=>$pharmaceuticalForms
         ],200);

        }
        else{
         return response()->json([
             'status' => 404,
             'pharmaceuticalForms'=>'No Records Found'
         ],404);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'formafarmaceutica' =>'required |string|max:255',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
            $Pharmaceutical=PharmaceuticalForm::create([
                'formafarmaceutica' => $request['formafarmaceutica'],
            ]);

            if ($Pharmaceutical){
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
        $Pharmaceutical=PharmaceuticalForm::find($id);
        if ($Pharmaceutical){
            return response()->json([
                'status' => 200,
                'category'=>$Pharmaceutical
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
            'formafarmaceutica' =>'required |string|max:255',

        ]);
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        } else{
            $Pharmaceutical=PharmaceuticalForm::find($id);

            if ($Pharmaceutical){

                $Pharmaceutical->update([
                    'formafarmaceutica' => $request['formafarmaceutica'],
                ]);
                return response()->json([
                    'status'=>200,
                    'message'=>"Registro actualizado exitosamente ",
                    'data' => new PharmaceuticalFormResource( $Pharmaceutical)
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
        $Pharmaceutical=PharmaceuticalForm::find($id);

        if ($Pharmaceutical){
            $Pharmaceutical->delete();
            return response()->json([
                'status' => true,
                'message' => 'Tipo de documento eliminado exitosamente'
            ], 200);

        }else{

            return response()->json([
                'status'=>404,
                'message'=>'Registro no encontrado'
                ],404);

        }
    }
}
