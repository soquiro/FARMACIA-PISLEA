<?php

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
    return $request->user();*/

    Route::apiResource('v1/categories',App\Http\Controllers\Api\V1\CategoryController::class);
       // ->only(['index','show','destroy','insert']);

    Route::apiResource('v1/suppliers',App\Http\Controllers\Api\V1\SupplierController::class);
      //  ->only(['index','show','destroy']);

    Route::apiResource('v1/document_types',App\Http\Controllers\Api\V1\Document_typeController::class);

