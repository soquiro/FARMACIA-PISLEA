<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discharge extends Model
{
    use HasFactory;

    protected $fillable=[
        'fecha_egreso',
        'entidad_id',
        'tipo_documento_id',
        'numero',
        'receta_id',
        'servicio_id',
        'proveedor_id',
        'observaciones',
        'usr',
        'estado_id',
        'usr_mod',
        'fhr_mod',
    ];
    protected $casts = [
        'fecha_egreso' => 'datetime',
        'fhr_mod' => 'datetime',
    ];
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entidad_id');
    }
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'tipo_documento_id','id')
        ->where('categoria_id', 2);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'proveedor_id');
    }
    public function service()
    {
        return $this->belongsTo(DocumentType::class, 'tipo_documento_id','id')
        ->where('categoria_id', 8);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'usr');
    }
    public function estate()
    {
        return $this->belongsTo(DocumentType::class, 'estado_id','id')
        ->where('categoria_id', 5);
    }
    public function dischargeDetails()
    {
        return $this->hasMany(DischargeDetail::class, 'egreso_id');
    }

}
