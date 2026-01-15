<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(['name' => 'Clothing', 'description' => 'Apparel and garments']);
        Category::create(['name' => 'Footwear', 'description' => 'Shoes and sandals']);
        Category::create(['name' => 'Accessories', 'description' => 'Fashion accessories']);
        Category::create(['name' => 'Home Decor', 'description' => 'Decorative items for home']);
    }
}
