<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => $this->faker->unique()->words(3, true),
            'descricao' => $this->faker->sentence(),
            'categoria' => $this->faker->randomElement(['Eletrônicos', 'Roupas', 'Casa', 'Acessórios']),
            'preco' => $this->faker->randomFloat(2, 10, 1000),
            'url_imagem' => 'https://via.placeholder.com/150',
            'ativo' => true,
        ];
    }
}