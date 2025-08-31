@extends('layouts.admin')

@section('title', 'Produtos')
@section('page-title', 'Gerenciar Produtos')
@section('page-subtitle', 'Lista de todos os produtos do sistema')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.produtos.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="busca" class="form-control" placeholder="Buscar produtos..."
                                value="{{ request('busca') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="categoria" class="form-select">
                                <option value="">Todas as categorias</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}"
                                        {{ request('categoria') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select">
                                <option value="">Todos os status</option>
                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Ativo</option>
                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inativo</option>
                            </select>
                        </div>
                        <div class="col-md-2">
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
                    <a href="{{ route('admin.produtos.create') }}" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-plus-circle me-2"></i>Novo Produto
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="bi bi-box-seam me-2"></i>
                Produtos ({{ $produtos->total() }})
            </h6>
        </div>
        <div class="card-body p-0">
            @if ($produtos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produto</th>
                                <th>Categoria</th>
                                <th class="text-center">Preço</th>
                                <th class="text-center">Estoque</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produtos as $produto)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($produto->imagens->count() > 0)
                                                <img src="{{ Storage::url($produto->imagens->first()->caminho) }}"
                                                    alt="{{ $produto->nome }}" class="rounded me-3"
                                                    style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                    style="width: 50px; height: 50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $produto->nome }}</h6>
                                                <small class="text-muted">{{ Str::limit($produto->descricao, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($produto->categoria)
                                            <span class="badge bg-info">{{ $produto->categoria->nome }}</span>
                                        @else
                                            <span class="text-muted">Sem categoria</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold text-success">R$
                                            {{ number_format($produto->preco, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if ($produto->estoque > 10)
                                            <span class="badge bg-success">{{ $produto->estoque }}</span>
                                        @elseif($produto->estoque > 0)
                                            <span class="badge bg-warning text-dark">{{ $produto->estoque }}</span>
                                        @else
                                            <span class="badge bg-danger">Esgotado</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($produto->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('produtos.show', $produto->slug) }}"
                                                class="btn btn-sm btn-outline-primary" title="Ver no site" target="_blank">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.produtos.edit', $produto) }}"
                                                class="btn btn-sm btn-outline-warning" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Excluir"
                                                onclick="confirmarExclusao({{ $produto->id }}, '{{ $produto->nome }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Formulário de exclusão oculto -->
                                        <form id="delete-form-{{ $produto->id }}"
                                            action="{{ route('admin.produtos.destroy', $produto) }}" method="POST"
                                            style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
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
                            Mostrando {{ $produtos->firstItem() }} a {{ $produtos->lastItem() }} de
                            {{ $produtos->total() }} produtos
                        </div>
                        <div>
                            {{ $produtos->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box-seam display-1 text-muted"></i>
                    <h5 class="text-muted mt-3">Nenhum produto encontrado</h5>
                    <p class="text-muted">Comece criando seu primeiro produto!</p>
                    <a href="{{ route('admin.produtos.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Criar Produto
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o produto <strong id="produtoNome"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="executarExclusao()">Excluir</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let produtoIdParaExcluir = null;

        function confirmarExclusao(id, nome) {
            produtoIdParaExcluir = id;
            document.getElementById('produtoNome').textContent = nome;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        function executarExclusao() {
            if (produtoIdParaExcluir) {
                document.getElementById('delete-form-' + produtoIdParaExcluir).submit();
            }
        }

        // Auto-submit do formulário de filtro quando mudar categoria ou status
        document.addEventListener('DOMContentLoaded', function() {
            const categoriaSelect = document.querySelector('select[name="categoria"]');
            const statusSelect = document.querySelector('select[name="status"]');

            categoriaSelect.addEventListener('change', function() {
                this.closest('form').submit();
            });

            statusSelect.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    </script>
@endpush
