<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EntryDetail>
 */
class EntryDetailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'ingreso_id'=>mt_rand(1,200),
            'med_entidad_id'=>mt_rand(1,700),

            'lote'=>$this->faker->randomNumber(6, true),
            'fecha_vencimiento'=>$this->faker->dateTimeBetween('-1 week', 'now'),
            'cantidad'=> mt_rand(100,5000),

            'costo_unitario'=>$this->faker->randomFloat(4),
            'costo_total'=>$this->faker->randomFloat(2),
            'observaciones'=>$this->faker->text(100),
            'estado_id'=>rand(1,3),
            'usuario'=>rand(1,3),
            'item_id'=>rand(1,200),
          //  'egresodetalle__id'=>$this->faker->random(), /*codigo del egreso reingresado*/

        ];
    }
}
