<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    // Desabilitar timestamps
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'ativa',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    // Relacionamentos
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'id_categoria');
    }

    // Scopes
    public function scopeAtiva($query)
    {
        return $query->where('ativa', true);
    }

    // Acessors
    public function getProdutosAtivosAttribute()
    {
        return $this->produtos()->ativo()->get();
    }
}
