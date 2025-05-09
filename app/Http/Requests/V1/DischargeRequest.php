<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class DischargeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_egreso' => 'required|date',
            'entidad_id' => 'required|exists:entities,id',
            'tipo_documento_id' => 'required|exists:document_types,id',
            'receta_id' => 'required|integer',
            'servicio_id' => 'nullable|integer',
            'proveedor_id' => 'nullable|integer',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required|exists:document_types,id',

            'discharge_details' => 'required|array|min:1',
            'discharge_details.*.ingreso_detalle_id' => 'required|exists:entry_details,id',
            'discharge_details.*.cantidad_solicitada' => 'required|integer|min:1',
            'discharge_details.*.cantidad_entregada'=> 'required|integer|min:1',
            'discharge_details.*.costo_unitario' => 'required|numeric|min:0',
            'discharge_details.*.costo_total' => 'required|numeric|min:0',
            'discharge_details.*.observaciones' => 'nullable|string',
            'discharge_details.*.estado_id' => 'required|exists:document_types,id',
        ];
    }

    public function messages()
    {
        return [
            'discharge_details.required' => 'Debes ingresar al menos un item del egreso.',
            'discharge_details.*.cantidad_entregada.min' => 'La cantidad entregada debe ser al menos 1.',
            'discharge_details.*.ingreso_detalle_id.exists' => 'El ingreso asociado no existe.',
        ];
    }
}
