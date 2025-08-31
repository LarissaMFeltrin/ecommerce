<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Administrador;

class ListarUsuariosSenhas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'usuarios:listar-senhas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista todos os usuários e administradores com suas senhas em texto plano';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔐 LISTANDO USUÁRIOS E SENHAS');
        $this->info('================================');

        // Listar usuários comuns
        $this->info("\n👥 USUÁRIOS COMUNS:");
        $this->info('-------------------');

        $usuarios = Usuario::all();
        if ($usuarios->count() > 0) {
            $headers = ['ID', 'Nome', 'Email', 'Senha', 'Telefone', 'CPF', 'Ativo'];
            $rows = [];

            foreach ($usuarios as $usuario) {
                $rows[] = [
                    $usuario->id,
                    $usuario->nome,
                    $usuario->email,
                    $usuario->senha,
                    $usuario->telefone ?? 'N/A',
                    $usuario->cpf ?? 'N/A',
                    $usuario->ativo ? 'Sim' : 'Não'
                ];
            }

            $this->table($headers, $rows);
        } else {
            $this->warn('Nenhum usuário encontrado.');
        }

        // Listar administradores
        $this->info("\n👑 ADMINISTRADORES:");
        $this->info('-------------------');

        $administradores = Administrador::all();
        if ($administradores->count() > 0) {
            $headers = ['ID', 'Nome', 'Email', 'Senha', 'Tipo', 'Ativo'];
            $rows = [];

            foreach ($administradores as $admin) {
                $rows[] = [
                    $admin->id,
                    $admin->nome,
                    $admin->email,
                    $admin->senha,
                    $admin->tipo,
                    $admin->ativo ? 'Sim' : 'Não'
                ];
            }

            $this->table($headers, $rows);
        } else {
            $this->warn('Nenhum administrador encontrado.');
        }

        $this->info("\n✅ Comando executado com sucesso!");
        $this->warn("⚠️  ATENÇÃO: As senhas estão sendo exibidas em texto plano!");

        return Command::SUCCESS;
    }
}
