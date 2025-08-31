<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Empresa;
use App\Models\Usuario;
use App\Models\Administrador;

class TestarSistemaMultiTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sistema:testar-multi-tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o sistema multi-tenant com empresas de diferentes ramos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏢 TESTANDO SISTEMA MULTI-TENANT');
        $this->info('==================================');

        // Verificar empresas existentes
        $empresas = Empresa::all();

        if ($empresas->count() === 0) {
            $this->warn('⚠️  Nenhuma empresa encontrada. Execute primeiro: php artisan db:seed --class=EmpresaSeeder');
            return Command::FAILURE;
        }

        $this->info("✅ Total de empresas: {$empresas->count()}");

        // Mostrar empresas por ramo de atividade
        $this->info("\n📊 EMPRESAS POR RAMO DE ATIVIDADE:");
        $this->info('-----------------------------------');

        $ramos = [];
        foreach ($empresas as $empresa) {
            $ramos[$empresa->ramo_atividade][] = $empresa;
        }

        foreach ($ramos as $ramo => $empresasRamo) {
            $this->info("\n🎯 {$ramo}:");
            foreach ($empresasRamo as $empresa) {
                $this->info("   • {$empresa->nome_fantasia} ({$empresa->plano})");
                $this->info("     - CNPJ: {$empresa->cnpj}");
                $this->info("     - Email: {$empresa->email}");
                $this->info("     - Tema: {$empresa->tema}");
                $this->info("     - Cores: {$empresa->cor_primaria} / {$empresa->cor_secundaria}");
                $this->info("     - Status: " . ($empresa->ativo ? 'Ativa ✅' : 'Inativa ❌'));

                // Mostrar configurações específicas
                if ($empresa->configuracoes) {
                    $this->info("     - Frete grátis acima: R$ " . number_format($empresa->configuracoes['frete_gratis_acima'] / 100, 2, ',', '.'));
                    $this->info("     - Taxa frete padrão: R$ " . number_format($empresa->configuracoes['taxa_frete_padrao'] / 100, 2, ',', '.'));
                    $this->info("     - Prazo entrega: {$empresa->configuracoes['prazo_entrega_padrao']} dias");
                }
            }
        }

        // Mostrar estatísticas gerais
        $this->info("\n📈 ESTATÍSTICAS GERAIS:");
        $this->info('------------------------');

        $totalUsuarios = Usuario::count();
        $totalAdministradores = Administrador::count();
        $totalProdutos = \App\Models\Produto::count();
        $totalPedidos = \App\Models\Pedido::count();

        $this->info("👥 Total de usuários: {$totalUsuarios}");
        $this->info("👑 Total de administradores: {$totalAdministradores}");
        $this->info("📦 Total de produtos: {$totalProdutos}");
        $this->info("📋 Total de pedidos: {$totalPedidos}");

        // Mostrar planos disponíveis
        $this->info("\n💳 PLANOS DISPONÍVEIS:");
        $this->info('----------------------');

        $planos = Empresa::getPlanos();
        foreach ($planos as $codigo => $plano) {
            $this->info("🔹 {$plano['nome']} (R$ {$plano['preco']}):");
            $this->info("   - {$plano['descricao']}");
            foreach ($plano['limites'] as $limite => $valor) {
                $this->info("   - {$limite}: {$valor}");
            }
        }

        // Mostrar ramos de atividade
        $this->info("\n🏭 RAMOS DE ATIVIDADE DISPONÍVEIS:");
        $this->info('-----------------------------------');

        $ramos = Empresa::getRamosAtividade();
        foreach ($ramos as $codigo => $nome) {
            $this->info("🔸 {$nome} ({$codigo})");
        }

        $this->info("\n🎯 COMO IDENTIFICAR EMPRESAS:");
        $this->info('-------------------------------');
        $this->info('1. Cada empresa tem um CNPJ único');
        $this->info('2. Cada empresa tem um domínio personalizado (opcional)');
        $this->info('3. Cada empresa tem um tema visual específico');
        $this->info('4. Cada empresa tem cores personalizadas');
        $this->info('5. Cada empresa tem configurações específicas por ramo');
        $this->info('6. Cada empresa tem limites baseados no plano contratado');

        $this->info("\n🚀 URLs DE EXEMPLO:");
        $this->info('-------------------');
        foreach ($empresas->take(3) as $empresa) {
            if ($empresa->dominio) {
                $this->info("🌐 {$empresa->nome_fantasia}: http://{$empresa->dominio}.seudominio.com");
            }
        }

        $this->info("\n✅ TESTE COMPLETO!");
        $this->info('Para acessar a gestão de empresas:');
        $this->info('http://127.0.0.1:8000/admin/empresas');

        return Command::SUCCESS;
    }
}