<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document_type extends Model
{
    use HasFactory;
    protected $fillable = [
        'categoria_id',
        'descripcion',
        'cod_servicio',
        'usr',
        'estado_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }
}
