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
        'status',
        'valor_total',
    ];

    protected $casts = [
        'valor_total' => 'decimal:2',
        'criado_em' => 'datetime',
    ];

    // Configurar timestamps personalizados
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null; // Não temos updated_at na tabela

    // Relacionamentos
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

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'id_pedido');
    }

    // Scopes
    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('id_usuario', $usuarioId);
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
