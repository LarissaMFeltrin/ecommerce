<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empresa;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $empresas = [
            [
                'nome' => 'Casa de Decoração Ltda',
                'nome_fantasia' => 'Casa de Decoração',
                'cnpj' => '12.345.678/0001-90',
                'email' => 'contato@casadecoracao.com',
                'telefone' => '(11) 99999-9999',
                'endereco' => 'Rua das Flores, 123',
                'cidade' => 'São Paulo',
                'estado' => 'SP',
                'cep' => '01234-567',
                'dominio' => 'casadecoracao.com',
                'tema' => 'decoracao',
                'cor_primaria' => '#8B4513',
                'cor_secundaria' => '#DEB887',
                'descricao' => 'Especializada em produtos de decoração para casa',
                'ramo_atividade' => 'ecommerce',
                'plano' => 'premium',
                'ativo' => true,
                'data_contrato' => now(),
                'data_vencimento' => now()->addYear(),
                'configuracoes' => [
                    'frete_gratis_acima' => 15000, // R$ 150,00
                    'taxa_frete_padrao' => 2000,   // R$ 20,00
                    'prazo_entrega_padrao' => 7,
                ],
            ],
            [
                'nome' => 'Tecnologia Avançada Ltda',
                'nome_fantasia' => 'TechStore',
                'cnpj' => '98.765.432/0001-10',
                'email' => 'vendas@techstore.com',
                'telefone' => '(21) 88888-8888',
                'endereco' => 'Av. Tecnologia, 456',
                'cidade' => 'Rio de Janeiro',
                'estado' => 'RJ',
                'cep' => '20000-000',
                'dominio' => 'techstore.com',
                'tema' => 'tecnologia',
                'cor_primaria' => '#007BFF',
                'cor_secundaria' => '#6C757D',
                'descricao' => 'Loja especializada em produtos de tecnologia',
                'ramo_atividade' => 'ecommerce',
                'plano' => 'enterprise',
                'ativo' => true,
                'data_contrato' => now(),
                'data_vencimento' => now()->addYear(),
                'configuracoes' => [
                    'frete_gratis_acima' => 20000, // R$ 200,00
                    'taxa_frete_padrao' => 2500,   // R$ 25,00
                    'prazo_entrega_padrao' => 5,
                ],
            ],
            [
                'nome' => 'Moda Fashion Ltda',
                'nome_fantasia' => 'Fashion Store',
                'cnpj' => '55.444.333/0001-22',
                'email' => 'contato@fashionstore.com',
                'telefone' => '(31) 77777-7777',
                'endereco' => 'Rua da Moda, 789',
                'cidade' => 'Belo Horizonte',
                'estado' => 'MG',
                'cep' => '30000-000',
                'dominio' => 'fashionstore.com',
                'tema' => 'moda',
                'cor_primaria' => '#FF69B4',
                'cor_secundaria' => '#FFC0CB',
                'descricao' => 'Loja de roupas e acessórios da moda',
                'ramo_atividade' => 'ecommerce',
                'plano' => 'basico',
                'ativo' => true,
                'data_contrato' => now(),
                'data_vencimento' => now()->addYear(),
                'configuracoes' => [
                    'frete_gratis_acima' => 10000, // R$ 100,00
                    'taxa_frete_padrao' => 1500,   // R$ 15,00
                    'prazo_entrega_padrao' => 10,
                ],
            ],
        ];

        foreach ($empresas as $empresaData) {
            Empresa::create($empresaData);
        }

        $this->command->info('✅ Empresas criadas com sucesso!');
    }
}
