<?php

use App\Enums\Origem;
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
        Schema::create('estoque', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_id')
                  ->unique()
                  ->constrained('produto')
                  ->cascadeOnDelete();
            $table->integer('quantidade')->default(0);
            $table->string('ultima_origem')->default(Origem::cadastro_whatsapp->value);
            $table->timestamp('atualizado_em')->useCurrent();
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
