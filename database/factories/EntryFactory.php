<?php

namespace Database\Factories;
use App\Models\Entry;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entry>
 */
class EntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $entidad_id = rand(1, 2);
        $tipo_documento_id = rand(1, 6);

        // Obtener el último número utilizado para la combinación de entidad_id y tipo_documento_id
        $lastNumber = Entry::where('entidad_id', $entidad_id)
                            ->where('tipo_documento_id', $tipo_documento_id)
                            ->max('numero');

        // Incrementar el número secuencialmente
        $numero = $lastNumber ? $lastNumber + 1 : 1;

        return [
            'entidad_id' => $entidad_id,
            'tipo_documento_id' => $tipo_documento_id,
            'numero' => $numero,
            'fecha_ingreso' => $this->faker->dateTimeBetween('-1 week', 'now'),
            'num_factura' => rand(1, 15),
            'observaciones' => $this->faker->text(100),
            'proveedor_id' => rand(1, 15),
            'usr' => rand(1, 5),
            'estado_id' => rand(1, 3),
            'usr_mod' => rand(1, 5),
            'fhr_mod' => $this->faker->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
