<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicineEntity>
 */
class MedicineEntityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stockmax'=>rand(1,200),
            'stockmin'=>rand(10,30),
            'darmax'=>rand(1,90),
            'darmin'=>rand(1,5),

            'medicamento_id'=>rand(1,700),
            'entidad_id'=>rand(1,2),
            'usr'=>rand(1,5),
            'estado_id'=>rand(1,3),
        ];
    }
}
