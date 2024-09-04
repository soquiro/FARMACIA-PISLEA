<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $table='medicines';
    protected $primaryKey='id';
    public $timestamp=false;

  //  protected $fillable = ['liname', 'nombre_generico', 'forma_farmaceutica'];

    public function pharmaceutical_forms()
    {
        return $this->belongsTo(pharmaceutical_forms::class, 'id', 'formafarmaceutica_id');
    }
    public function document_types()
    {
        return $this->belongsTo(document_types::class, 'categoria_id', 'categoriamed_id');
    }

}
