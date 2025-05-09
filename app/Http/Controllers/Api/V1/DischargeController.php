<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\DischargeRequest;
use App\Http\Resources\V1\DischargeResource;
use App\Http\Requests\V1\UpdateDischargeRequest;
use App\Models\Discharge;
use App\Models\DischargeDetail;
use App\Models\EntryDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class DischargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Discharge::with([
            'entity',
            'documentType',
            'supplier',
            'estate',
            'user',
            'service',
            'dischargeDetails.entryDetails.medicine'
        ])->orderByDesc('id');

        // Filtro por fechas
        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('fecha_egreso', [$request->fecha_inicio, $request->fecha_fin]);
        }

        // Filtro por tipo de documento
        if ($request->filled('tipo_documento_id')) {
            $query->where('tipo_documento_id', $request->tipo_documento_id);
        }

        $discharges = $query->get();

        return DischargeResource::collection($discharges);
    }

    public function store(DischargeRequest $request)
    {
        DB::beginTransaction();
        try {
            // Generar número correlativo
            $numero = Discharge::where('entidad_id', $request->entidad_id)
                ->where('tipo_documento_id', $request->tipo_documento_id)
                ->max('numero') + 1;

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
                'usr' => auth()->id(),
                'estado_id' => 28, // ACTIVO
            ]);

            foreach ($request->discharge_details as $detail) {
                $entryDetail = EntryDetail::find($detail['ingreso_detalle_id']);

                if (!$entryDetail) {
                    throw new \Exception('Ingreso detalle no encontrado.');
                }

                if ($entryDetail->stock_actual < $detail['cantidad_entregada']) {
                    throw new \Exception('Stock insuficiente para el medicamento.');
                }

                // actualizar stock
                $entryDetail->stock_actual -= $detail['cantidad_entregada'];
                $entryDetail->save();

                // Crear detalle de egreso
                DischargeDetail::create([
                    'egreso_id' => $discharge->id,
                    'ingreso_detalle_id' => $detail['ingreso_detalle_id'],
                    'cantidad_solicitada' => $detail['cantidad_solicitada'],
                    'cantidad_entregada' => $detail['cantidad_entregada'],
                    'costo_unitario' => $detail['costo_unitario'],
                    'costo_total' => $detail['costo_total'],
                    'observaciones' => $detail['observaciones'],
                    'usr' => auth()->id(),
                    'estado_id' => 28, // ACTIVO
                ]);
            }

            DB::commit();
            return new DischargeResource($discharge->load('dischargeDetails', 'entity', 'documentType', 'supplier', 'estate', 'user', 'service'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error al guardar el egreso: ' . $e->getMessage()
            ],422);
        }
    }

    public function show($id)
    {
        $discharge = Discharge::with([
            'entity',
            'documentType',
            'supplier',
            'estate',
            'user',
            'service',
            'dischargeDetails.entryDetails.medicine'
        ])->findOrFail($id);

        return new DischargeResource($discharge);
    }


public function update(UpdateDischargeRequest $request, Discharge $discharge)
{
    if ($discharge->estado_id !== 28) {
        return response()->json([
            'message' => 'Solo se puede modificar un egreso en estado ACTIVO.'
        ], 422);
    }

    $discharge->update([
        'fecha_egreso' => $request->fecha_egreso,
        'observaciones' => $request->observaciones,
        'usr_mod' => auth()->id(),
        'fhr_mod' => now()
    ]);

    return response()->json([
        'message' => 'Egreso actualizado correctamente.',
        'data' => new DischargeResource(
            $discharge->load(
                'entity',
                'documentType',
                'supplier',
                'estate',
                'user',
                'service',
                'dischargeDetails.entryDetails.medicine'
            )
        )
    ]);
}
    public function destroy(Discharge $discharge)
    {
        //
    }
}
