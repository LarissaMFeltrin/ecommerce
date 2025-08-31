@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </h1>
            <div class="text-muted">
                <small>Última atualização: {{ now()->format('d/m/Y H:i') }}</small>
            </div>
        </div>

        <!-- Cards de Estatísticas -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Produtos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_produtos'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-box fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total de Usuários
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_usuarios'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total de Pedidos</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_pedidos'] }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total de Vendas</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R$
                                    {{ number_format($stats['total_vendas'] / 100, 2, ',', '.') }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produtos Mais Vendidos -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-chart-bar mr-2"></i>Produtos Mais Vendidos
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($produtosMaisVendidos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th>Quantidade Vendida</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($produtosMaisVendidos as $produto)
                                            <tr>
                                                <td>{{ $produto->nome }}</td>
                                                <td>
                                                    <span class="badge badge-primary">{{ $produto->total_vendido }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                <p>Nenhum produto vendido ainda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pedidos Recentes -->
            <div class="col-lg-6">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-clock mr-2"></i>Pedidos Recentes
                        </h6>
                    </div>
                    <div class="card-body">
                        @if ($pedidosRecentes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Pedido</th>
                                            <th>Cliente</th>
                                            <th>Valor</th>
                                            <th>Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($pedidosRecentes as $pedido)
                                            <tr>
                                                <td>#{{ $pedido->id }}</td>
                                                <td>{{ $pedido->usuario->nome ?? 'N/A' }}</td>
                                                <td>R$ {{ number_format($pedido->valor_total / 100, 2, ',', '.') }}</td>
                                                <td>{{ $pedido->criado_em->format('d/m/Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-clock fa-3x mb-3"></i>
                                <p>Nenhum pedido realizado ainda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendas por Mês -->
        @if ($vendasPorMes->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-chart-line mr-2"></i>Vendas por Mês (Últimos 6 meses)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Mês/Ano</th>
                                            <th>Total de Vendas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vendasPorMes as $venda)
                                            <tr>
                                                <td>
                                                    @php
                                                        $mes = \Carbon\Carbon::createFromDate(
                                                            null,
                                                            $venda->mes,
                                                            1,
                                                        )->format('F/Y');
                                                    @endphp
                                                    {{ $mes }}
                                                </td>
                                                <td>R$ {{ number_format($venda->total / 100, 2, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-refresh do dashboard a cada 5 minutos
            setTimeout(function() {
                location.reload();
            }, 300000); // 5 minutos
        });
    </script>
@endpush
