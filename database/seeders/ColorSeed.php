<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Color;

class ColorSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Color::create(['name' => 'Red', 'hex_code' => '#FF0000']);
        Color::create(['name' => 'Green', 'hex_code' => '#00FF00']);
        Color::create(['name' => 'Blue', 'hex_code' => '#0000FF']);
        Color::create(['name' => 'Yellow', 'hex_code' => '#FFFF00']);
        Color::create(['name' => 'Black', 'hex_code' => '#000000']);
    }
}
