@extends('layouts.admin')

@section('title', 'Pedidos')
@section('page-title', 'Gerenciar Pedidos')
@section('page-subtitle', 'Lista de todos os pedidos do sistema')

@section('content')
    <div class="row mb-4">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.pedidos.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="busca" class="form-control"
                                placeholder="Buscar por ID ou cliente..." value="{{ request('busca') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="status" class="form-select">
                                <option value="">Todos os status</option>
                                <option value="pendente" {{ request('status') === 'pendente' ? 'selected' : '' }}>Pendente
                                </option>
                                <option value="aprovado" {{ request('status') === 'aprovado' ? 'selected' : '' }}>Aprovado
                                </option>
                                <option value="em_preparo" {{ request('status') === 'em_preparo' ? 'selected' : '' }}>Em
                                    Preparo</option>
                                <option value="enviado" {{ request('status') === 'enviado' ? 'selected' : '' }}>Enviado
                                </option>
                                <option value="entregue" {{ request('status') === 'entregue' ? 'selected' : '' }}>Entregue
                                </option>
                                <option value="cancelado" {{ request('status') === 'cancelado' ? 'selected' : '' }}>
                                    Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="data_inicio" class="form-control" placeholder="Data início"
                                value="{{ request('data_inicio') }}">
                        </div>
                        <div class="col-md-2">
                            <input type="date" name="data_fim" class="form-control" placeholder="Data fim"
                                value="{{ request('data_fim') }}">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card">
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <h5 class="text-primary mb-1">{{ $pedidos->total() }}</h5>
                            <small class="text-muted">Total</small>
                        </div>
                        <div class="col-12">
                            <h5 class="text-warning mb-1">{{ $pedidos->where('status', 'pendente')->count() }}</h5>
                            <small class="text-muted">Pendentes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-cart-check me-2"></i>
                Pedidos ({{ $pedidos->total() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if ($pedidos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th class="text-center">Itens</th>
                                <th class="text-center">Valor</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Data</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pedidos as $pedido)
                                <tr>
                                    <td>
                                        <div>
                                            <h6 class="mb-1">#{{ $pedido->id }}</h6>
                                            @if ($pedido->observacao)
                                                <small class="text-muted">{{ Str::limit($pedido->observacao, 30) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-white"
                                                style="width: 35px; height: 35px;">
                                                <span
                                                    class="fw-bold small">{{ strtoupper(substr($pedido->usuario->nome, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $pedido->usuario->nome }}</div>
                                                <small class="text-muted">{{ $pedido->usuario->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $pedido->itens->count() }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">R$
                                            {{ number_format($pedido->valor_total, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @switch($pedido->status)
                                            @case('pendente')
                                                <span class="badge bg-warning text-dark">Pendente</span>
                                            @break

                                            @case('aprovado')
                                                <span class="badge bg-success">Aprovado</span>
                                            @break

                                            @case('em_preparo')
                                                <span class="badge bg-info">Em Preparo</span>
                                            @break

                                            @case('enviado')
                                                <span class="badge bg-primary">Enviado</span>
                                            @break

                                            @case('entregue')
                                                <span class="badge bg-success">Entregue</span>
                                            @break

                                            @case('cancelado')
                                                <span class="badge bg-danger">Cancelado</span>
                                            @break

                                            @default
                                                <span class="badge bg-secondary">{{ ucfirst($pedido->status) }}</span>
                                        @endswitch
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <div class="fw-bold">{{ $pedido->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $pedido->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.pedidos.show', $pedido) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver detalhes">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <button type="button" class="btn btn-sm btn-outline-warning"
                                                title="Atualizar status"
                                                onclick="abrirModalStatus({{ $pedido->id }}, '{{ $pedido->status }}', '{{ $pedido->observacao }}')">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Mostrando {{ $pedidos->firstItem() }} a {{ $pedidos->lastItem() }} de {{ $pedidos->total() }}
                            pedidos
                        </div>
                        <div>
                            {{ $pedidos->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-check display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Nenhum pedido encontrado</h5>
                    <p class="text-muted">Não há pedidos no sistema.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mt-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-clock text-warning display-6"></i>
                    <h5 class="text-warning mt-2">{{ $pedidos->where('status', 'pendente')->count() }}</h5>
                    <small class="text-muted">Pendentes</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle text-success display-6"></i>
                    <h5 class="text-success mt-2">{{ $pedidos->where('status', 'aprovado')->count() }}</h5>
                    <small class="text-muted">Aprovados</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-gear text-info display-6"></i>
                    <h5 class="text-info mt-2">{{ $pedidos->where('status', 'em_preparo')->count() }}</h5>
                    <small class="text-muted">Em Preparo</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-truck text-primary display-6"></i>
                    <h5 class="text-primary mt-2">{{ $pedidos->where('status', 'enviado')->count() }}</h5>
                    <small class="text-muted">Enviados</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-box-seam text-success display-6"></i>
                    <h5 class="text-success mt-2">{{ $pedidos->where('status', 'entregue')->count() }}</h5>
                    <small class="text-muted">Entregues</small>
                </div>
            </div>
        </div>

        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-x-circle text-danger display-6"></i>
                    <h5 class="text-danger mt-2">{{ $pedidos->where('status', 'cancelado')->count() }}</h5>
                    <small class="text-muted">Cancelados</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Atualizar Status -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atualizar Status do Pedido</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Novo Status *</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="pendente">Pendente</option>
                                <option value="aprovado">Aprovado</option>
                                <option value="em_preparo">Em Preparo</option>
                                <option value="enviado">Enviado</option>
                                <option value="entregue">Entregue</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="observacao" class="form-label">Observação</label>
                            <textarea class="form-control" id="observacao" name="observacao" rows="3"
                                placeholder="Adicione uma observação sobre o status..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Atualizar Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function abrirModalStatus(pedidoId, statusAtual, observacao) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            const statusSelect = document.getElementById('status');
            const observacaoTextarea = document.getElementById('observacao');

            // Configurar o formulário
            form.action = `/admin/pedidos/${pedidoId}/status`;

            // Selecionar o status atual
            statusSelect.value = statusAtual;

            // Preencher observação atual
            observacaoTextarea.value = observacao || '';

            // Abrir modal
            new bootstrap.Modal(modal).show();
        }

        // Auto-submit do formulário de filtro quando mudar status
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelect = document.querySelector('select[name="status"]');

            statusSelect.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    </script>
@endpush
