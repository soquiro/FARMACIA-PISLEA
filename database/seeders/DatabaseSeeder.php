<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\PharmaceuticalForm;
use App\Models\Category;
use App\Models\Document_type;
use App\Models\Medicine;
use App\Models\MedicineEntity;
use App\Models\MedicinePackage;
use App\Models\Entry;
use App\Models\EntryDetail;
use App\Models\Discharge;
use App\Models\DischargeDetail;
use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Illuminate\Support\Facades\Schema;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       \App\Models\User::factory()->count(10)->create();
       \App\Models\Supplier::factory()->count(30)->create();


      Schema::disableForeignKeyConstraints();

       $classes =[PharmaceuticalFormSeeder::class,
                CategorySeeder::class,
                EntitySeeder::class,
                DocumentTypeSeeder::class,
                MedicineSeeder::class,
                ServiceClassificationSeeder::class,


       ];

           $this->call($classes);


           \App\Models\MedicineEntity::factory()->count(700)->create();
           \App\Models\MedicinePackage::factory()->count(139)->create();
           \App\Models\Entry::factory()->count(200)->create();
           \App\Models\EntryDetail::factory()->count(200)->create();
           \App\Models\Discharge::factory()->count(200)->create();
           \App\Models\DischargeDetail::factory()->count(200)->create();


    }
}
