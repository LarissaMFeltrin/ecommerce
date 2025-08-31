@extends('layouts.admin')

@section('title', 'Categorias')
@section('page-title', 'Gerenciar Categorias')
@section('page-subtitle', 'Organize seus produtos em categorias')

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
                            <div class="col-md-5">
                                <input type="text" name="descricao" class="form-control"
                                    placeholder="Descrição (opcional)" value="{{ old('descricao') }}">
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ativo" value="1"
                                        {{ old('ativo', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ativo">
                                        Ativa
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check"></i>
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
                            <h4 class="text-success mb-1">{{ $categorias->where('ativo', true)->count() }}</h4>
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
                                        @if ($categoria->ativo)
                                            <span class="badge bg-success">Ativa</span>
                                        @else
                                            <span class="badge bg-secondary">Inativa</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-warning" title="Editar"
                                                onclick="editarCategoria({{ $categoria->id }}, '{{ $categoria->nome }}', '{{ $categoria->descricao }}', {{ $categoria->ativo ? 'true' : 'false' }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            @if ($categoria->produtos_count == 0)
                                                <form method="POST"
                                                    action="{{ route('admin.categorias.destroy', $categoria) }}"
                                                    class="d-inline"
                                                    data-confirm="Tem certeza que deseja excluir esta categoria?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    title="Não pode excluir (possui produtos)" disabled>
                                                    <i class="bi bi-trash"></i>
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
                    <h5 class="modal-title">Editar Categoria</h5>
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
                            <textarea class="form-control" id="edit_descricao" name="descricao" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="edit_ativo" name="ativo"
                                    value="1">
                                <label class="form-check-label" for="edit_ativo">
                                    Categoria ativa
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
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
                    <h4 class="text-primary mt-2">{{ $categorias->where('ativo', true)->count() }}</h4>
                    <p class="text-muted mb-0">Categorias Ativas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-tag text-secondary display-6"></i>
                    <h4 class="text-secondary mt-2">{{ $categorias->where('ativo', false)->count() }}</h4>
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
        function editarCategoria(id, nome, descricao, ativo) {
            const modal = document.getElementById('editarCategoriaModal');
            const form = document.getElementById('editarCategoriaForm');
            const nomeInput = document.getElementById('edit_nome');
            const descricaoTextarea = document.getElementById('edit_descricao');
            const ativoCheckbox = document.getElementById('edit_ativo');

            // Configurar o formulário
            form.action = `/admin/categorias/${id}`;

            // Preencher os campos
            nomeInput.value = nome;
            descricaoTextarea.value = descricao || '';
            ativoCheckbox.checked = ativo;

            // Abrir modal
            new bootstrap.Modal(modal).show();
        }

        // Confirm delete actions
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('form[data-confirm]');
            deleteForms.forEach(function(form) {
                form.addEventListener('submit', function(e) {
                    if (!confirm(this.dataset.confirm)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
@endpush
