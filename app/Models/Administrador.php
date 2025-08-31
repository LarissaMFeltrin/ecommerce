<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    use HasFactory;

    protected $table = 'administradores';

    // Desabilitar timestamps
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'tipo',
        'empresa_id',
        'ativo',
    ];

    protected $hidden = [
        'senha',
    ];

    // Relacionamentos
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_administrador');
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

    public function scopeSuperAdmin($query)
    {
        return $query->where('tipo', 'super_admin');
    }

    // Mutator para NÃO criptografar a senha (armazenar em texto plano)
    public function setSenhaAttribute($value)
    {
        $this->attributes['senha'] = $value; // Sem Hash::make()
    }

    /**
     * Verificar se é super administrador
     */
    public function isSuperAdmin()
    {
        return $this->tipo === 'super_admin';
    }

    /**
     * Verificar se é administrador de empresa
     */
    public function isEmpresaAdmin()
    {
        return $this->empresa_id !== null;
    }
}
