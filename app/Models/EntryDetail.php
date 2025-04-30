<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntryDetail extends Model
{
    use HasFactory;
    protected $table='entry_details';
    protected $fillable=[
        'ingreso_id',
        'medicamento_id',
        'lote',
        'fecha_vencimiento',
        'cantidad',
        'costo_unitario',
        'costo_total',
        'stock_actual',
        'observaciones',
        'estado_id',
        'usr',
        'item_id',
        'egresodetalle_id',
    ];

    public function entry()
    {
        return $this->belongsTo(Entry::class,'ingreso_id');
    }


    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicamento_id');
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
    public function dischargeDetails()
    {
        return $this->hasMany(DischargeDetail::class, 'ingreso_detalle_id');
    }
}



