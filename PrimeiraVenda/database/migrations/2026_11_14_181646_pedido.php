<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\StatusPedido;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pedido', function(Blueprint $table) {
            $table->id();

            $table->string('cliente_nome', 255);
            $table->string('cliente_telefone', 11);
            $table->decimal('valor_total', 10, 2);
            $table->string('status')->default(StatusPedido::pendente->value);
            $table->string('pix_copia_cola');
            $table->string('checkout_url');
            $table->timestamps();
            $table->timestamp('pago_em');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido');
    }
};
