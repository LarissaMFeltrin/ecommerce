<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'id_usuario',
        'id_endereco',
        'id_cupom',
        'valor_subtotal',
        'valor_frete',
        'valor_desconto',
        'valor_total',
        'forma_pagamento',
        'observacoes',
        'empresa_id',
    ];

    protected $casts = [
        'valor_subtotal' => 'decimal:2',
        'valor_frete' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_total' => 'decimal:2',
    ];

    // Configurar timestamps personalizados
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null; // Não temos updated_at na tabela

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function endereco()
    {
        return $this->belongsTo(Endereco::class, 'id_endereco');
    }

    public function cupom()
    {
        return $this->belongsTo(Cupom::class, 'id_cupom');
    }

    public function itens()
    {
        return $this->hasMany(ItemPedido::class, 'id_pedido');
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class, 'id_pedido');
    }

    // Scopes
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('id_usuario', $usuarioId);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecentes($query)
    {
        return $query->orderBy('criado_em', 'desc');
    }

    // Acessors
    public function getValorTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    public function getStatusFormatadoAttribute()
    {
        $status = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'em_processamento' => 'Em Processamento',
            'enviado' => 'Enviado',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
        ];

        return $status[$this->status] ?? $this->status;
    }
}
