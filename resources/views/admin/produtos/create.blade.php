@extends('layouts.admin')

@section('title', 'Novo Produto')
@section('page-title', 'Criar Novo Produto')
@section('page-subtitle', 'Adicione um novo produto ao catálogo')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-plus-circle me-2"></i>
                        Informações do Produto
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.produtos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome do Produto *</label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                        id="nome" name="nome" value="{{ old('nome') }}" required>
                                    @error('nome')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="categoria_id" class="form-label">Categoria *</label>
                                    <select class="form-select @error('categoria_id') is-invalid @enderror"
                                        id="categoria_id" name="categoria_id" required>
                                        <option value="">Selecione uma categoria</option>
                                        @foreach ($categorias as $categoria)
                                            <option value="{{ $categoria->id }}"
                                                {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
                                                {{ $categoria->nome }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('categoria_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição *</label>
                            <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao" rows="4"
                                required>{{ old('descricao') }}</textarea>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Descreva detalhadamente o produto para os clientes.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="preco" class="form-label">Preço (R$) *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" class="form-control @error('preco') is-invalid @enderror"
                                            id="preco" name="preco" value="{{ old('preco') }}" step="0.01"
                                            min="0" required>
                                    </div>
                                    @error('preco')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estoque" class="form-label">Estoque *</label>
                                    <input type="number" class="form-control @error('estoque') is-invalid @enderror"
                                        id="estoque" name="estoque" value="{{ old('estoque') }}" min="0" required>
                                    @error('estoque')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagens" class="form-label">Imagens do Produto</label>
                            <input type="file" class="form-control @error('imagens.*') is-invalid @enderror"
                                id="imagens" name="imagens[]" multiple accept="image/*">
                            @error('imagens.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Selecione uma ou mais imagens. Formatos aceitos: JPG, PNG, GIF. Tamanho máximo: 2MB por
                                imagem.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                                    {{ old('ativo', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ativo">
                                    Produto ativo (visível para os clientes)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.produtos.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i>Criar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview das Imagens -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-image me-2"></i>
                        Preview das Imagens
                    </h6>
                </div>
                <div class="card-body">
                    <div id="imagePreview" class="text-center">
                        <i class="bi bi-image display-4 text-muted"></i>
                        <p class="text-muted mt-2">Selecione imagens para visualizar</p>
                    </div>
                </div>
            </div>

            <!-- Dicas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>
                        Dicas para um Produto Atraente
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Use nomes descritivos e atrativos
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Descreva benefícios e características
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Adicione imagens de alta qualidade
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Mantenha o estoque atualizado
                        </li>
                        <li>
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Categorize corretamente
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('imagens');
            const imagePreview = document.getElementById('imagePreview');

            imageInput.addEventListener('change', function(e) {
                const files = e.target.files;

                if (files.length > 0) {
                    imagePreview.innerHTML = '';

                    Array.from(files).forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.className = 'img-fluid rounded mb-2';
                                img.style.maxHeight = '150px';
                                img.alt = `Preview ${index + 1}`;

                                imagePreview.appendChild(img);
                            };

                            reader.readAsDataURL(file);
                        }
                    });
                } else {
                    imagePreview.innerHTML = `
                <i class="bi bi-image display-4 text-muted"></i>
                <p class="text-muted mt-2">Selecione imagens para visualizar</p>
            `;
                }
            });

            // Validação de preço
            const precoInput = document.getElementById('preco');
            precoInput.addEventListener('input', function(e) {
                let value = e.target.value;
                if (value < 0) {
                    e.target.value = 0;
                }
            });

            // Validação de estoque
            const estoqueInput = document.getElementById('estoque');
            estoqueInput.addEventListener('input', function(e) {
                let value = e.target.value;
                if (value < 0) {
                    e.target.value = 0;
                }
            });
        });
    </script>
@endpush
