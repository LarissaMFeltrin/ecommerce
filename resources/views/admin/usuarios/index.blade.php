@extends('layouts.admin')

@section('title', 'Usuários')
@section('page-title', 'Gerenciar Usuários')
@section('page-subtitle', 'Lista de todos os usuários cadastrados')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.usuarios.index') }}" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou email..." value="{{ request('busca') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Todos os status</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
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
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-1">{{ $usuarios->total() }}</h4>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-1">{{ $usuarios->where('ativo', true)->count() }}</h4>
                        <small class="text-muted">Ativos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="bi bi-people me-2"></i>
            Usuários ({{ $usuarios->total() }})
        </h6>
    </div>
    <div class="card-body p-0">
        @if($usuarios->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Usuário</th>
                            <th>Contato</th>
                            <th class="text-center">Pedidos</th>
                            <th class="text-center">Avaliações</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white" 
                                         style="width: 50px; height: 50px;">
                                        <span class="fw-bold">{{ strtoupper(substr($usuario->nome, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $usuario->nome }}</h6>
                                        <small class="text-muted">
                                            Cadastrado em {{ $usuario->created_at->format('d/m/Y') }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="mb-1">
                                        <i class="bi bi-envelope me-2 text-muted"></i>
                                        <a href="mailto:{{ $usuario->email }}" class="text-decoration-none">
                                            {{ $usuario->email }}
                                        </a>
                                    </div>
                                    @if($usuario->telefone)
                                        <div>
                                            <i class="bi bi-telephone me-2 text-muted"></i>
                                            <a href="tel:{{ $usuario->telefone }}" class="text-decoration-none">
                                                {{ $usuario->telefone }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                @if($usuario->pedidos_count > 0)
                                    <span class="badge bg-info">{{ $usuario->pedidos_count }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($usuario->avaliacoes_count > 0)
                                    <span class="badge bg-warning text-dark">{{ $usuario->avaliacoes_count }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($usuario->ativo)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-secondary">Inativo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.usuarios.show', $usuario) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <form method="POST" 
                                          action="{{ route('admin.usuarios.toggle', $usuario) }}" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $usuario->ativo ? 'btn-outline-warning' : 'btn-outline-success' }}" 
                                                title="{{ $usuario->ativo ? 'Desativar' : 'Ativar' }} usuário">
                                            <i class="bi {{ $usuario->ativo ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                        </button>
                                    </form>
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
                        Mostrando {{ $usuarios->firstItem() }} a {{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuários
                    </div>
                    <div>
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h5 class="text-muted mt-3">Nenhum usuário encontrado</h5>
                <p class="text-muted">Não há usuários cadastrados no sistema.</p>
            </div>
        @endif
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-people-fill text-primary display-4"></i>
                <h4 class="text-primary mt-2">{{ $usuarios->where('ativo', true)->count() }}</h4>
                <p class="text-muted mb-0">Usuários Ativos</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-person-x text-secondary display-4"></i>
                <h4 class="text-secondary mt-2">{{ $usuarios->where('ativo', false)->count() }}</h4>
                <p class="text-muted mb-0">Usuários Inativos</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-calendar-check text-success display-4"></i>
                <h4 class="text-success mt-2">{{ $usuarios->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                <p class="text-muted mb-0">Novos Este Mês</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-envelope text-info display-4"></i>
                <h4 class="text-info mt-2">{{ $usuarios->where('email_verificado_em', null)->count() }}</h4>
                <p class="text-muted mb-0">Emails Não Verificados</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-submit do formulário de filtro quando mudar status
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.querySelector('select[name="status"]');
    
    statusSelect.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>
@endpush
