<?php
use App\Http\Controllers\api\V1\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

    /*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();});*/
    Route::post('v1/auth/register',[AuthController::class,'create']);
    Route::post('v1/auth/login',[AuthController::class,'login']);

    //Route::get('auth/logout',[AuthController::class,'logout']);
    Route::middleware(['auth:sanctum'])->group(function(){
  //  Route::middleware(['cors'])->group(function(){

        Route::apiResource('v1/categories',App\Http\Controllers\Api\V1\CategoryController::class);
            // ->only(['index','show','destroy','insert']);

        Route::apiResource('v1/suppliers',App\Http\Controllers\Api\V1\SupplierController::class);
        //  ->only(['index','show','destroy']);

        Route::apiResource('v1/document_types',App\Http\Controllers\Api\V1\Document_typeController::class);

        Route::apiResource('v1/medicines',App\Http\Controllers\Api\V1\MedicineController::class);
        Route::apiResource('v1/medicine_entities',App\Http\Controllers\Api\V1\Medicine_entityController::class);
        Route::apiResource('v1/entries',App\Http\Controllers\Api\V1\EntryController::class);
        Route::apiResource('v1/discharges',App\Http\Controllers\Api\V1\DischargeController::class);
        Route::apiResource('v1/pharmaceuticalForms',App\Http\Controllers\Api\V1\PharmaceuticalFormController::class);
        Route::get('v1/auth/user', [AuthController::class, 'getUserInfo']);
        Route::get('v1/auth/logout',[AuthController::class,'logout']);
    });



