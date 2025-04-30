<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Discharge;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\EntryDetail;
use App\Models\DischargeDetail;

class DischargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    try {
        $discharges = Discharge::select(
            'discharges.id',
            'discharges.fecha_egreso',
            'discharges.entidad_id',
            'entities.descripcion as entidad',
            'discharges.tipo_documento_id',
            'document_types.descripcion as tipo',
            'discharges.numero',
            'discharges.receta_id',
            'discharges.servicio_id',
            'discharges.proveedor_id',
            'discharges.observaciones',
            'discharges.usr',
            'discharges.estado_id',
            'discharges.usr_mod',
            'discharges.fhr_mod'
        )
        ->join('entities', 'discharges.entidad_id', '=', 'entities.id')
        ->join('document_types', 'discharges.tipo_documento_id', '=', 'document_types.id')
        ->with(['Details' => function($query) {
            $query->select(
                'discharge_details.egreso_id',
                'discharge_details.ingreso_detalle_id',
                'entry_details.med_entidad_id',
                'medicine_entities.medicamento_id',
                'medicines.liname',
                'medicines.nombre_generico',
                'medicines.formafarmaceutica_id',
                'pharmaceutical_forms.formafarmaceutica',
                'discharge_details.cantidad_solicitada',
                'discharge_details.cantidad_entregada',
                'discharge_details.costo_unitario',
                'discharge_details.costo_total',
                'discharge_details.observaciones',
                'discharge_details.usr',
                'discharge_details.estado_id'
            )
            ->join('entry_details', 'entry_details.id', '=', 'discharge_details.ingreso_detalle_id')
            ->join('medicine_entities', 'medicine_entities.id', '=', 'entry_details.med_entidad_id')
            ->join('medicines', 'medicines.id', '=', 'medicine_entities.medicamento_id')
            ->join('pharmaceutical_forms', 'pharmaceutical_forms.id', '=', 'medicines.formafarmaceutica_id');
        }])
        ->get();

        if ($discharges->isEmpty()) {
            return response()->json([
                'status' => 404,
                'message' => 'No records found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'discharges' => $discharges
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 500,
            'message' => 'Error retrieving discharges',
            'error' => $e->getMessage()
        ], 500);
    }

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fecha_egreso' => 'required|date',
            'entidad_id' => 'required|integer|exists:entities,id',
            'tipo_documento_id' => 'required|integer|exists:document_types,id',
            'receta_id' => 'required|integer',
            'servicio_id' => 'required|integer',
            'proveedor_id' => 'required|integer',
            'observaciones' => 'nullable|string',
            'usr' => 'required|integer',
            'estado_id' => 'required|integer',

            'details' => 'required|array',
            'details.*.ingreso_detalle_id' => 'required|integer|exists:entry_details,id',
            'details.*.cantidad_solicitada' => 'required|integer|min:1',
            'details.*.cantidad_entregada' => 'required|integer|min:1',
            'details.*.costo_unitario' => 'required|numeric|min:0',
            'details.*.costo_total' => 'required|numeric|min:0',
            'details.*.observaciones' => 'nullable|string',
            'details.*.usr' => 'required|integer',
            'details.*.estado_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 422,
                'errors' => $validator->messages()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Obtener el número secuencial para la entidad y tipo de documento
            $numero = Discharge::where('entidad_id', $request->entidad_id)
                ->where('tipo_documento_id', $request->tipo_documento_id)
                ->max('numero') + 1 ?? 1;

            // Crear egreso
            $discharge = Discharge::create([
                'fecha_egreso' => $request->fecha_egreso,
                'entidad_id' => $request->entidad_id,
                'tipo_documento_id' => $request->tipo_documento_id,
                'numero' => $numero,
                'receta_id' => $request->receta_id,
                'servicio_id' => $request->servicio_id,
                'proveedor_id' => $request->proveedor_id,
                'observaciones' => $request->observaciones,
                'usr' => $request->usr,
                'estado_id' => $request->estado_id,
            ]);

