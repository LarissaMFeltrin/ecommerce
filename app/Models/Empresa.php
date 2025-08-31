<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    protected $table = 'empresas';

    protected $fillable = [
        'nome',
        'nome_fantasia',
        'cnpj',
        'email',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'dominio',
        'tema',
        'logo',
        'cor_primaria',
        'cor_secundaria',
        'descricao',
        'ramo_atividade',
        'configuracoes',
        'ativo',
        'data_contrato',
        'data_vencimento',
        'plano',
    ];

    protected $casts = [
        'configuracoes' => 'array',
        'ativo' => 'boolean',
        'data_contrato' => 'datetime',
        'data_vencimento' => 'datetime',
    ];

    /**
     * Relacionamentos
     */
    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }

    public function administradores()
    {
        return $this->hasMany(Administrador::class);
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class);
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    public function configuracoes()
    {
        return $this->hasMany(ConfiguracaoSistema::class);
    }

    /**
     * Scopes
     */
    public function scopeAtiva($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorRamo($query, $ramo)
    {
        return $query->where('ramo_atividade', $ramo);
    }

    public function scopePorPlano($query, $plano)
    {
        return $query->where('plano', $plano);
    }

    /**
     * Verificar se a empresa está ativa
     */
    public function isAtiva()
    {
        return $this->ativo && $this->data_vencimento > now();
    }

    /**
     * Verificar se a empresa está vencida
     */
    public function isVencida()
    {
        return $this->data_vencimento && $this->data_vencimento < now();
    }

    /**
     * Obter configuração específica da empresa
     */
    public function getConfiguracao($chave, $padrao = null)
    {
        if (!$this->configuracoes) {
            return $padrao;
        }

        return $this->configuracoes[$chave] ?? $padrao;
    }

    /**
     * Definir configuração específica da empresa
     */
    public function setConfiguracao($chave, $valor)
    {
        $configuracoes = $this->configuracoes ?? [];
        $configuracoes[$chave] = $valor;
        $this->configuracoes = $configuracoes;
        $this->save();
    }

    /**
     * Obter URL da empresa
     */
    public function getUrlAttribute()
    {
        if ($this->dominio) {
            return "http://{$this->dominio}." . config('app.domain', 'localhost');
        }

        return route('empresa.show', $this->id);
    }

    /**
     * Obter cores da empresa para CSS
     */
    public function getCoresCssAttribute()
    {
        return [
            '--cor-primaria' => $this->cor_primaria,
            '--cor-secundaria' => $this->cor_secundaria,
        ];
    }

    /**
     * Obter estatísticas da empresa
     */
    public function getEstatisticas()
    {
        return [
            'total_usuarios' => $this->usuarios()->count(),
            'total_produtos' => $this->produtos()->count(),
            'total_pedidos' => $this->pedidos()->count(),
            'total_vendas' => $this->pedidos()->where('status', 'aprovado')->sum('valor_total'),
            'produtos_ativos' => $this->produtos()->where('ativo', true)->count(),
            'usuarios_ativos' => $this->usuarios()->where('ativo', true)->count(),
        ];
    }

    /**
     * Verificar se a empresa pode usar determinada funcionalidade baseada no plano
     */
    public function podeUsar($funcionalidade)
    {
        $limites = [
            'basico' => [
                'produtos' => 100,
                'usuarios' => 50,
                'pedidos_mes' => 100,
                'storage_gb' => 1,
            ],
            'profissional' => [
                'produtos' => 1000,
                'usuarios' => 500,
                'pedidos_mes' => 1000,
                'storage_gb' => 10,
            ],
            'enterprise' => [
                'produtos' => -1, // ilimitado
                'usuarios' => -1, // ilimitado
                'pedidos_mes' => -1, // ilimitado
                'storage_gb' => 100,
            ],
        ];

        $plano = $this->plano ?? 'basico';
        $limite = $limites[$plano][$funcionalidade] ?? 0;

        if ($limite === -1) {
            return true; // ilimitado
        }

        // Implementar lógica de verificação específica
        switch ($funcionalidade) {
            case 'produtos':
                return $this->produtos()->count() < $limite;
            case 'usuarios':
                return $this->usuarios()->count() < $limite;
            case 'pedidos_mes':
                $pedidos_mes = $this->pedidos()->whereMonth('created_at', now()->month)->count();
                return $pedidos_mes < $limite;
            default:
                return true;
        }
    }

    /**
     * Obter ramos de atividade disponíveis
     */
    public static function getRamosAtividade()
    {
        return [
            'perfumes' => 'Perfumes e Cosméticos',
            'roupas' => 'Roupas e Moda',
            'eletronicos' => 'Eletrônicos',
            'casa' => 'Casa e Decoração',
            'esporte' => 'Esporte e Lazer',
            'livros' => 'Livros e Papelaria',
            'brinquedos' => 'Brinquedos e Games',
            'alimentacao' => 'Alimentação',
            'automotivo' => 'Automotivo',
            'outros' => 'Outros',
        ];
    }

    /**
     * Obter planos disponíveis
     */
    public static function getPlanos()
    {
        return [
            'basico' => [
                'nome' => 'Básico',
                'preco' => 99.90,
                'descricao' => 'Ideal para pequenas empresas',
                'limites' => [
                    'produtos' => 100,
                    'usuarios' => 50,
                    'pedidos_mes' => 100,
                ],
            ],
            'profissional' => [
                'nome' => 'Profissional',
                'preco' => 199.90,
                'descricao' => 'Para empresas em crescimento',
                'limites' => [
                    'produtos' => 1000,
                    'usuarios' => 500,
                    'pedidos_mes' => 1000,
                ],
            ],
            'enterprise' => [
                'nome' => 'Enterprise',
                'preco' => 499.90,
                'descricao' => 'Para grandes empresas',
                'limites' => [
                    'produtos' => 'Ilimitado',
                    'usuarios' => 'Ilimitado',
                    'pedidos_mes' => 'Ilimitado',
                ],
            ],
        ];
    }
}
