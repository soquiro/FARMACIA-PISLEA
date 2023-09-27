<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Discharge>
 */
class DischargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'fecha_egreso'=>$this->faker->dateTimeBetween('-1 week', 'now'),
            'entidad_id'=>rand(1,2),
            'tipo_documento_id'=>rand(1,6),
            'numero'=>$this->faker->numberBetween(1,200),
            'receta_id'=>rand(1,500),
           // 'servicio_id'=>rand(1,15),
           // 'proveedor_id'=>rand(0,30),
            'observaciones'=>$this->faker->text(100),
            'usr'=>rand(1,5),
            'estado_id'=>rand(1,3),
            'usr_mod'=>rand(1,5),
            'fhr_mod'=>$this->faker->dateTimeBetween('-1 week', 'now'),

        ];
    }
}
