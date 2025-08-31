<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use App\Models\Administrador;
use App\Models\Empresa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CriarAdministrador extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:criar {--email= : Email do administrador} {--nome= : Nome do administrador} {--senha= : Senha do administrador} {--tipo= : Tipo do administrador (admin/super_admin)} {--empresa= : ID da empresa (opcional)}';

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
        $tipo = $this->option('tipo') ?: $this->choice('Tipo de administrador', ['admin', 'super_admin'], 'admin');
        $empresaId = $this->option('empresa');

        // Se não foi especificada empresa, perguntar se quer criar uma
        if (!$empresaId && $tipo === 'admin') {
            $criarEmpresa = $this->confirm('Deseja criar uma nova empresa para este administrador?', false);

            if ($criarEmpresa) {
                $empresaId = $this->criarEmpresa();
            } else {
                $empresaId = $this->selecionarEmpresa();
            }
        }

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
            'tipo' => $tipo,
            'empresa_id' => $empresaId,
        ], [
            'email' => 'required|email|unique:usuarios,email',
            'nome' => 'required|string|min:3|max:255',
            'senha' => 'required|string|min:6',
            'tipo' => 'required|in:admin,super_admin',
            'empresa_id' => $tipo === 'admin' ? 'required|exists:empresas,id' : 'nullable|exists:empresas,id',
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
                'empresa_id' => $empresaId,
                'email_verificado_em' => now(),
            ]);

            // Criar administrador
            Administrador::create([
                'id_usuario' => $usuario->id,
                'nome' => $nome,
                'email' => $email,
                'senha' => Hash::make($senha),
                'tipo' => $tipo,
                'empresa_id' => $empresaId,
                'ativo' => true,
            ]);

            $this->info('✅ Administrador criado com sucesso!');
            $this->info("Nome: {$nome}");
            $this->info("Email: {$email}");
            $this->info("Tipo: {$tipo}");
            $this->info("ID do usuário: {$usuario->id}");

            if ($empresaId) {
                $empresa = Empresa::find($empresaId);
                $this->info("Empresa: {$empresa->nome_fantasia}");
            }

            $this->info('');
            $this->info('Acesse o sistema em: /admin');
            $this->info('Use as credenciais acima para fazer login.');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao criar administrador: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Criar uma nova empresa
     */
    private function criarEmpresa()
    {
        $this->info('=== Criando Nova Empresa ===');

        $nome = $this->ask('Nome da empresa');
        $nomeFantasia = $this->ask('Nome fantasia da empresa');
        $cnpj = $this->ask('CNPJ da empresa');
        $email = $this->ask('Email da empresa');
        $telefone = $this->ask('Telefone da empresa');
        $ramo = $this->choice('Ramo de atividade', ['ecommerce', 'varejo', 'servicos', 'outros'], 'ecommerce');
        $plano = $this->choice('Plano', ['basico', 'premium', 'enterprise'], 'basico');

        try {
            $empresa = Empresa::create([
                'nome' => $nome,
                'nome_fantasia' => $nomeFantasia,
                'cnpj' => $cnpj,
                'email' => $email,
                'telefone' => $telefone,
                'ramo_atividade' => $ramo,
                'plano' => $plano,
                'tema' => 'default',
                'cor_primaria' => '#007bff',
                'cor_secundaria' => '#6c757d',
                'ativo' => true,
                'data_contrato' => now(),
                'data_vencimento' => now()->addYear(),
                'configuracoes' => [
                    'frete_gratis_acima' => 10000, // R$ 100,00
                    'taxa_frete_padrao' => 1500,   // R$ 15,00
                    'prazo_entrega_padrao' => 5,
                ],
            ]);

            $this->info("✅ Empresa '{$empresa->nome_fantasia}' criada com sucesso! (ID: {$empresa->id})");
            return $empresa->id;
        } catch (\Exception $e) {
            $this->error('❌ Erro ao criar empresa: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Selecionar uma empresa existente
     */
    private function selecionarEmpresa()
    {
        $empresas = Empresa::ativa()->orderBy('nome_fantasia')->get();

        if ($empresas->isEmpty()) {
            $this->warn('⚠️  Nenhuma empresa encontrada. Criando uma nova empresa...');
            return $this->criarEmpresa();
        }

        $this->info('=== Empresas Disponíveis ===');
        $empresasArray = $empresas->pluck('nome_fantasia', 'id')->toArray();

        $empresaId = $this->choice('Selecione a empresa:', $empresasArray);

        return array_search($empresaId, $empresasArray);
    }
}
