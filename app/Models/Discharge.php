<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discharge extends Model
{
    use HasFactory;

    protected $fillable=['fecha_egreso','entidad_id','tipo_documento_id','receta_id','numero',
    'proveedor_id','observaciones','usr', 'estado_id'];
    //, 'usr_mod','fhr_mod'

      // Relación con el modelo TipoDocumento
      public function documentType()
      {
          return $this->belongsTo(Document_Type::class, 'tipo_documento_id');
      }

      // Relación con el modelo DischargeDetail
      public function Details()
      {
          return $this->hasMany(DischargeDetail::class, 'egreso_id');
      }
      public function entity()
      {
          return $this->belongsTo(Entity::class, 'entidad_id');
      }


}
