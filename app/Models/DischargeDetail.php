<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DischargeDetail extends Model
{
    use HasFactory;
    protected $fillable=['egreso_id','ingreso_detalle_id','cantidad_solicitada', 'costo_unitario'
    , 'cantidad_entregada',
    'costo_total',
    'observaciones',
    'usr',
    'estado_id'
    ];
    //, 'usr_mod','fhr_mod'

    public function entryDetail()
    {
        return $this->belongsTo(EntryDetail::class, 'ingreso_detalle_id');
    }
    /*  public function entryDetails()
        {
            return $this->hasMany(EntryDetail::class, 'ingreso_id');
    }*/


}
