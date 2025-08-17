@extends('layouts.app')

@section('title', 'Produtos')
@section('description', 'Confira nossa seleção de produtos')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header da Página -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Nossos Produtos</h1>
            <p class="text-gray-600">Encontre os melhores produtos com qualidade e preço</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar com Filtros -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtros</h2>

                    <!-- Filtro por Categoria -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Categorias</h3>
                        <div class="space-y-2">
                            <a href="{{ route('produtos.index') }}"
                                class="block text-sm text-gray-600 hover:text-indigo-600 {{ !request('categoria') ? 'text-indigo-600 font-medium' : '' }}">
                                Todas as categorias
                            </a>
                            @foreach ($categorias as $categoria)
                                <a href="{{ route('produtos.index', ['categoria' => $categoria->id]) }}"
                                    class="block text-sm text-gray-600 hover:text-indigo-600 {{ request('categoria') == $categoria->id ? 'text-indigo-600 font-medium' : '' }}">
                                    {{ $categoria->nome }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Ordenação -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Ordenar por</h3>
                        <div class="space-y-2">
                            <a href="{{ route('produtos.index', array_merge(request()->query(), ['ordenacao' => 'nome', 'direcao' => 'asc'])) }}"
                                class="block text-sm text-gray-600 hover:text-indigo-600 {{ request('ordenacao') == 'nome' && request('direcao') == 'asc' ? 'text-indigo-600 font-medium' : '' }}">
                                Nome A-Z
                            </a>
                            <a href="{{ route('produtos.index', array_merge(request()->query(), ['ordenacao' => 'nome', 'direcao' => 'desc'])) }}"
                                class="block text-sm text-gray-600 hover:text-indigo-600 {{ request('ordenacao') == 'nome' && request('direcao') == 'desc' ? 'text-indigo-600 font-medium' : '' }}">
                                Nome Z-A
                            </a>
                            <a href="{{ route('produtos.index', array_merge(request()->query(), ['ordenacao' => 'preco', 'direcao' => 'asc'])) }}"
                                class="block text-sm text-gray-600 hover:text-indigo-600 {{ request('ordenacao') == 'preco' && request('direcao') == 'asc' ? 'text-indigo-600 font-medium' : '' }}">
                                Menor Preço
                            </a>
                            <a href="{{ route('produtos.index', array_merge(request()->query(), ['ordenacao' => 'preco', 'direcao' => 'desc'])) }}"
                                class="block text-sm text-gray-600 hover:text-indigo-600 {{ request('ordenacao') == 'preco' && request('direcao') == 'desc' ? 'text-indigo-600 font-medium' : '' }}">
                                Maior Preço
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Produtos -->
            <div class="lg:w-3/4">
                <!-- Resultados -->
                <div class="mb-6">
                    <p class="text-gray-600">
                        {{ $produtos->total() }} produto(s) encontrado(s)
                        @if (request('busca'))
                            para "{{ request('busca') }}"
                        @endif
                    </p>
                </div>

                <!-- Grid de Produtos -->
                @if ($produtos->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($produtos as $produto)
                            @include('components.produto-card', ['produto' => $produto])
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    <div class="mt-8">
                        {{ $produtos->appends(request()->query())->links() }}
                    </div>
                @else
                    <!-- Nenhum produto encontrado -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Nenhum produto encontrado</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            @if (request('busca'))
                                Não encontramos produtos para "{{ request('busca') }}". Tente uma busca diferente.
                            @else
                                Não há produtos disponíveis no momento.
                            @endif
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('produtos.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Ver todos os produtos
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
