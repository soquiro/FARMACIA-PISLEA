<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;

class AuthController extends Controller
{
    public function create(Request $request){
        $rules =[
            'name'=>'required|string|max:100',
            'email'=>'required|string|email|max:100|unique:users',
            'password'=>'required|string|min:8',
        ];
        $validator=Validator::make($request->input(),$rules);
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->messages()
            ],422);
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)

        ]);
        return response()->json([
            'status'=>true,
            'message'=>'Usuario creado satisfactoriamente',
            'token'=>$user->createToken('API TOKEN')->plainTextToken
        ],200);
    }

    public function login(Request $request){
        $rules =[
            'email'=>'required|string|email|max:100',
            'password'=>'required|string',
        ];
        $validator=Validator::make($request->input(),$rules);
        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'errors'=>$validator->errors()->all()
            ],400);
        }
        if (!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'status'=>false,
                'errors'=>['No autorizado']
            ],401);
        }
        $user=User::where('email',$request->email)->first();
        return response()->json([
            'status'=>true,
            'message'=>'Usuario logeado satisfactoriamente',
            'data'=>$user,
            'token'=>$user->createToken('API TOKEN')->plainTextToken
        ],200);


    }
    public function getUserInfo()
{
    // Verifica si el usuario está autenticado
    if (auth()->check()) {
        $user = auth()->user(); // Obtiene el usuario autenticado
        return response()->json([
            'status' => true,
            'message' => 'Información del usuario obtenida con éxito',
            'data' => $user
        ], 200);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'No autorizado',
        ], 401);
    }
}
    public function logout(){
         // Verifica si el usuario está autenticado
    if (auth()->check()) {
        try {
            // Elimina todos los tokens del usuario autenticado
            auth()->user()->tokens()->delete();

            // Responde con un mensaje de éxito
            return response()->json([
                'status' => true,
                'message' => 'Usuario salió satisfactoriamente',
            ], 200);
        } catch (\Exception $e) {
            // Maneja cualquier error que pueda ocurrir
            return response()->json([
                'status' => false,
                'message' => 'Hubo un problema al intentar cerrar la sesión.',
                'error' => $e->getMessage(),
            ], 500);
        }
    } else {
        // Respuesta cuando no hay un usuario autenticado
        return response()->json([
            'status' => false,
            'message' => 'Ningún usuario autenticado.',
        ], 401);
    }



    }




}
