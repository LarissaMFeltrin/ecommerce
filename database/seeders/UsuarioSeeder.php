<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Usuario;
use App\Models\Administrador;
use Illuminate\Support\Facades\DB;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar tabelas existentes (deletar em vez de truncar)
        DB::table('avaliacoes')->delete();
        DB::table('carrinho')->delete();
        DB::table('itens_pedido')->delete();
        DB::table('pedidos')->delete();
        DB::table('enderecos')->delete();
        DB::table('administradores')->delete();
        DB::table('usuarios')->delete();

        // Criar usuário administrador
        $admin = Usuario::create([
            'nome' => 'Administrador',
            'email' => 'admin@loja.com',
            'senha' => '123456', // Senha em texto plano
            'telefone' => '(11) 99999-9999',
            'cpf' => '123.456.789-00',
            'data_nascimento' => '1990-01-01',
            'ativo' => true,
        ]);

        // Criar administrador no sistema
        Administrador::create([
            'id_usuario' => $admin->id,
            'nome' => 'Administrador',
            'email' => 'admin@loja.com',
            'senha' => '123456', // Senha em texto plano
            'tipo' => 'admin',
            'ativo' => true,
        ]);

        // Criar usuários de teste
        $usuarios = [
            [
                'nome' => 'João Silva',
                'email' => 'joao@email.com',
                'senha' => '123456', // Senha em texto plano
                'telefone' => '(11) 88888-8888',
                'cpf' => '111.222.333-44',
                'data_nascimento' => '1985-05-15',
                'ativo' => true,
            ],
            [
                'nome' => 'Maria Santos',
                'email' => 'maria@email.com',
                'senha' => '123456', // Senha em texto plano
                'telefone' => '(11) 77777-7777',
                'cpf' => '222.333.444-55',
                'data_nascimento' => '1992-08-20',
                'ativo' => true,
            ],
            [
                'nome' => 'Pedro Oliveira',
                'email' => 'pedro@email.com',
                'senha' => '123456', // Senha em texto plano
                'telefone' => '(11) 66666-6666',
                'cpf' => '333.444.555-66',
                'data_nascimento' => '1988-12-10',
                'ativo' => true,
            ],
            [
                'nome' => 'Ana Costa',
                'email' => 'ana@email.com',
                'senha' => '123456', // Senha em texto plano
                'telefone' => '(11) 55555-5555',
                'cpf' => '444.555.666-77',
                'data_nascimento' => '1995-03-25',
                'ativo' => true,
            ],
        ];

        foreach ($usuarios as $usuario) {
            Usuario::create($usuario);
        }

        $this->command->info('Usuários criados com sucesso!');
        $this->command->info('Admin: admin@loja.com / 123456');
        $this->command->info('Usuários de teste criados com senha: 123456');
        $this->command->info('⚠️  ATENÇÃO: Senhas armazenadas em texto plano no banco de dados!');
    }
}
