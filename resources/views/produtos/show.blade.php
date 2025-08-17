@extends('layouts.app')

@section('title', $produto->nome)
@section('description', Str::limit($produto->descricao, 160))

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600">
                        Início
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('produtos.index') }}" class="text-gray-700 hover:text-indigo-600 ml-1 md:ml-2">
                            Produtos
                        </a>
                    </div>
                </li>
                @if ($produto->categoria)
                    <li>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <a href="{{ route('produtos.categoria', $produto->categoria->slug) }}"
                                class="text-gray-700 hover:text-indigo-600 ml-1 md:ml-2">
                                {{ $produto->categoria->nome }}
                            </a>
                        </div>
                    </li>
                @endif
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">{{ $produto->nome }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Imagem do Produto -->
            <div>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="{{ $produto->imagem ? asset('storage/' . $produto->imagem) : 'https://via.placeholder.com/600x400?text=Produto' }}"
                        alt="{{ $produto->nome }}" class="w-full h-96 object-cover">
                </div>
            </div>

            <!-- Informações do Produto -->
            <div>
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- Categoria -->
                    @if ($produto->categoria)
                        <div class="mb-4">
                            <span class="bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $produto->categoria->nome }}
                            </span>
                        </div>
                    @endif

                    <!-- Nome do Produto -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $produto->nome }}</h1>

                    <!-- Preço -->
                    <div class="mb-6">
                        <span class="text-4xl font-bold text-indigo-600">{{ $produto->preco_formatado }}</span>
                    </div>

                    <!-- Status do Estoque -->
                    <div class="mb-6">
                        @if ($produto->disponivel)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Em estoque ({{ $produto->estoque }} unidades)
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                Fora de estoque
                            </span>
                        @endif
                    </div>

                    <!-- Descrição -->
                    @if ($produto->descricao)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Descrição</h3>
                            <p class="text-gray-600 leading-relaxed">{{ $produto->descricao }}</p>
                        </div>
                    @endif

                    <!-- Formulário de Adição ao Carrinho -->
                    @if ($produto->disponivel)
                        <form action="{{ route('carrinho.adicionar') }}" method="POST" class="mb-6"
                            onsubmit="return adicionarAoCarrinho(this)">
                            @csrf
                            <input type="hidden" name="produto_id" value="{{ $produto->id }}">

                            <div class="flex items-center space-x-4">
                                <div class="flex items-center border border-gray-300 rounded-md">
                                    <label for="quantidade" class="px-3 py-2 text-sm text-gray-600">Quantidade:</label>
                                    <input type="number" name="quantidade" id="quantidade" value="1" min="1"
                                        max="{{ $produto->estoque }}"
                                        class="w-20 px-3 py-2 text-center border-0 focus:ring-0 focus:ring-indigo-500">
                                </div>

                                <button type="submit"
                                    class="flex-1 bg-indigo-600 text-white px-6 py-3 rounded-md font-medium hover:bg-indigo-700 transition-colors duration-200">
                                    Adicionar ao Carrinho
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="mb-6">
                            <button disabled
                                class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-md font-medium cursor-not-allowed">
                                Produto Indisponível
                            </button>
                        </div>
                    @endif

                    <!-- Informações Adicionais -->
                    <div class="border-t pt-6">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Código:</span>
                                <span class="text-gray-900 ml-2">#{{ $produto->id }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Categoria:</span>
                                <span class="text-gray-900 ml-2">{{ $produto->categoria->nome ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produtos Relacionados -->
        @if ($produtosRelacionados->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Produtos Relacionados</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($produtosRelacionados as $produtoRelacionado)
                        @include('components.produto-card', ['produto' => $produtoRelacionado])
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Avaliações -->
        @if ($produto->avaliacoes->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Avaliações dos Clientes</h2>
                <div class="space-y-4">
                    @foreach ($produto->avaliacoes as $avaliacao)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center">
                                    <div class="flex text-yellow-400">
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $avaliacao->nota)
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                                    </path>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                                    </path>
                                                </svg>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">{{ $avaliacao->nota }}/5</span>
                                </div>
                                <span class="text-sm text-gray-500">{{ $avaliacao->criado_em->format('d/m/Y') }}</span>
                            </div>
                            <p class="text-gray-700">{{ $avaliacao->comentario }}</p>
                            <p class="text-sm text-gray-500 mt-2">- {{ $avaliacao->usuario->nome ?? 'Usuário' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
