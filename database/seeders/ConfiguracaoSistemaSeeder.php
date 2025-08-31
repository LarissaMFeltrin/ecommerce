<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracaoSistema;

class ConfiguracaoSistemaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configuracoes = [
            // Configurações da Loja
            [
                'chave' => 'nome_loja',
                'valor' => 'Minha Loja Online',
                'tipo' => 'string',
                'categoria' => 'loja',
                'descricao' => 'Nome da loja que aparece no cabeçalho e título',
            ],
            [
                'chave' => 'slogan_loja',
                'valor' => 'Sua loja de confiança para todos os produtos',
                'tipo' => 'string',
                'categoria' => 'loja',
                'descricao' => 'Slogan ou descrição da loja',
            ],
            [
                'chave' => 'email_contato',
                'valor' => 'contato@minhaloja.com',
                'tipo' => 'string',
                'categoria' => 'loja',
                'descricao' => 'Email de contato da loja',
            ],
            [
                'chave' => 'telefone_contato',
                'valor' => '(11) 99999-9999',
                'tipo' => 'string',
                'categoria' => 'loja',
                'descricao' => 'Telefone de contato da loja',
            ],
            [
                'chave' => 'endereco_loja',
                'valor' => 'Rua das Flores, 123 - São Paulo/SP',
                'tipo' => 'string',
                'categoria' => 'loja',
                'descricao' => 'Endereço físico da loja',
            ],
            [
                'chave' => 'horario_funcionamento',
                'valor' => 'Segunda a Sexta: 8h às 18h | Sábado: 8h às 12h',
                'tipo' => 'string',
                'categoria' => 'loja',
                'descricao' => 'Horário de funcionamento da loja',
            ],

            // Configurações de Frete
            [
                'chave' => 'frete_gratis_acima',
                'valor' => '100.00',
                'tipo' => 'integer',
                'categoria' => 'frete',
                'descricao' => 'Valor mínimo para frete grátis (em centavos)',
            ],
            [
                'chave' => 'taxa_frete_padrao',
                'valor' => '15.90',
                'tipo' => 'integer',
                'categoria' => 'frete',
                'descricao' => 'Taxa de frete padrão (em centavos)',
            ],
            [
                'chave' => 'prazo_entrega_padrao',
                'valor' => '3',
                'tipo' => 'integer',
                'categoria' => 'frete',
                'descricao' => 'Prazo de entrega padrão em dias úteis',
            ],
            [
                'chave' => 'frete_expresso_disponivel',
                'valor' => 'true',
                'tipo' => 'boolean',
                'categoria' => 'frete',
                'descricao' => 'Se o frete expresso está disponível',
            ],
            [
                'chave' => 'taxa_frete_expresso',
                'valor' => '25.90',
                'tipo' => 'integer',
                'categoria' => 'frete',
                'descricao' => 'Taxa de frete expresso (em centavos)',
            ],

            // Configurações de Pagamento
            [
                'chave' => 'pagamento_cartao_credito',
                'valor' => 'true',
                'tipo' => 'boolean',
                'categoria' => 'pagamento',
                'descricao' => 'Aceitar pagamento com cartão de crédito',
            ],
            [
                'chave' => 'pagamento_cartao_debito',
                'valor' => 'true',
                'tipo' => 'boolean',
                'categoria' => 'pagamento',
                'descricao' => 'Aceitar pagamento com cartão de débito',
            ],
            [
                'chave' => 'pagamento_pix',
                'valor' => 'true',
                'tipo' => 'boolean',
                'categoria' => 'pagamento',
                'descricao' => 'Aceitar pagamento com PIX',
            ],
            [
                'chave' => 'pagamento_boleto',
                'valor' => 'true',
                'tipo' => 'boolean',
                'categoria' => 'pagamento',
                'descricao' => 'Aceitar pagamento com boleto',
            ],
            [
                'chave' => 'parcelamento_maximo',
                'valor' => '12',
                'tipo' => 'integer',
                'categoria' => 'pagamento',
                'descricao' => 'Número máximo de parcelas',
            ],

            // Configurações de SEO
            [
                'chave' => 'meta_title_padrao',
                'valor' => 'Minha Loja Online - Produtos de Qualidade',
                'tipo' => 'string',
                'categoria' => 'seo',
                'descricao' => 'Meta title padrão para páginas sem título específico',
            ],
            [
                'chave' => 'meta_description_padrao',
                'valor' => 'Encontre os melhores produtos com preços imbatíveis. Entrega rápida e segura em todo Brasil.',
                'tipo' => 'string',
                'categoria' => 'seo',
                'descricao' => 'Meta description padrão para páginas sem descrição específica',
            ],
            [
                'chave' => 'google_analytics_id',
                'valor' => '',
                'tipo' => 'string',
                'categoria' => 'seo',
                'descricao' => 'ID do Google Analytics',
            ],
            [
                'chave' => 'facebook_pixel_id',
                'valor' => '',
                'tipo' => 'string',
                'categoria' => 'seo',
                'descricao' => 'ID do Facebook Pixel',
            ],

            // Configurações Gerais
            [
                'chave' => 'manutencao_ativo',
                'valor' => 'false',
                'tipo' => 'boolean',
                'categoria' => 'geral',
                'descricao' => 'Ativar modo de manutenção',
            ],
            [
                'chave' => 'manutencao_mensagem',
                'valor' => 'Estamos em manutenção. Volte em breve!',
                'tipo' => 'string',
                'categoria' => 'geral',
                'descricao' => 'Mensagem exibida durante manutenção',
            ],
            [
                'chave' => 'registro_usuarios_aberto',
                'valor' => 'true',
                'tipo' => 'boolean',
                'categoria' => 'geral',
                'descricao' => 'Permitir registro de novos usuários',
            ],
            [
                'chave' => 'avaliacoes_aprovacao_automatica',
                'valor' => 'false',
                'tipo' => 'boolean',
                'categoria' => 'geral',
                'descricao' => 'Aprovar avaliações automaticamente',
            ],
            [
                'chave' => 'limite_produtos_carrinho',
                'valor' => '50',
                'tipo' => 'integer',
                'categoria' => 'geral',
                'descricao' => 'Limite de produtos no carrinho',
            ],
        ];

        foreach ($configuracoes as $config) {
            ConfiguracaoSistema::set(
                $config['chave'],
                $config['valor'],
                $config['tipo'],
                $config['categoria'],
                $config['descricao']
            );
        }

        $this->command->info('✅ Configurações do sistema criadas com sucesso!');
        $this->command->info('📋 Total de configurações: ' . count($configuracoes));
    }
}
