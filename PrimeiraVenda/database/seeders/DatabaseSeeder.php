<?php

namespace Database\Seeders;

use App\Models\Produto;
use App\Models\Estoque;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Produto::factory()
            ->count(15)
            ->has(Estoque::factory()) 
            ->create();

        Produto::factory()
            ->count(5)
            ->create();
    }   
}
