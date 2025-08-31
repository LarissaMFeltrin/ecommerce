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
        // Verificar e adicionar colunas na tabela usuarios se não existirem
        if (!Schema::hasColumn('usuarios', 'ativo')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->boolean('ativo')->default(true)->after('data_nascimento');
            });
        }

        if (!Schema::hasColumn('usuarios', 'email_verificado_em')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->timestamp('email_verificado_em')->nullable()->after('ativo');
            });
        }

        // Verificar e adicionar colunas na tabela administradores se não existirem
        if (!Schema::hasColumn('administradores', 'ativo')) {
            Schema::table('administradores', function (Blueprint $table) {
                $table->boolean('ativo')->default(true)->after('tipo');
            });
        }

        if (!Schema::hasColumn('administradores', 'id_usuario')) {
            Schema::table('administradores', function (Blueprint $table) {
                $table->foreignId('id_usuario')->nullable()->constrained('usuarios')->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover colunas adicionadas se existirem
        if (Schema::hasColumn('usuarios', 'ativo')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('ativo');
            });
        }

        if (Schema::hasColumn('usuarios', 'email_verificado_em')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->dropColumn('email_verificado_em');
            });
        }

        if (Schema::hasColumn('administradores', 'ativo')) {
            Schema::table('administradores', function (Blueprint $table) {
                $table->dropColumn('ativo');
            });
        }

        if (Schema::hasColumn('administradores', 'id_usuario')) {
            Schema::table('administradores', function (Blueprint $table) {
                $table->dropForeign(['id_usuario']);
                $table->dropColumn('id_usuario');
            });
        }
    }
};