@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Meus Pedidos</h1>
            <p class="text-gray-600 mt-2">Acompanhe o status e histórico de todos os seus pedidos</p>
        </div>

        @if ($pedidos->count() > 0)
            <!-- Filtros -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Filtrar por status:</label>
                        <select id="status-filter"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="">Todos os status</option>
                            <option value="pendente">Pendente</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="em_processamento">Em Processamento</option>
                            <option value="enviado">Enviado</option>
                            <option value="entregue">Entregue</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-4">
                        <label class="text-sm font-medium text-gray-700">Ordenar por:</label>
                        <select id="order-filter"
                            class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="recente">Mais Recente</option>
                            <option value="antigo">Mais Antigo</option>
                            <option value="valor_maior">Maior Valor</option>
                            <option value="valor_menor">Menor Valor</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Lista de Pedidos -->
            <div class="space-y-6">
                @foreach ($pedidos as $pedido)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header do Pedido -->
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <div
                                class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                            <i class="fas fa-shopping-bag text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Pedido #{{ $pedido->id }}</h3>
                                        <p class="text-sm text-gray-600">Realizado em
                                            {{ $pedido->criado_em->format('d/m/Y \à\s H:i') }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col items-end space-y-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
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
                                    <p class="text-2xl font-bold text-primary">{{ $pedido->valor_total_formatado }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Itens do Pedido -->
                        <div class="px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Itens do Pedido</h4>
                            <div class="space-y-3">
                                @foreach ($pedido->itens as $item)
                                    <div class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg">
                                        @if ($item->produto->imagem)
                                            <img src="{{ asset('storage/' . $item->produto->imagem) }}"
                                                alt="{{ $item->produto->nome }}" class="w-12 h-12 object-cover rounded-md">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-md flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif

                                        <div class="flex-1">
                                            <h5 class="text-sm font-medium text-gray-900">{{ $item->produto->nome }}</h5>
                                            <p class="text-xs text-gray-600">{{ $item->produto->categoria->nome }}</p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-sm text-gray-600">Qtd: {{ $item->quantidade }}</p>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $item->preco_unitario_formatado }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Informações de Entrega -->
                        @if ($pedido->endereco)
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Endereço de Entrega</h4>
                                <div class="text-sm text-gray-600">
                                    <p>{{ $pedido->endereco->rua }}, {{ $pedido->endereco->numero }}</p>
                                    @if ($pedido->endereco->complemento)
                                        <p>{{ $pedido->endereco->complemento }}</p>
                                    @endif
                                    <p>{{ $pedido->endereco->bairro }}, {{ $pedido->endereco->cidade }} -
                                        {{ $pedido->endereco->estado }}</p>
                                    <p>CEP: {{ $pedido->endereco->cep }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Cupom aplicado -->
                        @if ($pedido->cupom)
                            <div class="px-6 py-4 bg-green-50 border-t border-green-200">
                                <h4 class="text-sm font-medium text-green-700 mb-1">Cupom Aplicado</h4>
                                <p class="text-sm text-green-600">
                                    <strong>{{ $pedido->cupom->codigo }}</strong> - {{ $pedido->cupom->descricao }}
                                </p>
                            </div>
                        @endif

                        <!-- Ações do Pedido -->
                        <div class="px-6 py-4 bg-white border-t border-gray-200">
                            <div
                                class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                                <div class="flex items-center space-x-4 text-sm text-gray-600">
                                    <span>{{ $pedido->itens->count() }}
                                        {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }}</span>
                                    <span>•</span>
                                    <span>Total: {{ $pedido->valor_total_formatado }}</span>
                                </div>

                                <div class="flex space-x-3">
                                    <a href="{{ route('pedidos.show', $pedido) }}"
                                        class="inline-flex items-center px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white transition duration-200 text-sm font-medium">
                                        <i class="fas fa-eye mr-2"></i> Ver Detalhes
                                    </a>

                                    @if ($pedido->status === 'pendente')
                                        <button
                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200 text-sm font-medium"
                                            onclick="cancelarPedido({{ $pedido->id }})">
                                            <i class="fas fa-times mr-2"></i> Cancelar
                                        </button>
                                    @endif

                                    @if ($pedido->status === 'entregue')
                                        <button
                                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-200 text-sm font-medium"
                                            onclick="avaliarPedido({{ $pedido->id }})">
                                            <i class="fas fa-star mr-2"></i> Avaliar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="mt-8">
                {{ $pedidos->links() }}
            </div>
        @else
            <!-- Estado vazio -->
            <div class="text-center py-12">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <i class="fas fa-shopping-bag text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Nenhum pedido encontrado</h3>
                <p class="text-gray-600 mb-6">Você ainda não fez nenhum pedido ou os filtros aplicados não retornaram
                    resultados.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center space-y-3 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('produtos.index') }}"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-shopping-bag mr-2"></i> Fazer Primeira Compra
                    </a>
                    <button onclick="limparFiltros()"
                        class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-filter mr-2"></i> Limpar Filtros
                    </button>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Filtros
        document.getElementById('status-filter').addEventListener('change', function() {
            aplicarFiltros();
        });

        document.getElementById('order-filter').addEventListener('change', function() {
            aplicarFiltros();
        });

        function aplicarFiltros() {
            const status = document.getElementById('status-filter').value;
            const order = document.getElementById('order-filter').value;

            let url = new URL(window.location);

            if (status) url.searchParams.set('status', status);
            if (order) url.searchParams.set('order', order);

            window.location.href = url.toString();
        }

        function limparFiltros() {
            window.location.href = window.location.pathname;
        }

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

        // Aplicar filtros da URL ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('status')) {
                document.getElementById('status-filter').value = urlParams.get('status');
            }

            if (urlParams.has('order')) {
                document.getElementById('order-filter').value = urlParams.get('order');
            }
        });
    </script>
@endsection
