<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;
    protected $table='entries';
    protected $fillable=['entidad_id','tipo_documento_id','numero',
    'fecha_ingreso','proveedor_id','num_factura','observaciones',
    'usr', 'estado_id', 'usr_mod','fhr_mod',


    ];
    public function documentType()
    {
        return $this->belongsTo(Document_Type::class, 'tipo_documento_id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entidad_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'proveedor_id');
    }

    public function entryDetails()
    {
        return $this->hasMany(EntryDetail::class, 'ingreso_id');
    }
    public function details()
    {
        return $this->hasMany(EntryDetail::class, 'ingreso_id');
    }




}
