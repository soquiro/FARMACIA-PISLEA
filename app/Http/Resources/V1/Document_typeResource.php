<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Document_typeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            'id'=>$this->id,
            'categoria_id'=>$this->categoria_id,
            'descripcion'=>$this->descripcion,
            'cod_servicio'=>$this->cod_servicio,
            'usr'=>$this->usr,
            'estado_id'=>$this->estado_id,

        ];
    }
}
