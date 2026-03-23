<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('products')->insert([
            ['nome' => 'Notebook', 'valor' => '3500.00', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Mouse', 'valor' => '150.00', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Teclado', 'valor' => '200.00', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Monitor', 'valor' => '1200.00', 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Headset', 'valor' => '350.00', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