            // Manejo de stock PEPS
            foreach ($request->details as $detail) {
                $this->procesarStock($detail, $discharge->id);
            }

            DB::commit();

            return response()->json([
                'message' => 'Egreso registrado exitosamente',
                'data' => $discharge
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Manejo del stock bajo el método PEPS (Primeras Entradas, Primeras Salidas).
     */
    private function procesarStock($detail, $dischargeId)
    {
        $cantidadEntregada = $detail['cantidad_entregada'];
        $costoTotal = 0;

        $entryDetail = EntryDetail::find($detail['ingreso_detalle_id']);

        if (!$entryDetail) {
            throw new \Exception("No se encontró el item con ID: " . $detail['ingreso_detalle_id']);
        }

        $stockDisponible = $entryDetail->stock_actual;
        $cantidadSolicitada = $detail['cantidad_entregada'];

        if ($stockDisponible < $cantidadSolicitada) {
            throw new \Exception("Stock insuficiente para el item con ID: " . $detail['ingreso_detalle_id'] .
                                 " | Stock disponible: $stockDisponible | Cantidad solicitada: $cantidadSolicitada");
        }




        $entryDetails = EntryDetail::where('med_entidad_id', $detail['ingreso_detalle_id'])
            ->where('stock_actual', '>', 0)
            ->orderBy('id', 'asc')
            ->lockForUpdate()
            ->get();




        foreach ($entryDetails as $entryDetail) {
            if ($cantidadEntregada <= 0) break;

            $cantidadUtilizar = min($entryDetail->stock_actual, $cantidadEntregada);
            $costoParcial = $entryDetail->costo_unitario * $cantidadUtilizar;
            $costoTotal += $costoParcial;

            // Actualizar stock
            $entryDetail->decrement('stock_actual', $cantidadUtilizar);

            $cantidadEntregada -= $cantidadUtilizar;
        }
        dd($entryDetails->stock_actual, $detail['cantidad_entregada']);
        if ($cantidadEntregada > 0) {
            throw new \Exception("Stock insuficiente para el item con ID: {$detail['ingreso_detalle_id']}");
        }

        // Registrar detalle del egreso
        DischargeDetail::create([
            'egreso_id' => $dischargeId,
            'ingreso_detalle_id' => $detail['ingreso_detalle_id'],
            'cantidad_solicitada' => $detail['cantidad_solicitada'],
            'cantidad_entregada' => $detail['cantidad_entregada'],
            'costo_unitario' => $costoTotal / $detail['cantidad_entregada'], // Costo promedio
            'costo_total' => $costoTotal,
            'observaciones' => $detail['observaciones'],
            'usr' => $detail['usr'],
            'estado_id' => $detail['estado_id'],
        ]);
    }



   /* public function store(Request $request)
    {
        // Validación de los datos de entrada
    $validator = Validator::make($request->all(), [
        'fecha_egreso' => 'required|date',
        'entidad_id' => 'required|integer',
        'tipo_documento_id' => 'required|integer',
        'receta_id' => 'required|integer',
        'servicio_id' => 'required|integer',
        'proveedor_id' => 'required|integer',
        'observaciones' => 'nullable|string',
        'usr' => 'required|integer',
        'estado_id' => 'required|integer',

        'details.*.ingreso_detalle_id' => 'required|integer',
        'details.*.cantidad_solicitada' => 'required|integer',
        'details.*.cantidad_entregada' => 'required|integer',
        'details.*.costo_unitario' => 'required|numeric',
        'details.*.costo_total' => 'required|numeric',
        'details.*.observaciones' => 'nullable|string',
        'details.*.usr' => 'required|integer',
        'details.*.estado_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages()
        ], 422);
    }

    // Iniciar una transacción
    DB::beginTransaction();

    try {
        // Bloquear la tabla 'discharges' para asegurar la secuencia del número
      //  DB::statement('LOCK TABLES discharges WRITE, entry_details WRITE');

        // Obtener el último número utilizado para la combinación de entidad_id y tipo_documento_id
        $lastNumber = Discharge::where('entidad_id', $request->entidad_id)
                                ->where('tipo_documento_id', $request->tipo_documento_id)
                                ->max('numero');

        // Incrementar el número secuencialmente
        $numero = $lastNumber ? $lastNumber + 1 : 1;

        // Crear el nuevo registro en la tabla discharges
        $discharge = Discharge::create([
            'fecha_egreso' => $request['fecha_egreso'],
            'entidad_id' => $request['entidad_id'],
            'tipo_documento_id' => $request['tipo_documento_id'],
            'numero' => $numero,
            'receta_id' => $request['receta_id'],
            'servicio_id' => $request['servicio_id'],
            'proveedor_id' => $request['proveedor_id'],
            'observaciones' => $request['observaciones'],
            'usr' => $request['usr'],
            'estado_id' => $request['estado_id'],
          // 'usr_mod' =>  $request['usr_mod'],
            //'fhr_mod' => $request['fhr_mod'],
        ]);

        // Proceso PEPS: Gestionar los egresos basados en el inventario PEPS
        foreach ($request['details'] as $detail) {
            $cantidadEntregada = $detail['cantidad_entregada'];
            $costoTotal = 0;

            // Obtener los registros de entrada ordenados por fecha (PEPS)
            $entryDetails = EntryDetail::where('med_entidad_id', $detail['ingreso_detalle_id'])
                                        ->where('stock_actual', '>', 0)
                                        ->orderBy('id', 'asc')
                                        ->lockForUpdate() // Bloquear los registros para la transacción
                                        ->get();


            foreach ($entryDetails as $entryDetail) {
                if ($cantidadEntregada <= 0) break;

                $cantidadUtilizar = min($entryDetail->stock_actual, $cantidadEntregada);

                // Calcular costo parcial
                $costoUnitario = $entryDetail->costo_unitario;
                $costoParcial = $costoUnitario * $cantidadUtilizar;
                $costoTotal += $costoParcial;

                // Reducir el stock actual
                $entryDetail->stock_actual -= $cantidadUtilizar;
                $entryDetail->save();

                // Reducir la cantidad pendiente de entregar
                $cantidadEntregada -= $cantidadUtilizar;
            }

            // Validar si la cantidad entregada supera el stock disponible
            if ($cantidadEntregada > 0) {
                throw new \Exception('Stock insuficiente para ingreso_detalle_id ' . $detail['ingreso_detalle_id']);
            }

            // Crear el detalle de egreso
            DischargeDetail::create([
                'egreso_id' => $discharge->id,
                'ingreso_detalle_id' => $detail['ingreso_detalle_id'],
                'cantidad_solicitada' => $detail['cantidad_solicitada'],
                'cantidad_entregada' => $detail['cantidad_entregada'],
                'costo_unitario' => $costoTotal / $detail['cantidad_entregada'], // Costo promedio ponderado
                'costo_total' => $costoTotal,
                'observaciones' => $detail['observaciones'],
                'usr' => $detail['usr'],
                'estado_id' => $detail['estado_id'],
            ]);
        }

        // Desbloquear las tablas
       // DB::statement('UNLOCK TABLES');

        // Confirmar la transacción
        DB::commit();

        // Devolver una respuesta exitosa
        return response()->json(['message' => 'Discharge and details creado exitosamente', 'discharge' => $discharge], 201);

    } catch (\Exception $e) {
        // Revertir la transacción si ocurre un error
        DB::rollBack();

        // Devolver un mensaje de error
        return response()->json(['error' => 'Error Algo salió mal', 'message' => $e->getMessage()], 500);
    }
    }*/

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            // Obtener el egreso con las relaciones necesarias
            $discharge = Discharge::with([
                'entity:id,descripcion',  // Relación con la tabla entities
                'documentType:id,descripcion',  // Relación con la tabla document_types
                'details' => function ($query) {  // Relación con los detalles de egreso
                    $query->select(
                        'discharge_details.id',
                        'discharge_details.egreso_id',
                        'discharge_details.ingreso_detalle_id',
                        'discharge_details.cantidad_solicitada',
                        'discharge_details.cantidad_entregada',
                        'discharge_details.costo_unitario',
                        'discharge_details.costo_total',
                        'discharge_details.observaciones',
                        'discharge_details.usr',
                        'discharge_details.estado_id',
                        'entry_details.med_entidad_id'
                    )
                    ->join('entry_details', 'entry_details.id', '=', 'discharge_details.ingreso_detalle_id')
                    ->join('medicine_entities', 'medicine_entities.id', '=', 'entry_details.med_entidad_id')
                    ->join('medicines', 'medicines.id', '=', 'medicine_entities.medicamento_id')
                    ->join('pharmaceutical_forms', 'pharmaceutical_forms.id', '=', 'medicines.formafarmaceutica_id')
                    ->addSelect(
                        'medicines.liname',
                        'medicines.nombre_generico',
                        'pharmaceutical_forms.formafarmaceutica'
                    );
                }
            ])->findOrFail($id);  // Encuentra el egreso por su ID o falla si no existe


            if ($discharge){
                return response()->json([
                    'message' => 'Egreso encontrado exitosamente',
                    'data' => $discharge
                ],200);
            }else
            {
              return response()->json([
                'status'=>404,
                'message'=>"Registro no encotrado",
            ],404);

            }


        } catch (\Exception $e) {
            // Manejo de errores y excepciones
            return response()->json([
                'error' => 'Error Algo salió mal',
                'message' => $e->getMessage()
            ], 500);
        }




    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'fecha_egreso' => 'required|date',
        'entidad_id' => 'required|integer|exists:entities,id',
        'tipo_documento_id' => 'required|integer|exists:document_types,id',
        'receta_id' => 'required|integer',
        'servicio_id' => 'required|integer',
        'proveedor_id' => 'required|integer',
        'observaciones' => 'nullable|string',
        'usr' => 'required|integer',
        'estado_id' => 'required|integer',

        'details' => 'required|array',
        'details.*.ingreso_detalle_id' => 'required|integer|exists:entry_details,id',
        'details.*.cantidad_solicitada' => 'required|integer|min:1',
        'details.*.cantidad_entregada' => 'required|integer|min:1',
        'details.*.costo_unitario' => 'required|numeric|min:0',
        'details.*.costo_total' => 'required|numeric|min:0',
        'details.*.observaciones' => 'nullable|string',
        'details.*.usr' => 'required|integer',
        'details.*.estado_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages()
        ], 422);
    }

    DB::beginTransaction();
    try {
        $discharge = Discharge::findOrFail($id);

        $discharge->update([
            'fecha_egreso' => $request->fecha_egreso,
            'entidad_id' => $request->entidad_id,
            'tipo_documento_id' => $request->tipo_documento_id,
            'receta_id' => $request->receta_id,
            'servicio_id' => $request->servicio_id,
            'proveedor_id' => $request->proveedor_id,
            'observaciones' => $request->observaciones,
            'usr' => $request->usr,
            'estado_id' => $request->estado_id,
        ]);

        // Restaurar stock antes de actualizar
        foreach ($discharge->details as $oldDetail) {
            $this->revertStock($oldDetail);
            $oldDetail->delete();
        }

        // Manejo de stock PEPS con nuevos detalles
        foreach ($request->details as $detail) {
            $this->procesarStock($detail, $discharge->id);
        }

        DB::commit();
        return response()->json([
            'message' => 'Egreso actualizado exitosamente',
            'data' => $discharge
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => 'Ocurrió un error',
            'message' => $e->getMessage()
        ], 500);
    }
}

