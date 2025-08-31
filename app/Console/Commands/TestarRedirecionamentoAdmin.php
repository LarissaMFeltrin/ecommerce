<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Administrador;

class TestarRedirecionamentoAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:testar-redirecionamento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o redirecionamento de administradores após login';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 TESTANDO REDIRECIONAMENTO DE ADMINISTRADORES');
        $this->info('================================================');

        // Buscar usuário administrador
        $admin = Usuario::where('email', 'admin@loja.com')->first();

        if (!$admin) {
            $this->error('❌ Usuário administrador não encontrado!');
            return Command::FAILURE;
        }

        $this->info("✅ Usuário encontrado: {$admin->nome} ({$admin->email})");

        // Verificar se é administrador
        $isAdmin = $admin->isAdmin();
        $this->info("🔐 É administrador? " . ($isAdmin ? 'SIM ✅' : 'NÃO ❌'));

        if ($isAdmin) {
            $this->info('🎯 Redirecionamento: /admin/administradores');
            $this->info('📋 URL da rota: ' . route('admin.administradores.index'));
        } else {
            $this->error('❌ Usuário não é administrador!');
            $this->info('🔍 Verificando tabela de administradores...');

            $adminRecord = Administrador::where('id_usuario', $admin->id)->first();
            if ($adminRecord) {
                $this->info("📋 Registro encontrado na tabela administradores:");
                $this->info("   - ID: {$adminRecord->id}");
                $this->info("   - Nome: {$adminRecord->nome}");
                $this->info("   - Email: {$adminRecord->email}");
                $this->info("   - Tipo: {$adminRecord->tipo}");
                $this->info("   - Ativo: " . ($adminRecord->ativo ? 'Sim' : 'Não'));
            } else {
                $this->error('❌ Nenhum registro encontrado na tabela administradores!');
            }
        }

        $this->info("\n🎯 TESTE COMPLETO!");
        $this->info('Para testar o redirecionamento:');
        $this->info('1. Acesse: http://127.0.0.1:8000/login');
        $this->info('2. Faça login com: admin@loja.com / 123456');
        $this->info('3. Deve ser redirecionado para: /admin/administradores');

        return Command::SUCCESS;
    }
}