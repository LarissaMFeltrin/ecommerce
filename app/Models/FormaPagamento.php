<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaPagamento extends Model
{
    use HasFactory;

    protected $table = 'formas_pagamento';

    protected $fillable = [
        'nome',
        'codigo',
        'ativo',
        'empresa_id',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'forma_pagamento', 'codigo');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    // Acessors
    public function getNomeFormatadoAttribute()
    {
        $nomes = [
            'pix' => 'PIX',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'boleto' => 'Boleto'
        ];

        return $nomes[$this->codigo] ?? $this->nome;
    }

    public function getIconeAttribute()
    {
        $icones = [
            'pix' => 'fas fa-qrcode',
            'cartao_credito' => 'fas fa-credit-card',
            'cartao_debito' => 'fas fa-credit-card',
            'boleto' => 'fas fa-barcode'
        ];

        return $icones[$this->codigo] ?? 'fas fa-money-bill';
    }
}
