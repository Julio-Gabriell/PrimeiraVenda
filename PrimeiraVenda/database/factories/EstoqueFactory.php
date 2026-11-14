<?php

namespace Database\Factories;

use App\Enums\Origem;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class EstoqueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'produto_id' => Produto::factory(),
            'quantidade' => $this->faker->numberBetween(0, 50),
            'ultima_origem' => Origem::cadastro_whatsapp,
        ];
    }
}