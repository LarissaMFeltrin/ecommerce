<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Endereco extends Model
{
    use HasFactory;

    protected $table = 'enderecos';

    protected $fillable = [
        'id_usuario',
        'cep',
        'rua',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'principal',
    ];

    protected $casts = [
        'principal' => 'boolean',
        'criado_em' => 'datetime',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_endereco');
    }

    // Scopes
    public function scopePrincipal($query)
    {
        return $query->where('principal', true);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('id_usuario', $usuarioId);
    }

    // Acessors
    public function getEnderecoCompletoAttribute()
    {
        $endereco = $this->rua . ', ' . $this->numero;

        if ($this->complemento) {
            $endereco .= ' - ' . $this->complemento;
        }

        $endereco .= ', ' . $this->bairro . ', ' . $this->cidade . ' - ' . $this->estado . ', CEP: ' . $this->cep;

        return $endereco;
    }
}
