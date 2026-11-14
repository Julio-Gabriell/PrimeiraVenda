<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produto', function(Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->string('descricao', 255);
            $table->string('categoria', 255);
            $table->decimal('preco', 10, 2);
            $table->string('url_imagem', 255);
            $table->boolean('ativo')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produto');
    }
};
