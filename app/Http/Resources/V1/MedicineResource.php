<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       // return parent::toArray($request);
       return[
        'id'=>$this->id,
        'liname'=>$this->liname,
        'categoriamed_id'=>$this->categoriamed_id,
        'nombre generico'=>$this->nombre_generico,
        'formafarmaceutica_id'=>$this->formafarmaceutica_id,
        'observaciones'=>$this->observaciones,
        'estado_id'=>$this->estado_id,
        'usr'=>$this->usr,


       ];
    }
}
