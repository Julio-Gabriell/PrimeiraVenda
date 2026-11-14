<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\Origem;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('estoque', function(Blueprint $table) {
            $table->id();

            $table->foreignId('produto_id')
                  ->constrained('produto');

            $table->integer('quantidade');
            $table->string('ultima_origem')->default(Origem::cadastro_whatsapp->value);
            $table->timestamp('atualizado_em');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estoque');
    }
};
