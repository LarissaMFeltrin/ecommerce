<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrinho extends Model
{
    use HasFactory;

    protected $table = 'carrinho';

    protected $fillable = [
        'id_usuario',
        'id_produto',
        'quantidade',
        'empresa_id',
    ];

    protected $casts = [
        'quantidade' => 'integer',
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

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'id_produto');
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

    // Acessors
    public function getSubtotalAttribute()
    {
        return $this->produto ? $this->produto->preco * $this->quantidade : 0;
    }

    public function getSubtotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->subtotal, 2, ',', '.');
    }
}
