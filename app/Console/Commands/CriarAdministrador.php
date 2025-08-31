<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Administrador;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CriarAdministrador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:criar {--email= : Email do administrador} {--nome= : Nome do administrador} {--senha= : Senha do administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Criar um novo administrador no sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Criação de Administrador ===');

        // Obter dados do administrador
        $email = $this->option('email') ?: $this->ask('Email do administrador');
        $nome = $this->option('nome') ?: $this->ask('Nome completo do administrador');
        $senha = $this->option('senha') ?: $this->secret('Senha do administrador');
        $confirmarSenha = $this->secret('Confirme a senha');

        // Validar dados
        if ($senha !== $confirmarSenha) {
            $this->error('As senhas não coincidem!');
            return 1;
        }

        $validator = Validator::make([
            'email' => $email,
            'nome' => $nome,
            'senha' => $senha,
        ], [
            'email' => 'required|email|unique:usuarios,email',
            'nome' => 'required|string|min:3|max:255',
            'senha' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        try {
            // Criar usuário
            $usuario = Usuario::create([
                'nome' => $nome,
                'email' => $email,
                'senha' => Hash::make($senha),
                'ativo' => true,
                'email_verificado_em' => now(),
            ]);

            // Criar administrador
            Administrador::create([
                'id_usuario' => $usuario->id,
                'nome' => $nome,
                'email' => $email,
                'senha' => Hash::make($senha),
                'tipo' => 'super_admin',
                'ativo' => true,
            ]);

            $this->info('✅ Administrador criado com sucesso!');
            $this->info("Nome: {$nome}");
            $this->info("Email: {$email}");
            $this->info("ID do usuário: {$usuario->id}");
            $this->info('');
            $this->info('Acesse o sistema em: /admin');
            $this->info('Use as credenciais acima para fazer login.');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao criar administrador: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}