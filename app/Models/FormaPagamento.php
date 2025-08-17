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
        'codigo_sistema',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    // Acessors
    public function getNomeFormatadoAttribute()
    {
        $nomes = [
            'pix' => 'PIX',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'boleto' => 'Boleto Bancário',
            'dinheiro' => 'Dinheiro',
        ];

        return $nomes[$this->codigo_sistema] ?? $this->nome;
    }
}