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
        Schema::create('modalidades_frete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_transportadora')->constrained('transportadoras');
            $table->string('nome', 100);
            $table->integer('prazo_entrega');
            $table->decimal('valor_base', 10, 2);
            $table->decimal('valor_por_kilo', 10, 2);
            $table->string('faixa_cep_inicio', 10);
            $table->string('faixa_cep_fim', 10);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modalidades_frete');
    }
};