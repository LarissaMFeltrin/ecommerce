<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupons';

    protected $fillable = [
        'codigo',
        'descricao',
        'tipo',
        'valor',
        'valor_minimo',
        'quantidade_usos',
        'validade',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'valor_minimo' => 'decimal:2',
        'validade' => 'date',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cupom');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeValido($query)
    {
        return $query->where('validade', '>=', now()->toDateString());
    }

    // Métodos
    public function isValido()
    {
        return $this->ativo &&
            $this->validade >= now()->toDateString() &&
            ($this->quantidade_usos === null || $this->pedidos()->count() < $this->quantidade_usos);
    }

    public function calcularDesconto($valorTotal)
    {
        if ($valorTotal < $this->valor_minimo) {
            return 0;
        }

        if ($this->tipo === 'percentual') {
            return ($valorTotal * $this->valor) / 100;
        }

        return $this->valor;
    }

    // Acessors
    public function getValorFormatadoAttribute()
    {
        if ($this->tipo === 'percentual') {
            return $this->valor . '%';
        }

        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }
}