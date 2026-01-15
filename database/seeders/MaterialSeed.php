<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaterialSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Material::create(['name' => 'Cotton', 'description' => 'Soft and breathable fabric']);
        Material::create(['name' => 'Polyester', 'description' => 'Durable and wrinkle-resistant fabric']);
        Material::create(['name' => 'Wool', 'description' => 'Warm and insulating fabric']);
        Material::create(['name' => 'Silk', 'description' => 'Luxurious and smooth fabric']);
        Material::create(['name' => 'Denim', 'description' => 'Sturdy cotton twill fabric']);
    }
}
