<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';

    protected $fillable = [
        'id_pedido',
        'tipo',
        'status',
        'valor',
        'referencia_externa',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'criado_em' => 'datetime',
    ];

    // Configurar timestamps personalizados
    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null; // Não temos updated_at na tabela

    // Relacionamentos
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'id_pedido');
    }

    // Scopes
    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Acessors
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getStatusFormatadoAttribute()
    {
        $status = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'rejeitado' => 'Rejeitado',
            'cancelado' => 'Cancelado',
            'processando' => 'Processando',
        ];

        return $status[$this->status] ?? $this->status;
    }
}