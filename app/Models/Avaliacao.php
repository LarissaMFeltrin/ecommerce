<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avaliacao extends Model
{
    use HasFactory;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'id_usuario',
        'id_produto',
        'nota',
        'comentario',
    ];

    protected $casts = [
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

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    // Scopes
    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('id_produto', $produtoId);
    }

    public function scopePorNota($query, $nota)
    {
        return $query->where('nota', $nota);
    }

    // Acessors
    public function getNotaFormatadaAttribute()
    {
        return $this->nota . '/5';
    }

    public function getEstrelasAttribute()
    {
        return str_repeat('★', $this->nota) . str_repeat('☆', 5 - $this->nota);
    }
}