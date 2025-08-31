@extends('layouts.admin')

@section('title', 'Administradores')

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

        /* Suporte para ícones Bootstrap */
        .btn-actions .bi {
            font-size: 0.9rem;
        }

        /* Fallback para ícones caso não carreguem */
        .btn-actions .bi.bi-eye::before {
            content: "👁";
        }

        .btn-actions .bi.bi-pencil::before {
            content: "✏";
        }

        .btn-actions .bi.bi-person-x::before {
            content: "🚫";
        }
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-shield-check me-2"></i>
                        Administradores do Sistema
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0">
                        Gerencie os administradores que têm acesso ao painel administrativo.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('admin.administradores.create') }}" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-plus-circle me-2"></i>Novo Administrador
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-people me-2"></i>
                Lista de Administradores ({{ $administradores->total() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if ($administradores->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Administrador</th>
                                <th>Email</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Data Criação</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($administradores as $admin)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white"
                                                style="width: 40px; height: 40px;">
                                                <span class="fw-bold">{{ strtoupper(substr($admin->nome, 0, 1)) }}</span>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $admin->nome }}</h6>
                                                <small class="text-muted">ID: {{ $admin->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $admin->email }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($admin->tipo === 'super_admin')
                                            <span class="badge bg-danger">Super Admin</span>
                                        @else
                                            <span class="badge bg-primary">Admin</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($admin->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div>
                                            <div class="fw-bold">
                                                {{ $admin->created_at ? $admin->created_at->format('d/m/Y') : 'N/A' }}</div>
                                            <small
                                                class="text-muted">{{ $admin->created_at ? $admin->created_at->format('H:i') : '' }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-sm btn-outline-info btn-actions"
                                                title="Ver detalhes">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-warning btn-actions"
                                                title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            @if ($admin->tipo !== 'super_admin')
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-actions"
                                                    title="Desativar">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            @endif
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
                            Mostrando {{ $administradores->firstItem() }} a {{ $administradores->lastItem() }} de
                            {{ $administradores->total() }} administradores
                        </div>
                        <div>
                            {{ $administradores->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-shield-check display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Nenhum administrador encontrado</h5>
                    <p class="text-muted">Comece criando o primeiro administrador do sistema!</p>
                    <a href="{{ route('admin.administradores.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Criar Administrador
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-shield-check text-primary display-6"></i>
                    <h4 class="text-primary mt-2">{{ $administradores->total() }}</h4>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-shield-star text-danger display-6"></i>
                    <h4 class="text-danger mt-2">{{ $administradores->where('tipo', 'super_admin')->count() }}</h4>
                    <p class="text-muted mb-0">Super Admins</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-shield text-primary display-6"></i>
                    <h4 class="text-primary mt-2">{{ $administradores->where('tipo', 'admin')->count() }}</h4>
                    <p class="text-muted mb-0">Admins</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle text-success display-6"></i>
                    <h4 class="text-success mt-2">{{ $administradores->where('ativo', true)->count() }}</h4>
                    <p class="text-muted mb-0">Ativos</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Verificar se os ícones Bootstrap estão funcionando
            const icons = document.querySelectorAll('.bi');
            let biWorking = false;

            // Testar se Bootstrap Icons está funcionando
            if (icons.length > 0) {
                const testIcon = icons[0];
                const computedStyle = window.getComputedStyle(testIcon, '::before');
                biWorking = computedStyle.content !== 'none' && computedStyle.content !== '';
            }

            if (!biWorking) {
                console.log('Bootstrap Icons não está funcionando, aplicando fallback');
                // Aplicar fallback visual
                icons.forEach(icon => {
                    const iconClass = Array.from(icon.classList).find(cls => cls.startsWith('bi-'));
                    if (iconClass) {
                        icon.style.fontFamily = 'monospace';
                        icon.style.fontSize = '1.2em';

                        // Mapear classes para emojis
                        const fallbackMap = {
                            'bi-eye': '👁',
                            'bi-pencil': '✏',
                            'bi-person-x': '🚫',
                            'bi-shield-check': '🛡️',
                            'bi-people': '👥',
                            'bi-plus-circle': '➕'
                        };

                        if (fallbackMap[iconClass]) {
                            icon.textContent = fallbackMap[iconClass];
                        }
                    }
                });
            }
        });
    </script>
@endpush
