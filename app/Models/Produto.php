<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'estoque',
        'id_categoria',
        'imagem',
        'ativo',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria');
    }

    public function carrinho()
    {
        return $this->hasMany(Carrinho::class, 'id_produto');
    }

    public function itensPedido()
    {
        return $this->hasMany(ItemPedido::class, 'id_produto');
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class, 'id_produto');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorCategoria($query, $categoriaId)
    {
        return $query->where('id_categoria', $categoriaId);
    }

    public function scopeEmEstoque($query)
    {
        return $query->where('estoque', '>', 0);
    }

    // Acessors
    public function getPrecoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->preco, 2, ',', '.');
    }

    public function getDisponivelAttribute()
    {
        return $this->ativo && $this->estoque > 0;
    }
}