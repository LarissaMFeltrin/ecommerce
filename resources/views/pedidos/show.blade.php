@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
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
                        <a href="{{ route('pedidos.index') }}" class="text-sm font-medium text-gray-700 hover:text-primary">
                            Meus Pedidos
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">Pedido #{{ $pedido->id }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header do Pedido -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Pedido #{{ $pedido->id }}</h1>
                    <p class="text-gray-600">Realizado em {{ $pedido->criado_em->format('d/m/Y \à\s H:i') }}</p>
                </div>

                <div class="flex flex-col items-end space-y-2">
                    <span
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                           {{ $pedido->status === 'entregue'
                               ? 'bg-green-100 text-green-800'
                               : ($pedido->status === 'enviado'
                                   ? 'bg-blue-100 text-blue-800'
                                   : ($pedido->status === 'aprovado'
                                       ? 'bg-yellow-100 text-yellow-800'
                                       : ($pedido->status === 'cancelado'
                                           ? 'bg-red-100 text-red-800'
                                           : 'bg-gray-100 text-gray-800'))) }}">
                        <i class="fas fa-circle text-xs mr-2"></i>
                        {{ $pedido->status_formatado }}
                    </span>
                    <p class="text-3xl font-bold text-primary">{{ $pedido->valor_total_formatado }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Conteúdo Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Itens do Pedido -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Itens do Pedido</h2>
                    <div class="space-y-4">
                        @foreach ($pedido->itens as $item)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                @if ($item->produto->imagem)
                                    <img src="{{ asset('storage/' . $item->produto->imagem) }}"
                                        alt="{{ $item->produto->nome }}" class="w-16 h-16 object-cover rounded-md">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded-md flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <a href="{{ route('produtos.show', $item->produto) }}" class="hover:text-primary">
                                            {{ $item->produto->nome }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $item->produto->categoria->nome }}</p>
                                    <p class="text-sm text-gray-500">Código: {{ $item->produto->id }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Quantidade: {{ $item->quantidade }}</p>
                                    <p class="text-sm font-medium text-gray-900">Preço unitário:
                                        {{ $item->preco_unitario_formatado }}</p>
                                    <p class="text-lg font-bold text-primary">{{ $item->subtotal_formatado }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Histórico de Status -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Histórico do Pedido</h2>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check text-white text-sm"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900">Pedido Realizado</h3>
                                <p class="text-sm text-gray-600">{{ $pedido->criado_em->format('d/m/Y \à\s H:i') }}</p>
                                <p class="text-sm text-gray-500">Seu pedido foi recebido e está sendo processado</p>
                            </div>
                        </div>

                        @if ($pedido->status !== 'pendente')
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-credit-card text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">Pagamento Aprovado</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $pedido->criado_em->addMinutes(5)->format('d/m/Y \à\s H:i') }}</p>
                                    <p class="text-sm text-gray-500">Pagamento confirmado, preparando para envio</p>
                                </div>
                            </div>
                        @endif

                        @if (in_array($pedido->status, ['enviado', 'entregue']))
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-shipping-fast text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">Pedido Enviado</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $pedido->criado_em->addHours(2)->format('d/m/Y \à\s H:i') }}</p>
                                    <p class="text-sm text-gray-500">Seu pedido foi enviado e está a caminho</p>
                                </div>
                            </div>
                        @endif

                        @if ($pedido->status === 'entregue')
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-home text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">Pedido Entregue</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $pedido->criado_em->addDays(3)->format('d/m/Y \à\s H:i') }}</p>
                                    <p class="text-sm text-gray-500">Seu pedido foi entregue com sucesso!</p>
                                </div>
                            </div>
                        @endif

                        @if ($pedido->status === 'cancelado')
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-times text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">Pedido Cancelado</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $pedido->criado_em->addHours(1)->format('d/m/Y \à\s H:i') }}</p>
                                    <p class="text-sm text-gray-500">Pedido cancelado conforme solicitado</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Ações do Pedido -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Ações</h2>
                    <div class="flex flex-wrap gap-3">
                        @if ($pedido->status === 'pendente')
                            <button onclick="cancelarPedido({{ $pedido->id }})"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200">
                                <i class="fas fa-times mr-2"></i> Cancelar Pedido
                            </button>
                        @endif

                        @if ($pedido->status === 'entregue')
                            <button onclick="avaliarPedido({{ $pedido->id }})"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-200">
                                <i class="fas fa-star mr-2"></i> Avaliar Produtos
                            </button>
                        @endif

                        <a href="{{ route('pedidos.index') }}"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i> Voltar aos Pedidos
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Resumo do Pedido -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span
                                class="font-medium">{{ 'R$ ' . number_format($pedido->itens->sum('subtotal'), 2, ',', '.') }}</span>
                        </div>

                        @if ($pedido->cupom)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Desconto ({{ $pedido->cupom->codigo }})</span>
                                <span class="font-medium text-green-600">-{{ $pedido->cupom->valor_formatado }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Frete</span>
                            <span class="font-medium text-green-600">Grátis</span>
                        </div>

                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span class="text-primary">{{ $pedido->valor_total_formatado }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endereço de Entrega -->
                @if ($pedido->endereco)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Endereço de Entrega</h2>
                        <div class="space-y-2 text-sm text-gray-600">
                            <p class="font-medium">{{ $pedido->endereco->rua }}, {{ $pedido->endereco->numero }}</p>
                            @if ($pedido->endereco->complemento)
                                <p>{{ $pedido->endereco->complemento }}</p>
                            @endif
                            <p>{{ $pedido->endereco->bairro }}</p>
                            <p>{{ $pedido->endereco->cidade }} - {{ $pedido->endereco->estado }}</p>
                            <p>CEP: {{ $pedido->endereco->cep }}</p>
                        </div>
                    </div>
                @endif

                <!-- Informações de Pagamento -->
                @if ($pedido->pagamentos->count() > 0)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações de Pagamento</h2>
                        @foreach ($pedido->pagamentos as $pagamento)
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Forma:</span>
                                    <span class="font-medium">{{ $pagamento->tipo }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium">{{ $pagamento->status_formatado }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Valor:</span>
                                    <span class="font-medium">{{ $pagamento->valor_formatado }}</span>
                                </div>
                                @if ($pagamento->referencia_externa)
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Referência:</span>
                                        <span class="font-medium">{{ $pagamento->referencia_externa }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Cupom Aplicado -->
                @if ($pedido->cupom)
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <h2 class="text-lg font-semibold text-green-800 mb-2">Cupom Aplicado</h2>
                        <div class="space-y-2">
                            <p class="text-green-700 font-medium">{{ $pedido->cupom->codigo }}</p>
                            <p class="text-green-600 text-sm">{{ $pedido->cupom->descricao }}</p>
                            <p class="text-green-600 text-sm">Desconto: {{ $pedido->cupom->valor_formatado }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function cancelarPedido(pedidoId) {
            if (confirm('Tem certeza que deseja cancelar este pedido?')) {
                // Implementar cancelamento do pedido
                alert('Funcionalidade de cancelamento será implementada em breve!');
            }
        }

        function avaliarPedido(pedidoId) {
            // Implementar avaliação do pedido
            alert('Funcionalidade de avaliação será implementada em breve!');
        }
    </script>
@endsection
