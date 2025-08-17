@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                        <i class="fas fa-home mr-2"></i>
                        Início
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('produtos.show', $produto) }}" class="text-sm font-medium text-gray-700 hover:text-primary">
                            {{ $produto->nome }}
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">Avaliar Produto</span>
                    </div>
                </li>
            </ol>
        </nav>

        <h1 class="text-3xl font-bold text-gray-900">Avaliar Produto</h1>
        <p class="text-gray-600 mt-2">Compartilhe sua experiência com este produto</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Formulário de Avaliação -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form action="{{ route('avaliacoes.store', $produto) }}" method="POST" id="form-avaliacao">
                    @csrf
                    
                    <!-- Informações do Produto -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            @if($produto->imagem)
                                <img src="{{ asset('storage/' . $produto->imagem) }}" 
                                     alt="{{ $produto->nome }}" 
                                     class="w-16 h-16 object-cover rounded-md">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $produto->nome }}</h3>
                                <p class="text-sm text-gray-600">{{ $produto->categoria->nome }}</p>
                                <p class="text-sm text-gray-500">Comprado em {{ now()->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Nota -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Sua Avaliação</label>
                        <div class="flex items-center space-x-4">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="flex items-center space-x-2 cursor-pointer">
                                    <input type="radio" name="nota" value="{{ $i }}" 
                                           class="text-primary focus:ring-primary" required>
                                    <div class="flex items-center space-x-1">
                                        @for($j = 1; $j <= $i; $j++)
                                            <i class="fas fa-star text-yellow-400 text-xl"></i>
                                        @endfor
                                        @for($j = $i + 1; $j <= 5; $j++)
                                            <i class="far fa-star text-gray-300 text-xl"></i>
                                        @endfor
                                    </div>
                                </label>
                            @endfor
                        </div>
                        @error('nota')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Título -->
                    <div class="mb-6">
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                            Título da Avaliação <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="titulo" name="titulo" 
                               value="{{ old('titulo') }}"
                               placeholder="Ex: Excelente produto, superou minhas expectativas!"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                               maxlength="100" required>
                        <p class="text-sm text-gray-500 mt-1">Máximo 100 caracteres</p>
                        @error('titulo')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Comentário -->
                    <div class="mb-6">
                        <label for="comentario" class="block text-sm font-medium text-gray-700 mb-2">
                            Seu Comentário <span class="text-red-500">*</span>
                        </label>
                        <textarea id="comentario" name="comentario" rows="6" 
                                  placeholder="Conte sua experiência com este produto. O que você gostou? O que poderia melhorar? Recomendaria para outros?"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                  maxlength="1000" required>{{ old('comentario') }}</textarea>
                        <div class="flex justify-between items-center mt-1">
                            <p class="text-sm text-gray-500">Mínimo 10, máximo 1000 caracteres</p>
                            <span id="contador-caracteres" class="text-sm text-gray-500">0/1000</span>
                        </div>
                        @error('comentario')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recomenda -->
                    <div class="mb-6">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="recomenda" value="1" 
                                   class="text-primary focus:ring-primary rounded"
                                   {{ old('recomenda') ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-gray-700">
                                Recomendo este produto para outros compradores
                            </span>
                        </label>
                    </div>

                    <!-- Botões -->
                    <div class="flex space-x-4">
                        <button type="submit" 
                                class="flex-1 px-6 py-3 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                            <i class="fas fa-paper-plane mr-2"></i> Enviar Avaliação
                        </button>
                        <a href="{{ route('produtos.show', $produto) }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition duration-200">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Dicas para uma Boa Avaliação -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="font-medium text-blue-900 mb-3">
                    <i class="fas fa-lightbulb text-blue-600 mr-2"></i>
                    Dicas para uma Boa Avaliação
                </h3>
                <ul class="space-y-2 text-sm text-blue-800">
                    <li>• Seja específico sobre o que gostou ou não</li>
                    <li>• Mencione a qualidade do produto</li>
                    <li>• Comente sobre o atendimento</li>
                    <li>• Inclua fotos se possível</li>
                    <li>• Seja honesto e construtivo</li>
                </ul>
            </div>

            <!-- Política de Avaliações -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-3">
                    <i class="fas fa-info-circle text-gray-600 mr-2"></i>
                    Política de Avaliações
                </h3>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li>• Apenas compradores podem avaliar</li>
                    <li>• Avaliações são moderadas</li>
                    <li>• Linguagem inadequada não é permitida</li>
                    <li>• Você pode editar sua avaliação</li>
                    <li>• Avaliações ajudam outros compradores</li>
                </ul>
            </div>

            <!-- Estatísticas do Produto -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-3">
                    <i class="fas fa-chart-bar text-gray-600 mr-2"></i>
                    Estatísticas
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Avaliação Média:</span>
                        <span class="font-medium">
                            @if($produto->avaliacao_media > 0)
                                {{ $produto->avaliacao_media }}/5
                                <div class="flex items-center space-x-1 mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $produto->avaliacao_media)
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        @else
                                            <i class="far fa-star text-gray-300 text-xs"></i>
                                        @endif
                                    @endfor
                                </div>
                            @else
                                Nenhuma avaliação
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total de Avaliações:</span>
                        <span class="font-medium">{{ $produto->avaliacao_total }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const comentario = document.getElementById('comentario');
    const contador = document.getElementById('contador-caracteres');
    
    // Atualizar contador de caracteres
    function atualizarContador() {
        const caracteres = comentario.value.length;
        contador.textContent = `${caracteres}/1000`;
        
        if (caracteres > 900) {
            contador.classList.add('text-red-600');
        } else if (caracteres > 800) {
            contador.classList.add('text-yellow-600');
        } else {
            contador.classList.remove('text-red-600', 'text-yellow-600');
        }
    }
    
    comentario.addEventListener('input', atualizarContador);
    atualizarContador(); // Inicializar contador
    
    // Validação do formulário
    document.getElementById('form-avaliacao').addEventListener('submit', function(e) {
        const nota = document.querySelector('input[name="nota"]:checked');
        const titulo = document.getElementById('titulo').value.trim();
        const comentario = document.getElementById('comentario').value.trim();
        
        if (!nota) {
            e.preventDefault();
            alert('Por favor, selecione uma nota para o produto.');
            return;
        }
        
        if (titulo.length < 5) {
            e.preventDefault();
            alert('O título deve ter pelo menos 5 caracteres.');
            return;
        }
        
        if (comentario.length < 10) {
            e.preventDefault();
            alert('O comentário deve ter pelo menos 10 caracteres.');
            return;
        }
        
        if (comentario.length > 1000) {
            e.preventDefault();
            alert('O comentário não pode ter mais de 1000 caracteres.');
            return;
        }
    });
});
</script>
@endsection
