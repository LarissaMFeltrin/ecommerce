<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupom extends Model
{
    use HasFactory;

    protected $table = 'cupons';

    protected $fillable = [
        'codigo',
        'tipo',
        'valor',
        'percentual',
        'valor_minimo',
        'data_inicio',
        'data_fim',
        'maximo_usos',
        'usos_atuais',
        'ativo',
        'empresa_id',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'percentual' => 'decimal:2',
        'valor_minimo' => 'decimal:2',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'maximo_usos' => 'integer',
        'usos_atuais' => 'integer',
        'ativo' => 'boolean',
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'id_cupom');
    }

    // Scopes
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeValido($query)
    {
        $now = now()->toDateString();
        return $query->where('ativo', true)
            ->where('data_inicio', '<=', $now)
            ->where('data_fim', '>=', $now)
            ->where(function ($q) {
                $q->whereNull('maximo_usos')
                    ->orWhere('usos_atuais', '<', 'maximo_usos');
            });
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorCodigo($query, $codigo)
    {
        return $query->where('codigo', $codigo);
    }

    // Acessors
    public function getValorDescontoAttribute()
    {
        if ($this->tipo === 'percentual') {
            return $this->percentual;
        }
        return $this->valor;
    }

    public function getTipoFormatadoAttribute()
    {
        return $this->tipo === 'percentual' ? 'Percentual' : 'Valor Fixo';
    }

    public function getValorMinimoFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_minimo, 2, ',', '.');
    }

    public function getValorFormatadoAttribute()
    {
        if ($this->tipo === 'percentual') {
            return $this->percentual . '%';
        }
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getDisponivelAttribute()
    {
        if (!$this->ativo) return false;

        $now = now()->toDateString();
        if ($now < $this->data_inicio || $now > $this->data_fim) return false;

        if ($this->maximo_usos && $this->usos_atuais >= $this->maximo_usos) return false;

        return true;
    }

    // Métodos
    public function podeUsar($valorPedido = 0)
    {
        if (!$this->disponivel) return false;

        if ($this->valor_minimo && $valorPedido < $this->valor_minimo) return false;

        return true;
    }

    public function usar()
    {
        if ($this->maximo_usos) {
            $this->increment('usos_atuais');
        }
    }
}
