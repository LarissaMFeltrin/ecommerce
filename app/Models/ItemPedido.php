<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedido extends Model
{
    use HasFactory;

    protected $table = 'itens_pedido';

    protected $fillable = [
        'id_pedido',
        'id_produto',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'empresa_id',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
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

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    // Scopes
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorPedido($query, $pedidoId)
    {
        return $query->where('id_pedido', $pedidoId);
    }

    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('id_produto', $produtoId);
    }

    // Acessors
    public function getPrecoUnitarioFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco_unitario, 2, ',', '.');
    }

    public function getSubtotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }

    // Mutators
    public function setSubtotalAttribute($value)
    {
        $this->attributes['subtotal'] = $this->preco_unitario * $this->quantidade;
    }
}
