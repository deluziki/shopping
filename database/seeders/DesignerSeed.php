<?php

namespace Database\Seeders;

use App\Models\Designer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesignerSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Designer::create(['name' => 'Designer A', 'bio' => 'Bio of Designer A']);
        Designer::create(['name' => 'Designer B', 'bio' => 'Bio of Designer B']);
        Designer::create(['name' => 'Designer C', 'bio' => 'Bio of Designer C']);
    }
}
