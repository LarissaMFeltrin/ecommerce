@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral do sistema')

@section('content')
<div class="row">
    <!-- Estatísticas Gerais -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-number">{{ $stats['total_produtos'] }}</div>
                <div class="stats-label">Total de Produtos</div>
                <i class="bi bi-box-seam fs-1 mt-2 opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-number">{{ $stats['total_usuarios'] }}</div>
                <div class="stats-label">Total de Usuários</div>
                <i class="bi bi-people fs-1 mt-2 opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-number">{{ $stats['total_pedidos'] }}</div>
                <div class="stats-label">Total de Pedidos</div>
                <i class="bi bi-cart-check fs-1 mt-2 opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body text-center">
                <div class="stats-number">R$ {{ number_format($stats['total_vendas'], 2, ',', '.') }}</div>
                <div class="stats-label">Total de Vendas</div>
                <i class="bi bi-currency-dollar fs-1 mt-2 opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Status dos Pedidos -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="bi bi-cart-check me-2"></i>Status dos Pedidos</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Pendentes</span>
                    <span class="badge bg-warning text-dark">{{ $stats['pedidos_pendentes'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Aprovados</span>
                    <span class="badge bg-success">{{ $stats['pedidos_aprovados'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>Cancelados</span>
                    <span class="badge bg-danger">{{ $stats['pedidos_cancelados'] }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Avaliações Pendentes</span>
                    <span class="badge bg-info">{{ $stats['avaliacoes_pendentes'] }}</span>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Produtos Mais Vendidos -->
    <div class="col-xl-8 col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="bi bi-trophy me-2"></i>Top 5 Produtos Mais Vendidos</h6>
            </div>
            <div class="card-body">
                @if($produtosMaisVendidos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade Vendida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($produtosMaisVendidos as $produto)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.produtos.edit', $produto->id) }}" class="text-decoration-none">
                                            {{ $produto->nome }}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary">{{ $produto->total_vendido }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Nenhum produto vendido ainda.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Gráfico de Vendas -->
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Vendas dos Últimos 6 Meses</h6>
            </div>
            <div class="card-body">
                <canvas id="vendasChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Pedidos Recentes -->
    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Pedidos Recentes</h6>
            </div>
            <div class="card-body">
                @if($pedidosRecentes->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($pedidosRecentes->take(5) as $pedido)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Pedido #{{ $pedido->id }}</h6>
                                    <small class="text-muted">{{ $pedido->usuario->nome }}</small>
                                    <br>
                                    <small class="text-muted">{{ $pedido->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $pedido->status === 'pendente' ? 'warning' : ($pedido->status === 'aprovado' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($pedido->status) }}
                                    </span>
                                    <br>
                                    <small class="text-muted">R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-outline-warning btn-sm">
                            Ver Todos os Pedidos
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center mb-0">Nenhum pedido encontrado.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Ações Rápidas -->
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h6 class="mb-0"><i class="bi bi-lightning me-2"></i>Ações Rápidas</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>Novo Produto
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.categorias.index') }}" class="btn btn-success w-100">
                            <i class="bi bi-tags me-2"></i>Gerenciar Categorias
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.pedidos.index') }}" class="btn btn-warning w-100">
                            <i class="bi bi-cart-check me-2"></i>Ver Pedidos
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.relatorios.index') }}" class="btn btn-info w-100">
                            <i class="bi bi-graph-up me-2"></i>Relatórios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de vendas
    const ctx = document.getElementById('vendasChart').getContext('2d');
    
    const vendasData = @json($vendasPorMes);
    const labels = vendasData.map(item => {
        const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        return `${meses[item.mes - 1]}/${item.ano}`;
    });
    const valores = vendasData.map(item => item.total);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Vendas (R$)',
                data: valores,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'R$ ' + value.toLocaleString('pt-BR');
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
