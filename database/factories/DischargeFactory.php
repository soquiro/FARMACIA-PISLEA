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
            'tipo_documento_id'=>rand(7,11),
            'numero'=>$this->faker->numberBetween(1,200),
            'receta_id'=>rand(1,500),
           // 'servicio_id'=>rand(1,15),
           // 'proveedor_id'=>rand(0,30),
            'observaciones'=>$this->faker->text(100),
            'usr'=>1,
            'estado_id'=>28,
            'usr_mod'=>1,
            'fhr_mod'=>$this->faker->dateTimeBetween('-1 week', 'now'),

        ];
    }
}
