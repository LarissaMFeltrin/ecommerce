@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Meu Carrinho</h1>
            <p class="text-gray-600 mt-2">Gerencie os produtos no seu carrinho de compras</p>
        </div>

        @if ($itens->count() > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <!-- Header do carrinho -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="grid grid-cols-12 gap-4 text-sm font-medium text-gray-700">
                        <div class="col-span-6">Produto</div>
                        <div class="col-span-2 text-center">Preço</div>
                        <div class="col-span-2 text-center">Quantidade</div>
                        <div class="col-span-2 text-center">Subtotal</div>
                    </div>
                </div>

                <!-- Itens do carrinho -->
                @foreach ($itens as $item)
                    <div class="px-6 py-4 border-b border-gray-200 hover:bg-gray-50">
                        <div class="grid grid-cols-12 gap-4 items-center">
                            <!-- Informações do produto -->
                            <div class="col-span-6">
                                <div class="flex items-center space-x-4">
                                    @if ($item->produto->imagem)
                                        <img src="{{ asset('storage/' . $item->produto->imagem) }}"
                                            alt="{{ $item->produto->nome }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-xl"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            <a href="{{ route('produtos.show', $item->produto) }}"
                                                class="hover:text-primary">
                                                {{ $item->produto->nome }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ $item->produto->categoria->nome }}</p>
                                        @if ($item->produto->estoque < $item->quantidade)
                                            <span class="text-red-600 text-sm font-medium">
                                                Estoque insuficiente ({{ $item->produto->estoque }} disponível)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Preço unitário -->
                            <div class="col-span-2 text-center">
                                <span class="text-lg font-medium text-gray-900">
                                    {{ $item->produto->preco_formatado }}
                                </span>
                            </div>

                            <!-- Controles de quantidade -->
                            <div class="col-span-2 text-center">
                                <form action="{{ route('carrinho.atualizar', $item) }}" method="POST"
                                    class="flex items-center justify-center space-x-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="button" onclick="alterarQuantidade({{ $item->id }}, -1)"
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>

                                    <input type="number" name="quantidade" value="{{ $item->quantidade }}" min="1"
                                        max="{{ $item->produto->estoque }}"
                                        class="w-16 text-center border border-gray-300 rounded-md px-2 py-1 text-sm"
                                        onchange="atualizarQuantidade({{ $item->id }}, this.value)">

                                    <button type="button" onclick="alterarQuantidade({{ $item->id }}, 1)"
                                        class="w-8 h-8 bg-gray-200 hover:bg-gray-300 rounded-full flex items-center justify-center text-gray-600 hover:text-gray-800">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </form>
                            </div>

                            <!-- Subtotal -->
                            <div class="col-span-2 text-center">
                                <span class="text-lg font-bold text-primary">
                                    {{ $item->subtotal_formatado }}
                                </span>
                            </div>

                            <!-- Botão remover -->
                            <div class="col-span-12 md:col-span-0 flex justify-end mt-4 md:mt-0">
                                <form action="{{ route('carrinho.remover', $item) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm"
                                        onclick="return confirm('Tem certeza que deseja remover este item?')">
                                        <i class="fas fa-trash mr-1"></i> Remover
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Resumo e ações -->
                <div class="bg-gray-50 px-6 py-6">
                    <div
                        class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                        <!-- Ações do carrinho -->
                        <div class="flex space-x-3">
                            <form action="{{ route('carrinho.limpar') }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium"
                                    onclick="return confirm('Tem certeza que deseja limpar o carrinho?')">
                                    <i class="fas fa-trash mr-2"></i> Limpar Carrinho
                                </button>
                            </form>

                            <a href="{{ route('produtos.index') }}"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium">
                                <i class="fas fa-arrow-left mr-2"></i> Continuar Comprando
                            </a>
                        </div>

                        <!-- Resumo do pedido -->
                        <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h3>

                            <div class="space-y-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal ({{ $itens->count() }}
                                        {{ $itens->count() == 1 ? 'item' : 'itens' }})</span>
                                    <span class="font-medium">{{ 'R$ ' . number_format($total, 2, ',', '.') }}</span>
                                </div>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Frete</span>
                                    <span class="font-medium text-green-600">Grátis</span>
                                </div>

                                <div class="border-t border-gray-200 pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span class="text-primary">{{ 'R$ ' . number_format($total, 2, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Botão finalizar compra -->
                            <div class="mt-6">
                                <a href="{{ route('pedidos.create') }}"
                                    class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-md transition duration-200 flex items-center justify-center">
                                    <i class="fas fa-credit-card mr-2"></i> Finalizar Compra
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Carrinho vazio -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-shopping-cart text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Seu carrinho está vazio</h3>
                <p class="text-gray-600 mb-6">Adicione alguns produtos para começar suas compras!</p>
                <a href="{{ route('produtos.index') }}"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-blue-600 transition duration-200">
                    <i class="fas fa-shopping-bag mr-2"></i> Ver Produtos
                </a>
            </div>
        @endif
    </div>

    <script>
        function alterarQuantidade(itemId, delta) {
            const input = document.querySelector(`input[name="quantidade"][onchange*="${itemId}"]`);
            const novaQuantidade = parseInt(input.value) + delta;

            if (novaQuantidade >= 1) {
                input.value = novaQuantidade;
                atualizarQuantidade(itemId, novaQuantidade);
            }
        }

        function atualizarQuantidade(itemId, quantidade) {
            // Aqui você pode implementar uma atualização AJAX se desejar
            // Por enquanto, o formulário será enviado quando o usuário mudar a quantidade
            console.log(`Atualizando item ${itemId} para quantidade ${quantidade}`);
        }
    </script>
@endsection
