<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DischargeDetailResource extends JsonResource
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
            'ingreso_detalle_id' => $this->ingreso_detalle_id,
            'liname' => $this->entryDetails->medicine->liname ?? null,
            'medicamento' => $this->entryDetails->medicine->nombre_generico ?? null,
            'lote' => $this->entryDetails->lote ?? null,
            'cantidad_solicitada' => $this->cantidad_solicitada,
            'cantidad_entregada' => $this->cantidad_entregada,
            'costo_unitario' => $this->costo_unitario,
            'costo_total' => $this->costo_total,
            'observaciones' => $this->observaciones,
            'estado_id'=> $this->estate->id ?? null,
            'estado' => $this->estate->descripcion ?? null,
            'usr' => $this->user->id ?? null,
            'usuario' => $this->user->name ?? null,
        ];
    }
}
