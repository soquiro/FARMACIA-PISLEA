<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\V1\EntryRequest;
use App\Http\Resources\V1\EntryResource;
use App\Models\Entry;
use App\Models\EntryDetail;
use App\Models\DischargeDetail;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    public function index()
    {
        $entries = Entry::with('entryDetails', 'entity', 'documentType', 'supplier', 'estate')->get();
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
            'usr' => auth()->id(), // o usa el campo que necesitas
            'estado_id' => $validated['estado_id'],
        ]);

        // Crear detalles
        foreach ($validated['entry_details'] as $detail) {
            $entry->entryDetails()->create(array_merge($detail, [
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

    public function show($id)
    {
        $entry = Entry::with('entryDetails', 'entity', 'documentType', 'supplier', 'estate')->findOrFail($id);
        return new EntryResource($entry);
    }

    public function update(EntryRequest $request, $id)
    {
        $entry = Entry::findOrFail($id);

        DB::beginTransaction();
        try {
            $entry->update($request->only([
                'entidad_id', 'tipo_documento_id', 'fecha_ingreso',
                'proveedor_id', 'num_factura', 'observaciones',
                'usr', 'estado_id'
            ]));

            foreach ($request->entry_details as $detail) {
                if (isset($detail['id'])) {
                    // Actualizar detalle existente
                    $entryDetail = EntryDetail::findOrFail($detail['id']);

                    // Validar si tiene movimientos de egreso
                    $used = DischargeDetail::where('ingreso_detalle_id', $entryDetail->id)->exists();
                    if ($used) {
                        continue; // No actualizar si ya está usado
                    }

                    $entryDetail->update($detail);
                } else {
                    // Crear nuevo detalle
                    $entry->entryDetails()->create($detail);
                }
            }

            DB::commit();
            return new EntryResource($entry->fresh('entryDetails'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $entry = Entry::findOrFail($id);

        DB::beginTransaction();
        try {
            // Anular el Entry
            $entry->update(['estado_id' => 2]); // 2 = Anulado (ajustar según tu catálogo)

            // Anular cada detalle
            foreach ($entry->entryDetails as $detail) {
                $detail->update(['estado_id' => 2]);
            }

            DB::commit();
            return response()->json(['message' => 'Ingreso anulado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al anular', 'error' => $e->getMessage()], 500);
        }
    }

    private function generateNumero($entidad_id, $tipo_documento_id)
    {
        $maxNumero = Entry::where('entidad_id', $entidad_id)
            ->where('tipo_documento_id', $tipo_documento_id)
            ->max('numero');

        return $maxNumero ? $maxNumero + 1 : 1;
    }
}
