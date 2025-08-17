@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
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
                            <a href="{{ route('pedidos.show', $pedido) }}"
                                class="text-sm font-medium text-gray-700 hover:text-primary">
                                Pedido #{{ $pedido->id }}
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-sm font-medium text-gray-500">Instruções de Pagamento</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-3xl font-bold text-gray-900">Instruções de Pagamento</h1>
            <p class="text-gray-600 mt-2">Pedido #{{ $pedido->id }} - {{ $pedido->valor_total_formatado }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Conteúdo Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status do Pagamento -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Status do Pagamento</h2>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                               {{ $pagamento->status === 'aprovado'
                                   ? 'bg-green-100 text-green-800'
                                   : ($pagamento->status === 'pendente'
                                       ? 'bg-yellow-100 text-yellow-800'
                                       : ($pagamento->status === 'falhou'
                                           ? 'bg-red-100 text-red-800'
                                           : 'bg-gray-100 text-gray-800')) }}">
                            <i class="fas fa-circle text-xs mr-2"></i>
                            {{ $pagamento->status_formatado }}
                        </span>
                    </div>

                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Forma de Pagamento:</span>
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $pagamento->tipo)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Valor:</span>
                            <span class="font-medium text-lg text-primary">{{ $pagamento->valor_formatado }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Referência:</span>
                            <span class="font-medium font-mono">{{ $pagamento->referencia_externa }}</span>
                        </div>
                        @if ($pagamento->processado_em)
                            <div class="flex justify-between">
                                <span>Processado em:</span>
                                <span class="font-medium">{{ $pagamento->processado_em->format('d/m/Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Instruções Específicas por Tipo -->
                @if ($pagamento->tipo === 'pix')
                    <!-- Instruções PIX -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-qrcode text-4xl text-green-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Pagamento via PIX</h2>
                            <p class="text-gray-600">Escaneie o QR Code ou copie o código PIX</p>
                        </div>

                        <!-- QR Code Simulado -->
                        <div class="text-center mb-6">
                            <div
                                class="w-48 h-48 bg-gray-200 rounded-lg flex items-center justify-center mx-auto border-2 border-dashed border-gray-300">
                                <div class="text-center">
                                    <i class="fas fa-qrcode text-6xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500">QR Code Simulado</p>
                                </div>
                            </div>
                        </div>

                        <!-- Código PIX -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Código PIX (Copie e Cole)</label>
                            <div class="flex space-x-2">
                                <input type="text"
                                    value="00020126580014br.gov.bcb.pix0136{{ $pagamento->referencia_externa }}520400005303986540599.005802BR5913Loja Exemplo6008Brasilia62070503***6304ABCD"
                                    class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-mono"
                                    readonly>
                                <button onclick="copiarPix()"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                    <i class="fas fa-copy mr-2"></i> Copiar
                                </button>
                            </div>
                        </div>

                        <!-- Instruções -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h3 class="font-medium text-blue-900 mb-2">Como pagar:</h3>
                            <ol class="list-decimal list-inside space-y-1 text-sm text-blue-800">
                                <li>Abra o app do seu banco</li>
                                <li>Escolha a opção PIX</li>
                                <li>Escaneie o QR Code ou cole o código</li>
                                <li>Confirme o pagamento</li>
                                <li>Aguarde a confirmação</li>
                            </ol>
                        </div>

                        <!-- Botão de Processamento Simulado -->
                        <div class="mt-6 text-center">
                            <form action="{{ route('pagamentos.processar', $pedido) }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="px-6 py-3 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-200">
                                    <i class="fas fa-check mr-2"></i> Simular Pagamento PIX
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($pagamento->tipo === 'cartao_credito')
                    <!-- Instruções Cartão de Crédito -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="text-center mb-6">
                            <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-credit-card text-4xl text-blue-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Pagamento via Cartão de Crédito</h2>
                            <p class="text-gray-600">Preencha os dados do seu cartão</p>
                        </div>

                        <form action="{{ route('pagamentos.processar', $pedido) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número do Cartão</label>
                                <input type="text" name="numero_cartao" placeholder="0000 0000 0000 0000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                    maxlength="19" required>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Validade</label>
                                    <input type="text" name="validade" placeholder="MM/AA"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                        maxlength="5" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                    <input type="text" name="cvv" placeholder="123"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                        maxlength="4" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Parcelas</label>
                                    <select name="parcelas"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="1">1x sem juros</option>
                                        <option value="2">2x sem juros</option>
                                        <option value="3">3x sem juros</option>
                                        <option value="6">6x sem juros</option>
                                        <option value="12">12x sem juros</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome no Cartão</label>
                                <input type="text" name="nome_cartao" placeholder="Como está impresso no cartão"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                                    <i class="fas fa-lock mr-2"></i> Processar Pagamento
                                </button>
                            </div>
                        </form>
                    </div>
                @elseif($pagamento->tipo === 'cartao_debito')
                    <!-- Instruções Cartão de Débito -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="text-center mb-6">
                            <div
                                class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-credit-card text-4xl text-purple-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Pagamento via Cartão de Débito</h2>
                            <p class="text-gray-600">Preencha os dados do seu cartão</p>
                        </div>

                        <form action="{{ route('pagamentos.processar', $pedido) }}" method="POST" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número do Cartão</label>
                                <input type="text" name="numero_cartao" placeholder="0000 0000 0000 0000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                    maxlength="19" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Validade</label>
                                    <input type="text" name="validade" placeholder="MM/AA"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                        maxlength="5" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                                    <input type="text" name="cvv" placeholder="123"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                        maxlength="4" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome no Cartão</label>
                                <input type="text" name="nome_cartao" placeholder="Como está impresso no cartão"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                    required>
                            </div>

                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full px-6 py-3 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition duration-200">
                                    <i class="fas fa-lock mr-2"></i> Processar Pagamento
                                </button>
                            </div>
                        </form>
                    </div>
                @elseif($pagamento->tipo === 'boleto')
                    <!-- Instruções Boleto -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="text-center mb-6">
                            <div
                                class="w-24 h-24 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-barcode text-4xl text-orange-600"></i>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Pagamento via Boleto</h2>
                            <p class="text-gray-600">Imprima ou pague online</p>
                        </div>

                        <!-- Boleto Simulado -->
                        <div class="text-center mb-6">
                            <div
                                class="w-full max-w-md bg-gray-200 rounded-lg p-6 mx-auto border-2 border-dashed border-gray-300">
                                <div class="text-center">
                                    <i class="fas fa-barcode text-6xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-500 mb-2">Boleto Bancário</p>
                                    <p class="text-xs text-gray-400">Código de Barras Simulado</p>
                                </div>
                            </div>
                        </div>

                        <!-- Código de Barras -->
                        <div class="bg-gray-50 rounded-lg p-4 mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Código de Barras</label>
                            <div class="flex space-x-2">
                                <input type="text" value="23790.12345 67890.123456 78901.234567 8 12345678901234"
                                    class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-mono"
                                    readonly>
                                <button onclick="copiarBoleto()"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                    <i class="fas fa-copy mr-2"></i> Copiar
                                </button>
                            </div>
                        </div>

                        <!-- Instruções -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                            <h3 class="font-medium text-orange-900 mb-2">Como pagar:</h3>
                            <ol class="list-decimal list-inside space-y-1 text-sm text-orange-800">
                                <li>Imprima o boleto ou copie o código</li>
                                <li>Pague em qualquer banco ou lotérica</li>
                                <li>Vencimento: {{ now()->addDays(3)->format('d/m/Y') }}</li>
                                <li>Após o pagamento, aguarde até 2 dias úteis</li>
                            </ol>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="mt-6 flex space-x-3">
                            <button onclick="imprimirBoleto()"
                                class="flex-1 px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition duration-200">
                                <i class="fas fa-print mr-2"></i> Imprimir Boleto
                            </button>
                            <button onclick="pagarOnline()"
                                class="flex-1 px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                <i class="fas fa-globe mr-2"></i> Pagar Online
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Informações Importantes -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                        <div>
                            <h3 class="font-medium text-yellow-900">Informações Importantes</h3>
                            <ul class="mt-2 space-y-1 text-sm text-yellow-800">
                                <li>• Este é um sistema de demonstração - os pagamentos são simulados</li>
                                <li>• Em produção, você integraria com gateways reais (PagSeguro, MercadoPago, etc.)</li>
                                <li>• O status será atualizado automaticamente após o processamento</li>
                                <li>• Em caso de dúvidas, entre em contato com nosso suporte</li>
                            </ul>
                        </div>
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
                                <span class="text-gray-600">Desconto</span>
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

                <!-- Ações -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Ações</h2>
                    <div class="space-y-3">
                        <a href="{{ route('pedidos.show', $pedido) }}"
                            class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50 transition duration-200 text-center block">
                            <i class="fas fa-arrow-left mr-2"></i> Voltar ao Pedido
                        </a>

                        @if ($pagamento->status === 'pendente')
                            <form action="{{ route('pagamentos.cancelar', $pedido) }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit"
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200"
                                    onclick="return confirm('Tem certeza que deseja cancelar este pagamento?')">
                                    <i class="fas fa-times mr-2"></i> Cancelar Pagamento
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copiarPix() {
            const input = document.querySelector('input[value*="00020126580014br.gov.bcb.pix"]');
            input.select();
            document.execCommand('copy');

            // Feedback visual
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i> Copiado!';
            button.classList.add('bg-green-600');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600');
            }, 2000);
        }

        function copiarBoleto() {
            const input = document.querySelector('input[value*="23790.12345"]');
            input.select();
            document.execCommand('copy');

            // Feedback visual
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check mr-2"></i> Copiado!';
            button.classList.add('bg-green-600');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-600');
            }, 2000);
        }

        function imprimirBoleto() {
            alert('Funcionalidade de impressão será implementada em produção!');
        }

        function pagarOnline() {
            alert('Funcionalidade de pagamento online será implementada em produção!');
        }

        // Máscaras para os campos
        document.addEventListener('DOMContentLoaded', function() {
            // Máscara para número do cartão
            const numeroCartao = document.querySelector('input[name="numero_cartao"]');
            if (numeroCartao) {
                numeroCartao.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                    e.target.value = value;
                });
            }

            // Máscara para validade
            const validade = document.querySelector('input[name="validade"]');
            if (validade) {
                validade.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 2) {
                        value = value.substring(0, 2) + '/' + value.substring(2, 4);
                    }
                    e.target.value = value;
                });
            }

            // Máscara para CVV
            const cvv = document.querySelector('input[name="cvv"]');
            if (cvv) {
                cvv.addEventListener('input', function(e) {
                    e.target.value = e.target.value.replace(/\D/g, '');
                });
            }
        });
    </script>
@endsection
