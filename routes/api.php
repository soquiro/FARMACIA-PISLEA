<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\DocumentTypeController;
use App\Http\Controllers\Api\V1\PharmaceuticalFormController;
use App\Http\Controllers\Api\V1\MedicineController;
use App\Http\Controllers\Api\V1\MedicinePackageController;
use App\Http\Controllers\Api\V1\EntryController;
use App\Http\Controllers\Api\V1\DischargeController;
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


        Route::apiResource('v1/categories',CategoryController::class);
            // ->only(['index','show','destroy','insert']);

        Route::apiResource('v1/suppliers',SupplierController::class);
        //  ->only(['index','show','destroy']);

        Route::apiResource('v1/documentTypes',DocumentTypeController::class);
        Route::apiResource('v1/pharmaceuticalForms',PharmaceuticalFormController::class);
        Route::apiResource('v1/medicines',MedicineController::class);
        Route::apiResource('v1/medicinePackages',MedicinePackageController::class);
        Route::get('v1/entries/with-stock', [EntryController::class, 'entryDetailsConStock']);
        Route::apiResource('v1/entries', EntryController::class);


        Route::apiResource('v1/discharges',DischargeController::class);

        Route::get('v1/auth/user', [AuthController::class, 'getUserInfo']);
        Route::get('v1/auth/logout',[AuthController::class,'logout']);
    });


