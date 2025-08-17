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
        Schema::create('financeiro_lancamentos', function (Blueprint $table) {
            $table->id();
            $table->string('tipo', 20); // receita ou despesa
            $table->string('descricao', 255);
            $table->foreignId('id_pedido')->nullable()->constrained('pedidos');
            $table->decimal('valor', 10, 2);
            $table->timestamp('data_lancamento');
            $table->timestamp('data_pagamento')->nullable();
            $table->string('status', 20);
            $table->timestamp('criado_em')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financeiro_lancamentos');
    }
};