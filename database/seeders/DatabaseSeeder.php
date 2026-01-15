<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Ilija Uzelac',
            'email' => 'deluziki@outlook.com',
            'role' => 'admin',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->call([
            ColorSeed::class,
            CategorySeed::class,
            DesignerSeed::class,
            SupplierSeed::class,
            MaterialSeed::class,
        ]);

    }
}
