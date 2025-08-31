@extends('layouts.admin')

@section('title', 'Categorias')
@section('page-title', 'Gerenciar Categorias')
@section('page-subtitle', 'Organize seus produtos em categorias')

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
    </style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Nova Categoria
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.categorias.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="nome"
                                    class="form-control @error('nome') is-invalid @enderror" placeholder="Nome da categoria"
                                    value="{{ old('nome') }}" required>
                                @error('nome')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="descricao" class="form-control"
                                    placeholder="Descrição (opcional)" value="{{ old('descricao') }}">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ativa" value="1"
                                        {{ old('ativa', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativa">
                                        Ativa
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100" title="Adicionar Categoria">
                                    <i class="bi bi-plus-lg me-1"></i>Adicionar
                                </button>
                            </div>
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
                                <h4 class="text-primary mb-1">{{ $categorias->total() }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success mb-1">{{ $categorias->where('ativa', true)->count() }}</h4>
                            <small class="text-muted">Ativas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-tags me-2"></i>
                Categorias ({{ $categorias->total() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if ($categorias->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th class="text-center">Produtos</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categorias as $categoria)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-info rounded-circle me-3 d-flex align-items-center justify-content-center text-white"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-tag"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $categoria->nome }}</h6>
                                                <small class="text-muted">Slug: {{ $categoria->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($categoria->descricao)
                                            <span>{{ Str::limit($categoria->descricao, 50) }}</span>
                                        @else
                                            <span class="text-muted">Sem descrição</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($categoria->produtos_count > 0)
                                            <span class="badge bg-primary">{{ $categoria->produtos_count }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($categoria->ativa)
                                            <span class="badge bg-success">Ativa</span>
                                        @else
                                            <span class="badge bg-secondary">Inativa</span>
                                        @endif
                                        <br>
                                        <small>
                                            <button type="button"
                                                class="btn btn-sm {{ $categoria->ativa ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                                onclick="toggleStatus({{ $categoria->id }}, {{ $categoria->ativa ? 'false' : 'true' }})"
                                                title="{{ $categoria->ativa ? 'Desativar categoria' : 'Ativar categoria' }}">
                                                <i
                                                    class="bi bi-{{ $categoria->ativa ? 'pause-circle' : 'play-circle' }}"></i>
                                                {{ $categoria->ativa ? 'Desativar' : 'Ativar' }}
                                            </button>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <button type="button" class="btn btn-outline-warning btn-sm btn-actions"
                                                title="Editar Categoria"
                                                onclick="editarCategoria({{ $categoria->id }}, '{{ $categoria->nome }}', '{{ $categoria->descricao }}', {{ $categoria->ativa ? 'true' : 'false' }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            @if ($categoria->produtos_count == 0)
                                                <form method="POST"
                                                    action="{{ route('admin.categorias.destroy', $categoria) }}"
                                                    class="d-inline"
                                                    data-confirm="Tem certeza que deseja excluir esta categoria?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm btn-actions"
                                                        title="Excluir Categoria">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary btn-sm btn-actions"
                                                    disabled
                                                    title="Não pode excluir (possui {{ $categoria->produtos_count }} produto(s))">
                                                    <i class="bi bi-lock"></i>
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
                            Mostrando {{ $categorias->firstItem() }} a {{ $categorias->lastItem() }} de
                            {{ $categorias->total() }} categorias
                        </div>
                        <div>
                            {{ $categorias->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-tags display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Nenhuma categoria encontrada</h5>
                    <p class="text-muted">Comece criando sua primeira categoria!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal para Editar Categoria -->
    <div class="modal fade" id="editarCategoriaModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil me-2"></i>Editar Categoria
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editarCategoriaForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nome" class="form-label">Nome da Categoria *</label>
                            <input type="text" class="form-control" id="edit_nome" name="nome" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="edit_descricao" name="descricao" rows="3"
                                placeholder="Descrição opcional da categoria"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_ativa" name="ativa"
                                    value="1">
                                <label class="form-check-label" for="edit_ativa">
                                    Categoria ativa
                                </label>
                                <div class="form-text">Categorias inativas não aparecem no catálogo público.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Estatísticas Rápidas -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-tags text-primary display-6"></i>
                    <h4 class="text-primary mt-2">{{ $categorias->where('ativa', true)->count() }}</h4>
                    <p class="text-muted mb-0">Categorias Ativas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-tag text-secondary display-6"></i>
                    <h4 class="text-secondary mt-2">{{ $categorias->where('ativa', false)->count() }}</h4>
                    <p class="text-muted mb-0">Categorias Inativas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-box-seam text-success display-6"></i>
                    <h4 class="text-success mt-2">{{ $categorias->sum('produtos_count') }}</h4>
                    <p class="text-muted mb-0">Total de Produtos</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-calendar-check text-info display-6"></i>
                    <h4 class="text-info mt-2">
                        {{ $categorias->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                    <p class="text-muted mb-0">Novas Este Mês</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function editarCategoria(id, nome, descricao, ativa) {
            const modal = document.getElementById('editarCategoriaModal');
            const form = document.getElementById('editarCategoriaForm');
            const nomeInput = document.getElementById('edit_nome');
            const descricaoTextarea = document.getElementById('edit_descricao');
            const ativaCheckbox = document.getElementById('edit_ativa');

            // Configurar o formulário
            form.action = `/admin/categorias/${id}`;

            // Preencher os campos
            nomeInput.value = nome;
            descricaoTextarea.value = descricao || '';
            ativaCheckbox.checked = ativa;

            // Abrir modal
            new bootstrap.Modal(modal).show();
        }

        function toggleStatus(id, newStatus) {
            const confirmMessage = newStatus ? 'Tem certeza que deseja ativar esta categoria?' :
                'Tem certeza que deseja desativar esta categoria?';
            if (confirm(confirmMessage)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('admin.categorias.index') }}/${id}/toggle-status`;
                form.style.display = 'none';

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = '{{ csrf_token() }}';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        }

        // Confirm delete actions
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[data-confirm]');
            deleteForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    const message = this.dataset.confirm;
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                });
            });

            // Adicionar feedback visual para o formulário de criação
            const createForm = document.querySelector('form[action*="categorias/store"]');
            if (createForm) {
                createForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Adicionando...';
                });
            }

            // Adicionar feedback visual para o formulário de edição
            const editForm = document.getElementById('editarCategoriaForm');
            if (editForm) {
                editForm.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Salvando...';
                });
            }
        });
    </script>
@endpush
