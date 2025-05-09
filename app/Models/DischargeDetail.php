<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DischargeDetail extends Model
{
    use HasFactory;
    protected $fillable=[
    'egreso_id',
    'ingreso_detalle_id',
    'cantidad_solicitada',
    'costo_unitario',
    'cantidad_entregada',
    'costo_total',
    'observaciones',
    'usr',
    'estado_id'
    ];
    //, 'usr_mod','fhr_mod'

    public function discharges()
    {
        return $this->belongsTo(Discharge::class, 'egreso_id');
    }
    public function entryDetails()
    {
        return $this->belongsTo(EntryDetail::class, 'ingreso_detalle_id');
    }
    public function estate()
    {
        return $this->belongsTo(DocumentType::class, 'estado_id','id')
        ->where('categoria_id', 5);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'usr');
    }

}
