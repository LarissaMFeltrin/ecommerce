<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';

    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'ativa',
        'empresa_id',
    ];

    protected $casts = [
        'ativa' => 'boolean',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'id_categoria');
    }

    // Scopes
    public function scopeAtiva($query)
    {
        return $query->where('ativa', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
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

    public function setNomeAttribute($value)
    {
        $this->attributes['nome'] = $value;
        // Gerar slug automaticamente quando o nome for definido
        if (empty($this->slug)) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }
}