<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineEntity extends Model
{
    use HasFactory;

    protected $table='medicine_entities';
    protected $primaryKey='id';
    public $timestamp=false;

    protected $fillable=[
        'medicamento_id',
        'entidad_id',
        'stockmax',
        'stockmin',
        'darmax',
        'darmin',
        'usr',
        'estado_id',
    ];

     /**
     * Relación con el modelo Medicine.
     * Una entidad de medicina pertenece a un medicamento.
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicamento_id');
    }
    /**
     * Relación con el modelo Entity.
     * Una entidad de medicina pertenece a una entidad.
     */
     /**
     * Relación con el modelo PharmaceuticalForm.
     * Una entidad de medicina pertenece a una forma farmacéutica.
     */
    public function pharmaceuticalForm()
    {
        return $this->belongsTo(PharmaceuticalForm::class, 'formafarmaceutica_id', 'id', 'medicines');
    }

    /**
     * Relación con el modelo DocumentType.
     * Una entidad de medicina pertenece a un tipo de documento.
     */
    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'categoriamed_id', 'id', 'medicines');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entidad_id');
    }

}
