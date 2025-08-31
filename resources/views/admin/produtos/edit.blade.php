@extends('layouts.admin')

@section('title', 'Editar Produto')
@section('page-title', 'Editar Produto')
@section('page-subtitle', 'Modifique as informações do produto')

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-pencil me-2"></i>
                        Informações do Produto
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.produtos.update', $produto) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome do Produto *</label>
                                    <input type="text" class="form-control @error('nome') is-invalid @enderror"
                                        id="nome" name="nome" value="{{ old('nome', $produto->nome) }}" required>
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
                                                {{ old('categoria_id', $produto->id_categoria) == $categoria->id ? 'selected' : '' }}>
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
                                required>{{ old('descricao', $produto->descricao) }}</textarea>
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
                                            id="preco" name="preco" value="{{ old('preco', $produto->preco) }}"
                                            step="0.01" min="0" required>
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
                                        id="estoque" name="estoque" value="{{ old('estoque', $produto->estoque) }}"
                                        min="0" required>
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

                        <!-- Imagens atuais -->
                        @if ($produto->imagens && $produto->imagens->count() > 0)
                            <div class="mb-3">
                                <label class="form-label">Imagens Atuais</label>
                                <div class="row">
                                    @foreach ($produto->imagens as $index => $imagem)
                                        <div class="col-md-3 mb-2">
                                            <div class="position-relative">
                                                <img src="{{ Storage::url($imagem->caminho) }}"
                                                    alt="Imagem {{ $index + 1 }}" class="img-thumbnail"
                                                    style="width: 100%; height: 100px; object-fit: cover;">
                                                <div class="form-check position-absolute top-0 start-0 m-1">
                                                    <input class="form-check-input" type="checkbox" name="remover_imagens[]"
                                                        value="{{ $index }}"
                                                        id="remover_imagem_{{ $index }}">
                                                    <label class="form-check-label text-white"
                                                        for="remover_imagem_{{ $index }}">
                                                        <i class="bi bi-trash"></i>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="form-text">Marque as imagens que deseja remover.</div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="ativo" name="ativo"
                                    value="1" {{ old('ativo', $produto->ativo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="ativo">
                                    Produto ativo
                                </label>
                            </div>
                            <div class="form-text">Produtos inativos não aparecem no catálogo público.</div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.produtos.index') }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Atualizar Produto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        Informações Adicionais
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID do Produto:</strong>
                        <span class="text-muted">{{ $produto->id }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Slug:</strong>
                        <span class="text-muted">{{ $produto->slug }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Empresa:</strong>
                        <span class="text-muted">{{ $produto->empresa->nome_fantasia ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Criado em:</strong>
                        <span class="text-muted">{{ $produto->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Última atualização:</strong>
                        <span class="text-muted">{{ $produto->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Preview de imagens antes do upload
        document.getElementById('imagens').addEventListener('change', function(e) {
            const files = e.target.files;
            const previewContainer = document.createElement('div');
            previewContainer.className = 'mb-3';
            previewContainer.innerHTML =
                '<label class="form-label">Preview das Novas Imagens</label><div class="row"></div>';

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-2';
                        col.innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                        `;
                        previewContainer.querySelector('.row').appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            }

            // Remover preview anterior se existir
            const existingPreview = document.querySelector('.mb-3:has(.row:has(img))');
            if (existingPreview && existingPreview.textContent.includes('Preview das Novas Imagens')) {
                existingPreview.remove();
            }

            // Adicionar novo preview
            document.getElementById('imagens').parentNode.after(previewContainer);
        });
    </script>
@endpush