/**
 * Revertir stock al estado previo al egreso.
 */
private function revertStock($detail)
{
    $entryDetail = EntryDetail::find($detail->ingreso_detalle_id);
    if ($entryDetail) {
        $entryDetail->increment('stock_actual', $detail->cantidad_entregada);
    }
}


    /*public function update(Request $request,$id)
    {
         // Validar los datos de entrada
    $validator = Validator::make($request->all(), [
        'fecha_egreso' => 'required|date',
        'entidad_id' => 'required|integer|exists:entities,id',
        'tipo_documento_id' => 'required|integer|exists:document_types,id',
        'receta_id' => 'nullable|integer',
        'Servicio_id' => 'nullable|integer',
        'Proveedor_id' => 'nullable|integer',
        'observaciones' => 'nullable|string',
        'usr' => 'required|integer',
        'estado_id' => 'required|integer',
        'details' => 'required|array',
        'details.*.ingreso_detalle_id' => 'required|integer|exists:entry_details,id',
        'details.*.cantidad_solicitada' => 'required|integer|min:1',
        'details.*.cantidad_entregada' => 'required|integer|min:1',
        'details.*.costo_unitario' => 'required|numeric|min:0',
        'details.*.costo_total' => 'required|numeric|min:0',
        'details.*.observaciones' => 'nullable|string',
        'details.*.usr' => 'required|integer',
        'details.*.estado_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => 422,
            'errors' => $validator->messages()
        ], 422);
    }

    // Iniciar una transacción
    DB::beginTransaction();

    try {
        // Encontrar el egreso por su ID
        $discharge = Discharge::findOrFail($id);

        // Actualizar el egreso
        $discharge->update([
            'fecha_egreso' => $request->fecha_egreso,
            'entidad_id' => $request->entidad_id,
            'tipo_documento_id' => $request->tipo_documento_id,
            'receta_id' => $request->receta_id,
            'Servicio_id' => $request->Servicio_id,
            'Proveedor_id' => $request->Proveedor_id,
            'observaciones' => $request->observaciones,
            'usr' => $request->usr,
            'estado_id' => $request->estado_id,
            'usr_mod' => auth()->id(), // Registrar el usuario que modifica
            'fhr_mod' => now(), // Fecha de modificación
        ]);

        // Recorrer los detalles y actualizar
        foreach ($request->details as $detail) {
            $dischargeDetail = DischargeDetail::where('egreso_id', $discharge->id)
                                              ->where('ingreso_detalle_id', $detail['ingreso_detalle_id'])
                                              ->first();

            // Si no existe, crear el detalle
            if (!$dischargeDetail) {
                $dischargeDetail = new DischargeDetail();
                $dischargeDetail->egreso_id = $discharge->id;
                $dischargeDetail->ingreso_detalle_id = $detail['ingreso_detalle_id'];
            }

            // Actualizar el detalle
            $dischargeDetail->cantidad_solicitada = $detail['cantidad_solicitada'];
            $dischargeDetail->cantidad_entregada = $detail['cantidad_entregada'];
            $dischargeDetail->costo_unitario = $detail['costo_unitario'];
            $dischargeDetail->costo_total = $detail['costo_total'];
            $dischargeDetail->observaciones = $detail['observaciones'];
            $dischargeDetail->usr = $detail['usr'];
            $dischargeDetail->estado_id = $detail['estado_id'];
            $dischargeDetail->save();

            // Actualizar el stock actual en la tabla entry_details
            $entryDetail = EntryDetail::findOrFail($detail['ingreso_detalle_id']);

            // Validar que el stock actual sea suficiente
            if ($entryDetail->stock_actual < $detail['cantidad_entregada']) {
                throw new \Exception("Stock insuficiente para el item con ID: {$detail['ingreso_detalle_id']}");
            }

            // Restar la cantidad entregada del stock actual
            $entryDetail->stock_actual -= $detail['cantidad_entregada'];
            $entryDetail->save();
        }

        // Confirmar la transacción
        DB::commit();

        return response()->json([
            'status' => 200,
            'message' => 'Egreso actualizado exitosamente',
            'data' => $discharge
        ], 200);


    } catch (\Exception $e) {
        // Revertir la transacción si ocurre un error
        DB::rollBack();

        return response()->json([
            'error' => 'Error Algo salió mal',
            'message' => $e->getMessage()
        ], 500);
    }
    }*/

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Discharge $discharge)
    {
        //
    }
}
