<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       \App\Models\User::factory()->count(10)->create();
       \App\Models\Supplier::factory()->count(30)->create();
       \App\Models\Entity::factory()->count(2)->create();
    }
}
