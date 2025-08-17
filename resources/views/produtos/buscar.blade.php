@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header da Busca -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('home') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary">
                            <i class="fas fa-home mr-2"></i>
                            Início
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <a href="{{ route('produtos.index') }}"
                                class="text-sm font-medium text-gray-700 hover:text-primary">
                                Produtos
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-sm font-medium text-gray-500">Resultados da Busca</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Resultados da Busca</h1>
                @if (request('q'))
                    <p class="text-gray-600 mb-2">Buscando por: <span class="font-semibold">"{{ request('q') }}"</span></p>
                @endif
                @if (request('categoria'))
                    <p class="text-gray-600 mb-2">Categoria: <span
                            class="font-semibold">{{ \App\Models\Categoria::find(request('categoria'))->nome ?? 'N/A' }}</span>
                    </p>
                @endif
                <p class="text-sm text-gray-500">{{ $produtos->total() }}
                    {{ $produtos->total() == 1 ? 'produto encontrado' : 'produtos encontrados' }}</p>
            </div>
        </div>

        <!-- Filtros e Ordenação -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <!-- Busca -->
                <div class="flex-1 max-w-md">
                    <form action="{{ route('produtos.buscar') }}" method="GET" class="relative">
                        @if (request('categoria'))
                            <input type="hidden" name="categoria" value="{{ request('categoria') }}">
                        @endif
                        <input type="text" name="q" placeholder="Buscar produtos..." value="{{ request('q') }}"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-arrow-right text-primary"></i>
                        </button>
                    </form>
                </div>

                <!-- Filtros -->
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">Categoria:</label>
                    <select id="categoria-filter"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="">Todas as categorias</option>
                        @foreach (\App\Models\Categoria::ativa()->get() as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nome }}
                            </option>
                        @endforeach
                    </select>

                    <label class="text-sm font-medium text-gray-700">Ordenar por:</label>
                    <select id="ordenacao"
                        class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="relevancia" {{ request('ordenacao') == 'relevancia' ? 'selected' : '' }}>Relevância
                        </option>
                        <option value="nome" {{ request('ordenacao') == 'nome' ? 'selected' : '' }}>Nome A-Z</option>
                        <option value="nome_desc" {{ request('ordenacao') == 'nome_desc' ? 'selected' : '' }}>Nome Z-A
                        </option>
                        <option value="preco_asc" {{ request('ordenacao') == 'preco_asc' ? 'selected' : '' }}>Menor Preço
                        </option>
                        <option value="preco_desc" {{ request('ordenacao') == 'preco_desc' ? 'selected' : '' }}>Maior Preço
                        </option>
                        <option value="recente" {{ request('ordenacao') == 'recente' ? 'selected' : '' }}>Mais Recente
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Resultados -->
        @if ($produtos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($produtos as $produto)
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <!-- Imagem do Produto -->
                        <div class="relative">
                            @if ($produto->imagem)
                                <img src="{{ asset('storage/' . $produto->imagem) }}" alt="{{ $produto->nome }}"
                                    class="w-full h-48 object-cover">
                            @else
                                <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            @endif

                            <!-- Badges -->
                            <div class="absolute top-2 left-2 space-y-1">
                                @if ($produto->estoque <= 5 && $produto->estoque > 0)
                                    <span class="inline-block bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                                        Últimas unidades
                                    </span>
                                @endif
                                @if ($produto->estoque == 0)
                                    <span class="inline-block bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        Esgotado
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Informações do Produto -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                <a href="{{ route('produtos.show', $produto) }}" class="hover:text-primary">
                                    {{ $produto->nome }}
                                </a>
                            </h3>

                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($produto->descricao, 80) }}
                            </p>

                            <!-- Categoria -->
                            <p class="text-xs text-primary font-medium mb-3">{{ $produto->categoria->nome }}</p>

                            <!-- Preço -->
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <span class="text-2xl font-bold text-primary">{{ $produto->preco_formatado }}</span>
                                    @if ($produto->estoque > 0)
                                        <p class="text-xs text-gray-500">Em estoque: {{ $produto->estoque }}
                                            {{ $produto->estoque == 1 ? 'unidade' : 'unidades' }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Ações -->
                            <div class="flex space-x-2">
                                @if ($produto->estoque > 0)
                                    <form action="{{ route('carrinho.adicionar') }}" method="POST" class="flex-1"
                                        onsubmit="return adicionarAoCarrinho(this)">
                                        @csrf
                                        <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                                        <input type="hidden" name="quantidade" value="1">
                                        <button type="submit"
                                            class="w-full bg-primary hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-md transition duration-200 flex items-center justify-center">
                                            <i class="fas fa-shopping-cart mr-2"></i> Adicionar
                                        </button>
                                    </form>
                                @else
                                    <button disabled
                                        class="w-full bg-gray-300 text-gray-500 font-medium py-2 px-4 rounded-md cursor-not-allowed">
                                        Esgotado
                                    </button>
                                @endif

                                <a href="{{ route('produtos.show', $produto) }}"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition duration-200">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="mt-8">
                {{ $produtos->links() }}
            </div>
        @else
            <!-- Estado vazio -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-search text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Nenhum produto encontrado</h3>
                <p class="text-gray-600 mb-6">
                    Não encontramos produtos com os termos de busca "{{ request('q') }}"
                    @if (request('categoria'))
                        na categoria selecionada
                    @endif
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('produtos.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i> Ver Todos os Produtos
                    </a>
                    <button onclick="limparFiltros()"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-filter mr-2"></i> Limpar Filtros
                    </button>
                </div>
            </div>
        @endif

        <!-- Sugestões de Busca -->
        @if ($produtos->count() == 0 && request('q'))
            <div class="mt-12">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Sugestões de Busca</h2>
                <div class="bg-gray-50 rounded-lg p-6">
                    <p class="text-gray-600 mb-4">Tente estas alternativas:</p>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li>• Verifique se digitou corretamente</li>
                        <li>• Tente termos mais genéricos</li>
                        <li>• Use sinônimos para o que está procurando</li>
                        <li>• Remova filtros de categoria para ampliar a busca</li>
                    </ul>
                </div>
            </div>
        @endif
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    <script>
        // Filtros
        document.getElementById('categoria-filter').addEventListener('change', function() {
            aplicarFiltros();
        });

        document.getElementById('ordenacao').addEventListener('change', function() {
            aplicarFiltros();
        });

        function aplicarFiltros() {
            const categoria = document.getElementById('categoria-filter').value;
            const ordenacao = document.getElementById('ordenacao').value;
            const busca = '{{ request('q') }}';

            let url = new URL(window.location);

            if (categoria) url.searchParams.set('categoria', categoria);
            if (ordenacao) url.searchParams.set('ordenacao', ordenacao);
            if (busca) url.searchParams.set('q', busca);

            window.location.href = url.toString();
        }

        function limparFiltros() {
            const busca = '{{ request('q') }}';
            let url = new URL(window.location);

            // Manter apenas o termo de busca
            url.searchParams.delete('categoria');
            url.searchParams.delete('ordenacao');

            if (busca) {
                url.searchParams.set('q', busca);
            }

            window.location.href = url.toString();
        }

        // Aplicar filtros da URL ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('categoria')) {
                document.getElementById('categoria-filter').value = urlParams.get('categoria');
            }

            if (urlParams.has('ordenacao')) {
                document.getElementById('ordenacao').value = urlParams.get('ordenacao');
            }
        });
    </script>
@endsection
