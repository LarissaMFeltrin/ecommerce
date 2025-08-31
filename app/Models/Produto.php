<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'preco',
        'estoque',
        'id_categoria',
        'imagem',
        'ativo',
        'empresa_id',
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

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

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
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

    public function getImagensAttribute()
    {
        if ($this->imagem) {
            $imagens = json_decode($this->imagem, true);
            if (is_array($imagens)) {
                return collect($imagens)->map(function ($imagem) {
                    return (object) ['caminho' => $imagem];
                });
            }
        }
        return collect();
    }

    // Route Model Binding
    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Mutators
    public function setSlugAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['slug'] = Str::slug($this->nome);
        } else {
            $this->attributes['slug'] = Str::slug($value);
        }
    }
}