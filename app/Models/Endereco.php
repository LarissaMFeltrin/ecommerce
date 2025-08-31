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
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
        'pais',
        'tipo',
        'empresa_id',
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

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_endereco');
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

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    // Acessors
    public function getEnderecoCompletoAttribute()
    {
        $endereco = $this->logradouro;
        if ($this->numero) {
            $endereco .= ', ' . $this->numero;
        }
        if ($this->complemento) {
            $endereco .= ' - ' . $this->complemento;
        }
        $endereco .= ', ' . $this->bairro . ', ' . $this->cidade . ' - ' . $this->estado . ', ' . $this->cep;

        return $endereco;
    }

    public function getEnderecoResumidoAttribute()
    {
        return $this->logradouro . ', ' . $this->numero . ', ' . $this->bairro . ', ' . $this->cidade . ' - ' . $this->estado;
    }
}
