<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "nombre"=>$this->faker->company,
            "nit"=>mt_rand(100000000000,1000000000000),
            "direccion"=>$this->faker->address(),
            "telefono"=>$this->faker->phoneNumber(),
            "persona_contacto"=>$this->faker->name(),
            "celular"=>$this->faker->phoneNumber(),
            "email"=> $this->faker->unique()->safeEmail(),
            "observaciones"=>$this->faker->text(100),

            "usr"=>rand(1,5),
            "estado_id"=>rand(1,3)
        ];
    }
}
