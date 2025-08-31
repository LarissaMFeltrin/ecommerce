<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracaoSistema extends Model
{
    use HasFactory;

    protected $table = 'configuracoes_sistema';

    protected $fillable = [
        'chave',
        'valor',
        'tipo',
        'descricao',
        'empresa_id',
    ];

    protected $casts = [
        'valor' => 'json',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    // Scopes
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorChave($query, $chave)
    {
        return $query->where('chave', $chave);
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    // Métodos estáticos para configurações comuns
    public static function getConfiguracao($chave, $empresaId = null)
    {
        $query = static::where('chave', $chave);

        if ($empresaId) {
            $query->porEmpresa($empresaId);
        }

        $config = $query->first();

        return $config ? $config->valor : null;
    }

    public static function setConfiguracao($chave, $valor, $empresaId = null, $tipo = 'string', $descricao = null)
    {
        $config = static::updateOrCreate(
            ['chave' => $chave, 'empresa_id' => $empresaId],
            [
                'valor' => $valor,
                'tipo' => $tipo,
                'descricao' => $descricao
            ]
        );

        return $config;
    }

    // Acessors
    public function getValorFormatadoAttribute()
    {
        if (is_array($this->valor)) {
            return json_encode($this->valor, JSON_PRETTY_PRINT);
        }

        return $this->valor;
    }

    public function getTipoFormatadoAttribute()
    {
        $tipos = [
            'string' => 'Texto',
            'integer' => 'Número Inteiro',
            'decimal' => 'Número Decimal',
            'boolean' => 'Verdadeiro/Falso',
            'json' => 'JSON',
            'array' => 'Lista'
        ];

        return $tipos[$this->tipo] ?? $this->tipo;
    }
}
