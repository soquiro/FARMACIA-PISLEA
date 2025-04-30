<?php

namespace App\Http\Requests\V1;

use Illuminate\Foundation\Http\FormRequest;

class EntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entidad_id' => 'required|exists:entities,id',
            'tipo_documento_id' => 'required|exists:document_types,id',
            'fecha_ingreso' => 'required|date',
            'proveedor_id' => 'required|exists:suppliers,id',
            'num_factura' => 'nullable|integer',
            'observaciones' => 'nullable|string',
            'estado_id' => 'required|exists:document_types,id',

            'entry_details' => 'required|array|min:1',
            'entry_details.*.medicamento_id' => 'required|exists:medicines,id',
            'entry_details.*.lote' => 'required|string',
            'entry_details.*.fecha_vencimiento' => 'required|date',
            'entry_details.*.cantidad' => 'required|numeric|min:1',
            'entry_details.*.costo_unitario' => 'required|numeric|min:0',
            'entry_details.*.costo_total' => 'required|numeric|min:0',
            'entry_details.*.stock_actual' => 'required|numeric|min:0',
            'entry_details.*.observaciones' => 'nullable|string',
            'entry_details.*.estado_id' => 'required|exists:document_types,id',
        ];
    }

    public function messages()
    {
        return [
            'entry_details.required' => 'Debes ingresar al menos un item del ingreso.',
            'entry_details.*.medicamento_id.required' => 'El medicamento es obligatorio.',
            'entry_details.*.cantidad.required' => 'La cantidad es obligatoria.',
        ];
    }
}
