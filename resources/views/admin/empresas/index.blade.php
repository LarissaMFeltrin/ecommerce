@extends('layouts.admin')

@section('title', 'Gestão de Empresas')

@push('styles')
    <style>
        .btn-actions {
            min-width: 50px;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s ease-in-out;
            padding: 0.25rem 0.5rem;
        }

        .btn-actions:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-actions i {
            font-size: 0.8rem;
            margin-right: 0.2rem;
        }

        .table td {
            vertical-align: middle;
        }

        .gap-1 {
            gap: 0.2rem !important;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
        }

        /* Fallback para ícones caso FontAwesome não carregue */
        .btn-actions i::before {
            content: attr(data-icon);
        }

        .btn-actions i.fa-eye::before {
            content: "👁";
        }

        .btn-actions i.fa-edit::before {
            content: "✏";
        }

        .btn-actions i.fa-pause::before {
            content: "⏸";
        }

        .btn-actions i.fa-play::before {
            content: "▶";
        }

        .btn-actions i.fa-trash::before {
            content: "🗑";
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building mr-2"></i>Gestão de Empresas
            </h1>
            <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>Nova Empresa
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.empresas.index') }}" class="row">
                    <div class="col-md-3">
                        <label for="ramo" class="form-label">Ramo de Atividade</label>
                        <select name="ramo" id="ramo" class="form-control">
                            <option value="">Todos os ramos</option>
                            <option value="perfumes" {{ request('ramo') == 'perfumes' ? 'selected' : '' }}>Perfumes</option>
                            <option value="roupas" {{ request('ramo') == 'roupas' ? 'selected' : '' }}>Roupas</option>
                            <option value="eletronicos" {{ request('ramo') == 'eletronicos' ? 'selected' : '' }}>Eletrônicos
                            </option>
                            <option value="casa" {{ request('ramo') == 'casa' ? 'selected' : '' }}>Casa</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="plano" class="form-label">Plano</label>
                        <select name="plano" id="plano" class="form-control">
                            <option value="">Todos os planos</option>
                            <option value="basico" {{ request('plano') == 'basico' ? 'selected' : '' }}>Básico</option>
                            <option value="profissional" {{ request('plano') == 'profissional' ? 'selected' : '' }}>
                                Profissional</option>
                            <option value="enterprise" {{ request('plano') == 'enterprise' ? 'selected' : '' }}>Enterprise
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativa</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inativa</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-secondary mr-2">
                            <i class="fas fa-search mr-2"></i>Filtrar
                        </button>
                        <a href="{{ route('admin.empresas.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times mr-2"></i>Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Lista de Empresas -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Logo</th>
                                <th>Nome</th>
                                <th>CNPJ</th>
                                <th>Ramo</th>
                                <th>Plano</th>
                                <th>Status</th>
                                <th>Contrato</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($empresas as $empresa)
                                <tr>
                                    <td>{{ $empresa->id }}</td>
                                    <td>
                                        @if ($empresa->logo)
                                            <img src="{{ Storage::url($empresa->logo) }}" alt="Logo"
                                                class="img-thumbnail" style="max-width: 50px;">
                                        @else
                                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <i class="bi bi-building"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $empresa->nome_fantasia ?: $empresa->nome }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $empresa->email }}</small>
                                    </td>
                                    <td>{{ $empresa->cnpj }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ ucfirst($empresa->ramo_atividade) }}</span>
                                    </td>
                                    <td>
                                        @switch($empresa->plano)
                                            @case('basico')
                                                <span class="badge badge-secondary">Básico</span>
                                            @break

                                            @case('profissional')
                                                <span class="badge badge-primary">Profissional</span>
                                            @break

                                            @case('enterprise')
                                                <span class="badge badge-success">Enterprise</span>
                                            @break

                                            @default
                                                <span class="badge badge-light">{{ ucfirst($empresa->plano) }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if ($empresa->ativo)
                                            <span class="badge badge-success">Ativa</span>
                                        @else
                                            <span class="badge badge-danger">Inativa</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Início:</strong>
                                            {{ $empresa->data_contrato ? $empresa->data_contrato->format('d/m/Y') : 'N/A' }}<br>
                                            <strong>Vencimento:</strong>
                                            {{ $empresa->data_vencimento ? $empresa->data_vencimento->format('d/m/Y') : 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1" role="group">
                                            <a href="{{ route('admin.empresas.show', $empresa) }}"
                                                class="btn btn-sm btn-info btn-actions" title="Ver detalhes">
                                                <i class="fas fa-eye" data-icon="👁"></i>
                                            </a>
                                            <a href="{{ route('admin.empresas.edit', $empresa) }}"
                                                class="btn btn-sm btn-warning btn-actions" title="Editar">
                                                <i class="fas fa-edit" data-icon="✏"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.empresas.toggle', $empresa) }}"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-sm {{ $empresa->ativo ? 'btn-warning' : 'btn-success' }} btn-actions"
                                                    title="{{ $empresa->ativo ? 'Desativar' : 'Ativar' }}">
                                                    <i class="fas fa-{{ $empresa->ativo ? 'pause' : 'play' }}"
                                                        data-icon="{{ $empresa->ativo ? '⏸' : '▶' }}"></i>
                                                    {{ $empresa->ativo ? 'Pausar' : 'Ativar' }}
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.empresas.destroy', $empresa) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta empresa?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger btn-actions"
                                                    title="Excluir">
                                                    <i class="fas fa-trash" data-icon="🗑"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-building display-1 mb-3"></i>
                                                <h5>Nenhuma empresa encontrada</h5>
                                                <p>Comece criando sua primeira empresa.</p>
                                                <a href="{{ route('admin.empresas.create') }}" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle me-2"></i>Criar Empresa
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    @if ($empresas->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $empresas->links() }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Empresas
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $empresas->total() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-building fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Empresas Ativas
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $empresas->where('ativo', true)->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Plano Profissional
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $empresas->where('plano', 'profissional')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-star fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Plano Enterprise</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $empresas->where('plano', 'enterprise')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-crown fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            // Filtros automáticos
            document.addEventListener('DOMContentLoaded', function() {
                const filters = ['ramo', 'plano', 'status'];

                filters.forEach(filter => {
                    const element = document.getElementById(filter);
                    if (element) {
                        element.addEventListener('change', function() {
                            this.closest('form').submit();
                        });
                    }
                });
            });
        </script>
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Verificar se os ícones FontAwesome estão funcionando
                    const icons = document.querySelectorAll('.fas');
                    let faWorking = false;

                    // Testar se FontAwesome está funcionando
                    if (icons.length > 0) {
                        const testIcon = icons[0];
                        const computedStyle = window.getComputedStyle(testIcon, '::before');
                        faWorking = computedStyle.content !== 'none' && computedStyle.content !== '';
                    }

                    if (!faWorking) {
                        console.log('FontAwesome não está funcionando, aplicando fallback');
                        // Aplicar fallback visual
                        icons.forEach(icon => {
                            const iconClass = Array.from(icon.classList).find(cls => cls.startsWith('fa-'));
                            if (iconClass) {
                                const fallbackIcon = icon.getAttribute('data-icon');
                                if (fallbackIcon) {
                                    icon.style.fontFamily = 'monospace';
                                    icon.style.fontSize = '1.2em';
                                    icon.textContent = fallbackIcon;
                                }
                            }
                        });
                    }
                });
            </script>
        @endpush
    @endpush
