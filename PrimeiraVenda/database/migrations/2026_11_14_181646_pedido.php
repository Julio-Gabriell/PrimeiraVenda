<?php

use App\Enums\StatusPedido;
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
        Schema::create('pedido', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_nome', 255);
            $table->string('cliente_telefone', 20)->nullable();
            $table->decimal('valor_total', 10, 2);
            $table->string('status')->default(StatusPedido::pendente->value);
            $table->text('pix_copia_cola')->nullable();
            $table->string('checkout_url', 500)->nullable();
            $table->timestamp('pago_em')->nullable();
            $table->timestamps();
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
