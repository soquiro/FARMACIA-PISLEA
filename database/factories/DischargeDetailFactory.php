<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DischargeDetail>
 */
class DischargeDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'egreso_id'=>mt_rand(1,200),
            'ingreso_detalle_id'=>mt_rand(1,700),
            'cantidad_solicitada'=> mt_rand(1,120),
            'cantidad_entregada'=> mt_rand(1,120),
            'costo_unitario'=>$this->faker->randomFloat(4),
            'costo_total'=>$this->faker->randomFloat(2),
            'observaciones'=>$this->faker->text(100),
            'usr'=>1,
            'estado_id'=>rand(27,28),

        ];
    }
}
