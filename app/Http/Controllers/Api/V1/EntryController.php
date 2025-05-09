<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EntryRequest;
use App\Http\Resources\V1\EntryResource;
use App\Models\Entry;
use App\Models\EntryDetail;
use App\Models\DischargeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class EntryController extends Controller
{

    public function index(Request $request)
{
    $query = Entry::with([
        'entryDetails',
        'entity',
        'documentType',
        'supplier',
        'estate'
    ])->orderByDesc('id');


    if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
        $query->whereBetween('fecha_ingreso', [$request->fecha_inicio, $request->fecha_fin]);
    }


    if ($request->filled('tipo_documento_id')) {
        $query->where('tipo_documento_id', $request->tipo_documento_id);
    }

    $entries = $query->get();

    return EntryResource::collection($entries);
}

    public function store(EntryRequest $request)
    {
        DB::beginTransaction();
    try {
        // Validar los datos explícitamente (aunque ya fueron validados, esto permite trabajar con $validated)
        $validated = $request->validated();

        // Obtener el número consecutivo
        $numero = $this->generateNumero($validated['entidad_id'], $validated['tipo_documento_id']);

        // Crear el Entry
        $entry = Entry::create([
            'entidad_id' => $validated['entidad_id'],
            'tipo_documento_id' => $validated['tipo_documento_id'],
            'numero' => $numero,
            'fecha_ingreso' => $validated['fecha_ingreso'],
            'proveedor_id' => $validated['proveedor_id'],
            'num_factura' => $validated['num_factura'] ?? null,
            'observaciones' => $validated['observaciones'] ?? null,
            'usr' => auth()->id(),
            'estado_id' => 27, // PENDIENTE
        ]);

        // Crear detalles
        foreach ($validated['entry_details'] as $detail) {
            $entry->entryDetails()->create(array_merge($detail, [
                'estado_id' => 27,
                'usr' => auth()->id(), // añade el usuario también a cada detalle
            ]));
        }

        DB::commit();
        return new EntryResource($entry->fresh('entryDetails'));
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Error al guardar', 'error' => $e->getMessage()], 500);
    }
   }
   public function update(EntryRequest $request, $id)
   {
       $entry = Entry::with('entryDetails')->findOrFail($id);

       // Solo se puede modificar si todos tienen estado pendiente
       if ($entry->estado_id != 27 && $entry->estado_id != 29 || $entry->entryDetails->where ('estado_id', '===', 28)->count()) {
           return response()->json(['message' => 'Solo puede modificarse si está en estado pendiente.'], 403);
       }

       return DB::transaction(function () use ($entry, $request) {
           $data = $request->validated();

           // Actualizar datos del ingreso
           $entry->update([
               ...$data,
               'usr_mod' => auth()->id(),
               'fhr_mod' => now(),
           ]);

           $idsEnviados = collect($data['entry_details'])->pluck('id')->filter()->toArray();

           // Procesar detalles existentes
           foreach ($entry->entryDetails as $existingDetail) {
               if (in_array($existingDetail->id, $idsEnviados)) {
                   $detailData = collect($data['entry_details'])->firstWhere('id', $existingDetail->id);
                   $existingDetail->update([
                       ...$detailData,
                       'usr_mod' => auth()->id(),
                       'fhr_mod' => now(),
                   ]);
               } else {
                   $isUsed = DischargeDetail::where('ingreso_detalle_id', $existingDetail->id)->exists();
                   if (!$isUsed) {
                       $existingDetail->update([
                           'estado_id' => 29, // ANULADO
                           'usr_mod' => auth()->id(),
                           'fhr_mod' => now(),
                       ]);
                   }
               }
           }

           // Agregar nuevos detalles
           foreach ($data['entry_details'] as $detail) {
               if (!isset($detail['id'])) {
                   EntryDetail::create([
                       ...$detail,
                       'ingreso_id' => $entry->id,
                       'estado_id' => 27,
                       'usr' => auth()->id(),
                   ]);
               }
           }

           return response()->json([
               'message' => 'Registro actualizado con éxito.',
               'entry' => new EntryResource(
                   $entry->refresh()->load([
                       'entryDetails.medicine',
                       'entryDetails.estate',
                       'entryDetails.user',
                       'entity',
                       'documentType',
                       'supplier',
                       'estate'
                   ])
               )
           ]);
       });
   }


    public function show($id)
    {
        $entry = Entry::with('entryDetails', 'entity', 'documentType', 'supplier', 'estate')->findOrFail($id);
        return new EntryResource($entry);
    }



    public function destroy($id)
    {
        $entry = Entry::with('entryDetails')->findOrFail($id);

        // No eliminar si está activo
        if ($entry->estado_id != 27) {
            return response()->json(['message' => 'Solo puede anularse si está en estado pendiente.'], 403);
        }

        // Validar que ningún detalle esté en discharge
        foreach ($entry->entryDetails as $detail) {
            if (DischargeDetail::where('ingreso_detalle_id', $detail->id)->exists()) {
                return response()->json(['message' => 'Uno o más detalles ya están usados en egresos y no pueden anularse.'], 403);
            }
        }

        return DB::transaction(function () use ($entry) {
            $entry->update([
                'estado_id' => 29, // ANULADO
                'usr_mod' => auth()->id(),
                'fhr_mod' => now(),
            ]);

            foreach ($entry->entryDetails as $detail) {
                $detail->update([
                    'estado_id' => 29, // ANULADO
                ]);
            }

            return response()->json(['message' => 'Ingreso y detalles anulados correctamente.']);
        });
    }


    private function generateNumero($entidad_id, $tipo_documento_id)
    {
        $maxNumero = Entry::where('entidad_id', $entidad_id)
            ->where('tipo_documento_id', $tipo_documento_id)
            ->max('numero');

        return $maxNumero ? $maxNumero + 1 : 1;
    }
    public function entryDetailsConStock()
    {
        $entryDetails = \App\Models\EntryDetail::with([
            'entry.documentType',
            'entry',
            'medicine',
            'estate',
            'user'
        ])
        ->where('stock_actual', '>', 0)
        ->get();

        return response()->json($entryDetails->map(function ($detail) {
            return [
                'id' => $detail->id,
                'medicamento_id' => $detail->medicamento_id,
                'liname' => $detail->medicine->liname ?? null,
                'medicamento' => $detail->medicine->nombre_generico ?? null,
                'lote' => $detail->lote,
                'fecha_vencimiento' => $detail->fecha_vencimiento,
                'cantidad' => $detail->cantidad,
                'costo_unitario' => $detail->costo_unitario,
                'costo_total' => $detail->costo_total,
                'stock_actual' => $detail->stock_actual,
                'ingreso_id' => $detail->entry->id ?? null,
                'fecha_ingreso' => $detail->entry->fecha_ingreso ?? null,
                'observaciones' => $detail->observaciones,
                'estado_id' => $detail->estado_id,
                'estado' => $detail->estate->descripcion ?? null,
                'usr' => $detail->usr,
                'usuario' => $detail->user->name ?? null,
            ];
        }));
    }


    public function activate(Entry $entry)
{
    // Verificamos que tanto el ingreso como todos sus items estén en estado pendiente (27)
    if ($entry->estado_id !== 27 || $entry->entryDetails->contains(fn ($detail) => $detail->estado_id !== 27)) {
        return response()->json([
            'message' => 'Solo se puede activar si el ingreso y todos sus detalles están en estado pendiente.'
        ], 403);
    }

    // Cambiar estado del Entry
    $entry->estado_id = 28; // ACTIVO
    $entry->usr_mod = auth()->id();
    $entry->fhr_mod = now();
    $entry->save();

    // Cambiar estado de todos los items del ingreso
    foreach ($entry->entryDetails as $detail) {
        $detail->estado_id = 28; // ACTIVO
        $detail->usr = auth()->id();
        $detail->save();
    }

    return new EntryResource($entry->fresh('entryDetails'));
}
}
