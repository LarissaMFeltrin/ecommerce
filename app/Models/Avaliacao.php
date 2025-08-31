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
        'aprovada',
        'empresa_id',
    ];

    protected $casts = [
        'nota' => 'integer',
        'aprovada' => 'boolean',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
    }

    // Scopes
    public function scopeAprovada($query)
    {
        return $query->where('aprovada', true);
    }

    public function scopePendente($query)
    {
        return $query->where('aprovada', false);
    }

    public function scopePorProduto($query, $produtoId)
    {
        return $query->where('id_produto', $produtoId);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('id_usuario', $usuarioId);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Acessors
    public function getStatusAttribute()
    {
        return $this->aprovada ? 'Aprovada' : 'Pendente';
    }

    public function getNotaFormatadaAttribute()
    {
        return number_format($this->nota, 1);
    }
}
