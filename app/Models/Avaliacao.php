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
        'titulo',
        'comentario',
        'recomenda',
        'status',
        'votos_uteis',
        'votos_nao_uteis',
        'resposta_admin',
        'respondido_em',
    ];

    protected $casts = [
        'nota' => 'integer',
        'recomenda' => 'boolean',
        'votos_uteis' => 'integer',
        'votos_nao_uteis' => 'integer',
        'respondido_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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

    public function scopeAprovadas($query)
    {
        return $query->where('status', 'aprovado');
    }

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeRejeitadas($query)
    {
        return $query->where('status', 'rejeitada');
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

    public function getStatusFormatadoAttribute()
    {
        $status = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'rejeitada' => 'Rejeitada'
        ];

        return $status[$this->status] ?? $this->status;
    }

    public function getRecomendaFormatadoAttribute()
    {
        return $this->recomenda ? 'Sim' : 'Não';
    }

    // Mutators
    public function setTituloAttribute($value)
    {
        $this->attributes['titulo'] = ucfirst($value);
    }

    public function setComentarioAttribute($value)
    {
        $this->attributes['comentario'] = ucfirst($value);
    }

    // Métodos auxiliares
    public function podeEditar()
    {
        return in_array($this->status, ['pendente', 'aprovado']);
    }

    public function podeExcluir()
    {
        return $this->status !== 'rejeitada';
    }

    public function marcarComoUtil()
    {
        $this->increment('votos_uteis');
    }

    public function marcarComoNaoUtil()
    {
        $this->increment('votos_nao_uteis');
    }

    public function aprovar()
    {
        $this->update(['status' => 'aprovado']);
    }

    public function rejeitar()
    {
        $this->update(['status' => 'rejeitada']);
    }

    public function responder($resposta)
    {
        $this->update([
            'resposta_admin' => $resposta,
            'respondido_em' => now()
        ]);
    }
}
