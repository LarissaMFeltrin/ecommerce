@extends('layouts.admin')

@section('title', 'Relatórios')
@section('page-title', 'Relatórios e Estatísticas')
@section('page-subtitle', 'Análise detalhada do desempenho da loja')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-calendar-range me-2"></i>
                        Período de Análise
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.relatorios.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <select name="periodo" class="form-select">
                                <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Última Semana</option>
                                <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>Último Mês</option>
                                <option value="ano" {{ $periodo === 'ano' ? 'selected' : '' }}>Último Ano</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Atualizar Relatório
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="exportarRelatorio()">
                                <i class="bi bi-download me-2"></i>Exportar PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-success mb-1">R$ {{ number_format($vendas, 2, ',', '.') }}</h4>
                                <small class="text-muted">Vendas</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-primary mb-1">{{ $pedidos }}</h4>
                            <small class="text-muted">Pedidos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Geral -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-currency-dollar text-success display-6"></i>
                    <h4 class="text-success mt-2">R$ {{ number_format($vendas, 2, ',', '.') }}</h4>
                    <p class="text-muted mb-0">Total de Vendas</p>
                    <small class="text-success">
                        <i class="bi bi-arrow-up"></i>
                        {{ $periodo === 'mes' ? 'Este mês' : ($periodo === 'semana' ? 'Esta semana' : 'Este ano') }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-cart-check text-primary display-6"></i>
                    <h4 class="text-primary mt-2">{{ $pedidos }}</h4>
                    <p class="text-muted mb-0">Total de Pedidos</p>
                    <small class="text-primary">
                        <i class="bi bi-arrow-up"></i>
                        {{ $periodo === 'mes' ? 'Este mês' : ($periodo === 'semana' ? 'Esta semana' : 'Este ano') }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-box-seam text-info display-6"></i>
                    <h4 class="text-info mt-2">{{ $produtosVendidos }}</h4>
                    <p class="text-muted mb-0">Produtos Vendidos</p>
                    <small class="text-info">
                        <i class="bi bi-arrow-up"></i>
                        {{ $periodo === 'mes' ? 'Este mês' : ($periodo === 'semana' ? 'Esta semana' : 'Este ano') }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-graph-up text-warning display-6"></i>
                    <h4 class="text-warning mt-2">R$
                        {{ $pedidos > 0 ? number_format($vendas / $pedidos, 2, ',', '.') : '0,00' }}</h4>
                    <p class="text-muted mb-0">Ticket Médio</p>
                    <small class="text-warning">
                        <i class="bi bi-info-circle"></i> Por pedido
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>
                        Evolução das Vendas
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="vendasChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-pie-chart me-2"></i>
                        Distribuição por Status
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabelas de Dados -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-trophy me-2"></i>
                        Top 10 Produtos Mais Vendidos
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-center">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $topProdutos = \App\Models\ItemPedido::join(
                                        'produtos',
                                        'itens_pedido.id_produto',
                                        '=',
                                        'produtos.id',
                                    )
                                        ->join('pedidos', 'itens_pedido.id_pedido', '=', 'pedidos.id')
                                        ->where('pedidos.status', 'aprovado')
                                        ->select(
                                            'produtos.nome',
                                            'produtos.id',
                                            \DB::raw('SUM(itens_pedido.quantidade) as total_vendido'),
                                            \DB::raw(
                                                'SUM(itens_pedido.quantidade * itens_pedido.preco_unitario) as valor_total',
                                            ),
                                        )
                                        ->groupBy('produtos.id', 'produtos.nome')
                                        ->orderBy('total_vendido', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp

                                @forelse($topProdutos as $index => $produto)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                                {{ $produto->nome }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $produto->total_vendido }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-success">R$
                                                {{ number_format($produto->valor_total, 2, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Nenhum produto vendido ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-people me-2"></i>
                        Top 10 Clientes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th class="text-center">Pedidos</th>
                                    <th class="text-center">Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $topClientes = \App\Models\Pedido::join(
                                        'usuarios',
                                        'pedidos.id_usuario',
                                        '=',
                                        'usuarios.id',
                                    )
                                        ->where('pedidos.status', 'aprovado')
                                        ->select(
                                            'usuarios.nome',
                                            'usuarios.id',
                                            \DB::raw('COUNT(pedidos.id) as total_pedidos'),
                                            \DB::raw('SUM(pedidos.valor_total) as valor_total'),
                                        )
                                        ->groupBy('usuarios.id', 'usuarios.nome')
                                        ->orderBy('valor_total', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp

                                @forelse($topClientes as $index => $cliente)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-success me-2">{{ $index + 1 }}</span>
                                                {{ $cliente->nome }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ $cliente->total_pedidos }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-success">R$
                                                {{ number_format($cliente->valor_total, 2, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Nenhum cliente com pedidos
                                            ainda.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Análise de Tendências -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Análise e Recomendações
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Pontos Positivos</h6>
                            <ul class="list-unstyled">
                                @if ($vendas > 0)
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Vendas ativas com {{ $pedidos }} pedidos processados
                                    </li>
                                @endif
                                @if ($produtosVendidos > 0)
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        {{ $produtosVendidos }} produtos vendidos no período
                                    </li>
                                @endif
                                @if ($pedidos > 0 && $vendas / $pedidos > 50)
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle text-success me-2"></i>
                                        Ticket médio alto: R$ {{ number_format($vendas / $pedidos, 2, ',', '.') }}
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-warning">Oportunidades de Melhoria</h6>
                            <ul class="list-unstyled">
                                @if ($pedidos == 0)
                                    <li class="mb-2">
                                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                        Nenhum pedido no período - considere promoções
                                    </li>
                                @endif
                                @if ($pedidos > 0 && $vendas / $pedidos < 30)
                                    <li class="mb-2">
                                        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                                        Ticket médio baixo - incentive compras maiores
                                    </li>
                                @endif
                                <li class="mb-2">
                                    <i class="bi bi-lightbulb text-info me-2"></i>
                                    Analise produtos mais vendidos para estoque
                                </li>
                            </ul>
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
            const vendasCtx = document.getElementById('vendasChart').getContext('2d');

            // Dados simulados para o gráfico (substitua pelos dados reais)
            const vendasData = {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun'],
                datasets: [{
                    label: 'Vendas (R$)',
                    data: [1200, 1900, 3000, 5000, 2000, 3000],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            };

            new Chart(vendasCtx, {
                type: 'line',
                data: vendasData,
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

            // Gráfico de status dos pedidos
            const statusCtx = document.getElementById('statusChart').getContext('2d');

            const statusData = {
                labels: ['Pendentes', 'Aprovados', 'Enviados', 'Entregues', 'Cancelados'],
                datasets: [{
                    data: [12, 19, 8, 15, 3],
                    backgroundColor: [
                        '#ffc107',
                        '#28a745',
                        '#17a2b8',
                        '#20c997',
                        '#dc3545'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            new Chart(statusCtx, {
                type: 'doughnut',
                data: statusData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });

        function exportarRelatorio() {
            // Implementar exportação para PDF
            alert('Funcionalidade de exportação será implementada em breve!');
        }
    </script>
@endpush
