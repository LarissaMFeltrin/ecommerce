<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'telefone',
        'cpf',
        'data_nascimento',
        'ativo',
        'empresa_id',
    ];

    protected $hidden = [
        'senha',
        'remember_token',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'ativo' => 'boolean',
        'email_verificado_em' => 'datetime',
    ];

    // Mutator para NÃO criptografar a senha (armazenar em texto plano)
    public function setSenhaAttribute($value)
    {
        $this->attributes['senha'] = $value; // Sem Hash::make()
    }

    public function getAuthPassword()
    {
        return $this->senha;
    }

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function carrinho()
    {
        return $this->hasMany(Carrinho::class, 'id_usuario');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_usuario');
    }

    public function enderecos()
    {
        return $this->hasMany(Endereco::class, 'id_usuario');
    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class, 'id_usuario');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function getNomeCompletoAttribute()
    {
        return $this->nome;
    }

    /**
     * Verificar se o usuário é administrador
     */
    public function isAdmin()
    {
        // Verificar se existe na tabela de administradores
        return \App\Models\Administrador::where('id_usuario', $this->id)
            ->where('ativo', true)
            ->exists();
    }

    /**
     * Verificar se o usuário é administrador de uma empresa específica
     */
    public function isAdminEmpresa($empresaId = null)
    {
        $query = \App\Models\Administrador::where('id_usuario', $this->id)
            ->where('ativo', true);

        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        return $query->exists();
    }

    /**
     * Obter empresa do usuário
     */
    public function getEmpresa()
    {
        return $this->empresa;
    }
}
