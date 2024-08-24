<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\EntryDetail;


class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {// Primera consulta para obtener datos de la tabla entries con las relaciones

          // Obtener todas las entradas junto con sus detalles
          $entries = Entry::with(['documentType', 'entity', 'supplier', 'entryDetails.medicine'])
          ->get();

      // Formatear los datos para que coincidan con las consultas SQL proporcionadas
      $result = $entries->map(function($entry) {
          return [
              'id' => $entry->id,
              'entidad_id' => $entry->entidad_id,
              'entidad' => $entry->entity->descripcion,
              'tipo_documento_id' => $entry->tipo_documento_id,
              'tipo' => $entry->documentType->descripcion,
              'numero' => $entry->numero,
              'fecha_ingreso' => $entry->fecha_ingreso,
              'proveedor_id' => $entry->proveedor_id,
              'proveedor' => $entry->supplier->nombre,
              'num_factura' => $entry->num_factura,
              'obsCompra' => $entry->observaciones,
              'usr' => $entry->usr,
              'estado_id' => $entry->estado_id,
              'entry_details' => $entry->entryDetails->map(function($detail) {
                  return [
                      'id' => $detail->id,
                      'ingreso_id' => $detail->ingreso_id,
                      'med_entidad_id' => $detail->med_entidad_id,
                      'liname' => $detail->medicine->liname,
                      'nombre_generico' => $detail->medicine->nombre_generico,
                      'lote' => $detail->lote,
                      'fecha_vencimiento' => $detail->fecha_vencimiento,
                      'cantidad' => $detail->cantidad,
                      'costo_unitario' => $detail->costo_unitario,
                      'costo_total' => $detail->costo_total,
                      'obsItem' => $detail->observaciones,
                      'estado_id' => $detail->estado_id,
                      'usuario' => $detail->usuario,
                  ];
              })
          ];
      });

      //return response()->json($result);



          // Devolver los datos como JSON
      //   return response()->json( $entries);

      if($entries->count()>0){
        return response()->json([
            'status' => 200,
            'entries'=>$result
        ],200);

       }
       else{
        return response()->json([
            'status' => 404,
            'entries'=>'No Records Found'
        ],404);
       }


