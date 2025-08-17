@extends('layouts.app')

@section('title', 'Início')
@section('description', 'Sua loja online de confiança')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-r from-primary to-blue-600 rounded-lg shadow-lg p-8 mb-8">
            <div class="text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
                    Bem-vindo à Minha Loja
                </h1>
                <p class="text-xl text-blue-100 mb-6">
                    Descubra produtos incríveis com os melhores preços
                </p>
                <a href="{{ route('produtos.index') }}"
                    class="bg-white text-primary px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Ver Todos os Produtos
                </a>
            </div>
        </div>

        <!-- Categories Section -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Categorias</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach ($categorias as $categoria)
                    <a href="{{ route('produtos.categoria', $categoria->slug) }}"
                        class="bg-white rounded-lg shadow-md p-4 text-center hover:shadow-lg transition duration-300">
                        <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-tag text-white text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800">{{ $categoria->nome }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $categoria->produtos->count() }} produtos</p>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Featured Products -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Produtos em Destaque</h2>
                <a href="{{ route('produtos.index') }}" class="text-primary hover:text-blue-600 font-semibold">
                    Ver Todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($produtos as $produto)
                    @include('components.produto-card', ['produto' => $produto])
                @endforeach
            </div>
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shipping-fast text-white text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Entrega Rápida</h3>
                <p class="text-gray-600">Entrega em todo o Brasil com rapidez e segurança</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Compra Segura</h3>
                <p class="text-gray-600">Pagamento seguro com várias formas de pagamento</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-white text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Suporte 24/7</h3>
                <p class="text-gray-600">Atendimento ao cliente sempre disponível</p>
            </div>
        </div>
    </div>
@endsection
