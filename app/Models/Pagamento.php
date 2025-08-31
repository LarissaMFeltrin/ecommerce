<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';

    protected $fillable = [
        'id_pedido',
        'valor',
        'forma_pagamento',
        'status',
        'codigo_transacao',
        'data_pagamento',
        'empresa_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_pagamento' => 'datetime',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    // Scopes
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAprovado($query)
    {
        return $query->where('status', 'aprovado');
    }

    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeCancelado($query)
    {
        return $query->where('status', 'cancelado');
    }

    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Acessors
    public function getStatusFormatadoAttribute()
    {
        $status = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'cancelado' => 'Cancelado',
            'reembolsado' => 'Reembolsado'
        ];

        return $status[$this->status] ?? $this->status;
    }

    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getFormaPagamentoFormatadaAttribute()
    {
        $formas = [
            'pix' => 'PIX',
            'cartao_credito' => 'Cartão de Crédito',
            'cartao_debito' => 'Cartão de Débito',
            'boleto' => 'Boleto'
        ];

        return $formas[$this->forma_pagamento] ?? $this->forma_pagamento;
    }
}
