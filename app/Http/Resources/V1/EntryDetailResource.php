<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'medicamento_id' => $this->medicamento_id,
            'liname' => $this->medicine->liname ?? null,
            'medicamento' => $this->medicine->nombre_generico ?? null,
            'lote' => $this->lote,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'cantidad' => $this->cantidad,
            'costo_unitario' => $this->costo_unitario,
            'costo_total' => $this->costo_total,
            'stock_actual' => $this->stock_actual,
            'observaciones' => $this->observaciones,
            'estado_id'=> $this->estate->id ?? null,
            'estado' => $this->estate->descripcion ?? null,
            'usr' => $this->user->id ?? null,
            'usuario' => $this->user->name ?? null,
        ];
    }
}