       /*   // Obtener todas las entradas junto con sus detalles
        $entries=Entry::with('details')->get();

         // Devolver los datos como JSON
      //   return response()->json( $entries);

        if($entries->count()>0){
         return response()->json([
             'status' => 200,
             'entries'=>$entries
         ],200);

        }
        else{
         return response()->json([
             'status' => 404,
             'entries'=>'No Records Found'
         ],404);
        }*/
     }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'entidad_id' => 'required|integer',
            'tipo_documento_id' => 'required|integer',
            'numero' => 'required|integer',
            'fecha_ingreso' => 'required|date',
            'proveedor_id' => 'required|integer',
            'num_factura' => 'required|integer',
            'observaciones' => 'nullable|string',
            'usr' => 'required|integer',
            'estado_id' => 'required|integer',
            'usr_mod' => 'required|integer',
            'fhr_mod' => 'nullable|date',

            'details.*.med_entidad_id' => 'required|integer',
            'details.*.lote' => 'required|string|max:255',
            'details.*.fecha_vencimiento' => 'required|date',
            'details.*.cantidad' => 'required|integer',
            'details.*.costo_unitario' => 'required|numeric',
            'details.*.costo_total' => 'required|numeric',
            'details.*.observaciones' => 'nullable|string',
            'details.*.estado_id' => 'required|integer',
            'details.*.usuario' => 'required|integer',
            'details.*.item_id' => 'required|integer',
           // 'details.*.egresodetalle_id' => 'nullable|integer',

        ]);

        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        }
            // Iniciar una transacción
        DB::beginTransaction();

         try {
                // Crear el nuevo registro en la tabla entries
                $entry = Entry::create([
                    'entidad_id' => $request['entidad_id'],
                    'tipo_documento_id' => $request['tipo_documento_id'],
                    'numero' => $request['numero'],
                    'fecha_ingreso' => $request['fecha_ingreso'],
                    'proveedor_id' => $request['proveedor_id'],
                    'num_factura' => $request['num_factura'],
                    'observaciones' => $request['observaciones'],
                    'usr' => $request['usr'],
                    'estado_id' => $request['estado_id'],
                    'usr_mod' => $request['usr_mod'],
                    'fhr_mod' => $request['fhr_mod'],
                ]);

                // Insertar los detalles en la tabla entry_details
                foreach ($request['details'] as $detail) {
                    EntryDetail::create([
                        'ingreso_id' => $entry->id,
                        'med_entidad_id' => $detail['med_entidad_id'],
                        'lote' => $detail['lote'],
                        'fecha_vencimiento' => $detail['fecha_vencimiento'],
                        'cantidad' => $detail['cantidad'],
                        'costo_unitario' => $detail['costo_unitario'],
                        'costo_total' => $detail['costo_total'],
                        'observaciones' => $detail['observaciones'],
                        'estado_id' => $detail['estado_id'],
                        'usuario' => $detail['usuario'],
                        'item_id' => $detail['item_id'],
                       // 'egresodetalle_id' => $detail['egresodetalle_id'],
                    ]);
                }

                // Confirmar la transacción
                DB::commit();

                // Devolver una respuesta exitosa
                return response()->json(['message' => 'Entry and details creado exitosamente', 'entry' => $entry], 201);

            } catch (\Exception $e) {
                // Revertir la transacción si ocurre un error
                DB::rollBack();

                // Devolver un mensaje de error
                return response()->json(['error' => 'Error Algo salio mal', 'message' => $e->getMessage()], 500);
            }
    }

                /**
                 * Display the specified resource.
                 */
    public function show($id)
    {
                                  // Buscar la entrada por su ID, cargando también los detalles relacionados
            $entry = Entry::with('details')->find($id);

            // Verificar si la entrada existe
            if (!$entry) {
                return response()->json([
                    'error' => 'INgreso no encontrado',
                    'message' => 'No se encontró ingreso con el ID especificado'
                ], 404);
            }

            // Devolver la entrada con los detalles en una respuesta JSON
            return response()->json([
                'entry' => $entry
            ], 200);
    }


    public function edit($id)
    {


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {   // Validar los datos
        $validator=Validator::make($request->all(),[
            'entidad_id' => 'required|integer',
            'tipo_documento_id' => 'required|integer',
            'numero' => 'required|integer',
            'fecha_ingreso' => 'required|date',
            'proveedor_id' => 'required|integer',
            'num_factura' => 'required|integer',
            'observaciones' => 'nullable|string',
            'usr' => 'required|integer',
            'estado_id' => 'required|integer',
            'usr_mod' => 'required|integer',
            'fhr_mod' => 'nullable|date',

            'details.*.med_entidad_id' => 'required|integer',
            'details.*.lote' => 'required|string|max:255',
            'details.*.fecha_vencimiento' => 'required|date',
            'details.*.cantidad' => 'required|integer',
            'details.*.costo_unitario' => 'required|numeric',
            'details.*.costo_total' => 'required|numeric',
            'details.*.observaciones' => 'nullable|string',
            'details.*.estado_id' => 'required|integer',
            'details.*.usuario' => 'required|integer',
            'details.*.item_id' => 'required|integer',
           // 'details.*.egresodetalle_id' => 'nullable|integer',

        ]);
        // Manejar la validación fallida
        if($validator->fails()){
            return response()->json([
                'status'=>422,
                'errors'=>$validator->messages()
            ],422);
        }
         // Buscar la entrada existente
         $entry = Entry::findOrFail($id);

         // Iniciar una transacción
         DB::beginTransaction();

         try {
             // Actualizar el registro en la tabla entries
             $entry->update([
                 'entidad_id' => $request->entidad_id,
                 'tipo_documento_id' => $request->tipo_documento_id,
                 'numero' => $request->numero,
                 'fecha_ingreso' => $request->fecha_ingreso,
                 'proveedor_id' => $request->proveedor_id,
                 'num_factura' => $request->num_factura,
                 'observaciones' => $request->observaciones,
                 'usr' => $request->usr,
                 'estado_id' => $request->estado_id,
                 'usr_mod' => $request->usr_mod,
                 'fhr_mod' => $request->fhr_mod,
             ]);

             // Procesar cada detalle
             foreach ($request->details as $detail) {
                 if (isset($detail['id'])) {
                     // Actualizar detalle existente
                     $entryDetail = EntryDetail::findOrFail($detail['id']);
                     $entryDetail->update([
                         'med_entidad_id' => $detail['med_entidad_id'],
                         'lote' => $detail['lote'],
                         'fecha_vencimiento' => $detail['fecha_vencimiento'],
                         'cantidad' => $detail['cantidad'],
                         'costo_unitario' => $detail['costo_unitario'],
                         'costo_total' => $detail['costo_total'],
                         'observaciones' => $detail['observaciones'],
                         'estado_id' => $detail['estado_id'],
                         'usuario' => $detail['usuario'],
                         'item_id' => $detail['item_id'],
                        // 'egresodetalle_id' => $detail['egresodetalle_id'],
                     ]);
                 } else {
                     // Crear nuevo detalle
                     EntryDetail::create([
                         'ingreso_id' => $entry->id,
                         'med_entidad_id' => $detail['med_entidad_id'],
                         'lote' => $detail['lote'],
                         'fecha_vencimiento' => $detail['fecha_vencimiento'],
                         'cantidad' => $detail['cantidad'],
                         'costo_unitario' => $detail['costo_unitario'],
                         'costo_total' => $detail['costo_total'],
                         'observaciones' => $detail['observaciones'],
                         'estado_id' => $detail['estado_id'],
                         'usuario' => $detail['usuario'],
                         'item_id' => $detail['item_id'],
                         //'egresodetalle_id' => $detail['egresodetalle_id'],
                     ]);
                 }
             }

             // Confirmar la transacción
             DB::commit();

             // Devolver una respuesta exitosa con los datos combinados
             return response()->json([
                 'message' => 'Entry and details actualizado exitosamente',
                 'entry' => $entry->load('details')
             ], 200);

         } catch (\Exception $e) {
             // Revertir la transacción si ocurre un error
             DB::rollBack();

             // Devolver un mensaje de error
             return response()->json([
                 'error' => 'Error Algo salio mal',
                 'message' => $e->getMessage()
             ], 500);
         }





    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
         // Buscar la entrada por su ID
         $entry = Entry::find($id);

         // Verificar si la entrada existe
         if (!$entry) {
             return response()->json([
                 'error' => 'Entrada no encontrada',
                 'message' => 'No se encontró una entrada con el ID especificado'
             ], 404);
         }

         // Verificar si alguno de los detalles tiene registros en discharge_details
         $hasDischargeDetails = EntryDetail::where('ingreso_id', $id)
             ->whereHas('dischargeDetails')
             ->exists();

         if ($hasDischargeDetails) {
             return response()->json([
                 'error' => 'No se puede eliminar',
                 'message' => 'La entrada no puede ser eliminada porque uno o más de sus detalles están asociados a registros en discharge_details.'
             ], 409); // Código 409: Conflicto
         }

         // Iniciar una transacción
         DB::beginTransaction();

         try {
              // Cambiar el estado de los detalles asociados a 28='ANULADO'
            $entry->details()->update(['estado_id' => 28]);

            // Cambiar el estado de la entrada principal a 28='ANULADO'
            $entry->update(['estado_id' => 28]);


             // Confirmar la transacción
             DB::commit();

             // Devolver una respuesta exitosa
             return response()->json([
                 'message' => 'el Ingreso y sus items fueron Anulados exitosamente'
             ], 200);

         } catch (\Exception $e) {
             // Revertir la transacción si ocurre un error
             DB::rollBack();

             // Devolver un mensaje de error
             return response()->json([
                 'error' => 'Error al eliminar la entrada',
                 'message' => $e->getMessage()
             ], 500);
         }
          // Buscar la entrada por su ID
        /*  $entry = Entry::find($id);

          // Verificar si la entrada existe
          if (!$entry) {
              return response()->json([
                  'error' => 'Ingreso no encontrado',
                  'message' => 'No se encontró un ingreso con el ID especificado'
              ], 404);
          }

          // Iniciar una transacción
          DB::beginTransaction();

          try {
              // Eliminar los detalles asociados
              $entry->details()->delete();

              // Eliminar la entrada principal
              $entry->delete();

              // Confirmar la transacción
              DB::commit();

              // Devolver una respuesta exitosa
              return response()->json([
                  'message' => 'Entrada y detalles eliminados exitosamente'
              ], 200);

          } catch (\Exception $e) {
              // Revertir la transacción si ocurre un error
              DB::rollBack();

              // Devolver un mensaje de error
              return response()->json([
                  'error' => 'Error al eliminar el ingreso',
                  'message' => $e->getMessage()
              ], 500);
          }*/
        }
}

