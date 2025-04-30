<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
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
            'entidad_id' => $this->entidad_id,
            'entidad' => $this->entity->descripcion ?? null,
            'tipo_documento_id' => $this->tipo_documento_id,
            'tipo_documento' => $this->documentType->descripcion ?? null,
            'numero' => $this->numero,
            'fecha_ingreso' => $this->fecha_ingreso,
            'proveedor_id' => $this->proveedor_id,
            'proveedor' => $this->supplier->nombre ?? null,
            'num_factura' => $this->num_factura,
            'observaciones' => $this->observaciones,
            'usr' => $this->user->id ?? null,
            'usuario' => $this->user->name ?? null,
            'estado_id' => $this->estado_id,
            'estado' => $this->estate->descripcion ?? null,
            'details' => EntryDetailResource::collection($this->entryDetails),

        ];
    }
}
