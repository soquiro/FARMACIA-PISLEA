<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryDetail extends Model
{
    use HasFactory;
    protected $table='entry_details';
    protected $fillable=['ingreso_id',
    'med_entidad_id',
    'lote',
    'fecha_vencimiento',
    'cantidad',
    'costo_unitario',
    'costo_total',
    'observaciones',
    'estado_id',
    'usuario',
    'item_id',
    'egresodetalle_id',
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class,'ingreso_id');
    }
    public function dischargeDetails()
    {
        return $this->hasMany(DischargeDetail::class, 'ingreso_detalle_id');
    }
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'med_entidad_id');
    }

}
