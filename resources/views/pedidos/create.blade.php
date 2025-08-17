@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header do Checkout -->
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
                            <a href="{{ route('carrinho.index') }}"
                                class="text-sm font-medium text-gray-700 hover:text-primary">
                                Carrinho
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                            <span class="text-sm font-medium text-gray-500">Checkout</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <h1 class="text-3xl font-bold text-gray-900">Finalizar Compra</h1>
            <p class="text-gray-600 mt-2">Complete suas informações para finalizar o pedido</p>
        </div>

        <form action="{{ route('pedidos.store') }}" method="POST" id="checkout-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Conteúdo Principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Resumo dos Itens -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Itens do Pedido</h2>
                        <div class="space-y-4">
                            @foreach ($itensCarrinho as $item)
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
                                        <h3 class="text-lg font-medium text-gray-900">{{ $item->produto->nome }}</h3>
                                        <p class="text-sm text-gray-600">{{ $item->produto->categoria->nome }}</p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Qtd: {{ $item->quantidade }}</p>
                                        <p class="text-lg font-bold text-primary">{{ $item->subtotal_formatado }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Seleção de Endereço -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Endereço de Entrega</h2>
                            <button type="button" onclick="abrirModalEndereco()"
                                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                <i class="fas fa-plus mr-2"></i> Novo Endereço
                            </button>
                        </div>

                        @if ($enderecos->count() > 0)
                            <div class="space-y-3">
                                @foreach ($enderecos as $endereco)
                                    <label
                                        class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="radio" name="endereco_id" value="{{ $endereco->id }}"
                                            class="mt-1 text-primary focus:ring-primary"
                                            {{ $endereco->principal ? 'checked' : '' }} required>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="font-medium text-gray-900">{{ $endereco->rua }},
                                                    {{ $endereco->numero }}</span>
                                                @if ($endereco->principal)
                                                    <span
                                                        class="inline-block bg-primary text-white text-xs px-2 py-1 rounded-full">Principal</span>
                                                @endif
                                            </div>
                                            @if ($endereco->complemento)
                                                <p class="text-sm text-gray-600">{{ $endereco->complemento }}</p>
                                            @endif
                                            <p class="text-sm text-gray-600">{{ $endereco->bairro }},
                                                {{ $endereco->cidade }} - {{ $endereco->estado }}</p>
                                            <p class="text-sm text-gray-600">CEP: {{ $endereco->cep }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-600 mb-4">Você ainda não cadastrou nenhum endereço</p>
                                <button type="button" onclick="abrirModalEndereco()"
                                    class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                    Cadastrar Primeiro Endereço
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Cupom de Desconto -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Cupom de Desconto</h2>
                        <div class="flex space-x-3">
                            <input type="text" id="cupom-codigo" placeholder="Digite o código do cupom"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            <button type="button" onclick="aplicarCupom()"
                                class="px-6 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                Aplicar
                            </button>
                        </div>
                        <div id="cupom-resultado" class="mt-3 hidden"></div>
                    </div>

                    <!-- Forma de Pagamento -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Forma de Pagamento</h2>
                        <div class="grid grid-cols-2 gap-4">
                            <label
                                class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="forma_pagamento" value="pix"
                                    class="text-primary focus:ring-primary" required>
                                <div>
                                    <i class="fas fa-qrcode text-2xl text-green-600"></i>
                                    <p class="font-medium text-gray-900">PIX</p>
                                    <p class="text-sm text-gray-600">Pagamento instantâneo</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="forma_pagamento" value="cartao_credito"
                                    class="text-primary focus:ring-primary" required>
                                <div>
                                    <i class="fas fa-credit-card text-2xl text-blue-600"></i>
                                    <p class="font-medium text-gray-900">Cartão de Crédito</p>
                                    <p class="text-sm text-gray-600">Até 12x sem juros</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="forma_pagamento" value="cartao_debito"
                                    class="text-primary focus:ring-primary" required>
                                <div>
                                    <i class="fas fa-credit-card text-2xl text-purple-600"></i>
                                    <p class="font-medium text-gray-900">Cartão de Débito</p>
                                    <p class="text-sm text-gray-600">Débito automático</p>
                                </div>
                            </label>

                            <label
                                class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="radio" name="forma_pagamento" value="boleto"
                                    class="text-primary focus:ring-primary" required>
                                <div>
                                    <i class="fas fa-barcode text-2xl text-orange-600"></i>
                                    <p class="font-medium text-gray-900">Boleto</p>
                                    <p class="text-sm text-gray-600">Vencimento em 3 dias</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Observações (Opcional)</h2>
                        <textarea name="observacoes" rows="3" placeholder="Alguma observação especial para sua entrega?"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                </div>

                <!-- Sidebar - Resumo do Pedido -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Resumo do Pedido</h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal ({{ $itensCarrinho->count() }}
                                    {{ $itensCarrinho->count() == 1 ? 'item' : 'itens' }})</span>
                                <span class="font-medium">{{ 'R$ ' . number_format($subtotal, 2, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Frete</span>
                                <span class="font-medium text-green-600">Grátis</span>
                            </div>

                            <div id="cupom-desconto" class="flex justify-between text-sm hidden">
                                <span class="text-gray-600">Desconto</span>
                                <span class="font-medium text-green-600" id="desconto-valor">-R$ 0,00</span>
                            </div>

                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total</span>
                                    <span class="text-primary"
                                        id="total-final">{{ 'R$ ' . number_format($total, 2, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botão Finalizar Compra -->
                        <button type="submit" id="btn-finalizar"
                            class="w-full bg-primary hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-md transition duration-200 flex items-center justify-center">
                            <i class="fas fa-credit-card mr-2"></i> Finalizar Compra
                        </button>

                        <p class="text-xs text-gray-500 text-center mt-3">
                            Ao finalizar, você concorda com nossos <a href="#"
                                class="text-primary hover:underline">termos de uso</a>
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal para Novo Endereço -->
    <div id="modal-endereco" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Novo Endereço</h3>
                    <button onclick="fecharModalEndereco()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="form-endereco" class="p-6">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                                <div class="flex space-x-2">
                                    <input type="text" id="cep" maxlength="8"
                                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                        placeholder="00000-000">
                                    <button type="button" onclick="buscarCep()"
                                        class="px-3 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rua</label>
                            <input type="text" id="rua" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                                <input type="text" id="numero" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Complemento</label>
                                <input type="text" id="complemento"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bairro</label>
                            <input type="text" id="bairro" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cidade</label>
                                <input type="text" id="cidade" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <select id="estado" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                    <option value="">Selecione</option>
                                    <option value="AC">Acre</option>
                                    <option value="AL">Alagoas</option>
                                    <option value="AP">Amapá</option>
                                    <option value="AM">Amazonas</option>
                                    <option value="BA">Bahia</option>
                                    <option value="CE">Ceará</option>
                                    <option value="DF">Distrito Federal</option>
                                    <option value="ES">Espírito Santo</option>
                                    <option value="GO">Goiás</option>
                                    <option value="MA">Maranhão</option>
                                    <option value="MT">Mato Grosso</option>
                                    <option value="MS">Mato Grosso do Sul</option>
                                    <option value="MG">Minas Gerais</option>
                                    <option value="PA">Pará</option>
                                    <option value="PB">Paraíba</option>
                                    <option value="PR">Paraná</option>
                                    <option value="PE">Pernambuco</option>
                                    <option value="PI">Piauí</option>
                                    <option value="RJ">Rio de Janeiro</option>
                                    <option value="RN">Rio Grande do Norte</option>
                                    <option value="RS">Rio Grande do Sul</option>
                                    <option value="RO">Rondônia</option>
                                    <option value="RR">Roraima</option>
                                    <option value="SC">Santa Catarina</option>
                                    <option value="SP">São Paulo</option>
                                    <option value="SE">Sergipe</option>
                                    <option value="TO">Tocantins</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="principal" class="text-primary focus:ring-primary">
                            <label for="principal" class="text-sm text-gray-700">Definir como endereço principal</label>
                        </div>
                    </div>

                    <div class="flex space-x-3 mt-6">
                        <button type="button" onclick="salvarEndereco()"
                            class="flex-1 px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                            Salvar Endereço
                        </button>
                        <button type="button" onclick="fecharModalEndereco()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let cupomAplicado = null;

        function abrirModalEndereco() {
            document.getElementById('modal-endereco').classList.remove('hidden');
        }

        function fecharModalEndereco() {
            document.getElementById('modal-endereco').classList.add('hidden');
            document.getElementById('form-endereco').reset();
        }

        function buscarCep() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');

            if (cep.length !== 8) {
                alert('CEP deve ter 8 dígitos');
                return;
            }

            fetch('{{ route('enderecos.buscar-cep') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cep: cep
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('rua').value = data.endereco.rua;
                        document.getElementById('bairro').value = data.endereco.bairro;
                        document.getElementById('cidade').value = data.endereco.cidade;
                        document.getElementById('estado').value = data.endereco.estado;
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar CEP:', error);
                });
        }

        function salvarEndereco() {
            const formData = {
                cep: document.getElementById('cep').value,
                rua: document.getElementById('rua').value,
                numero: document.getElementById('numero').value,
                complemento: document.getElementById('complemento').value,
                bairro: document.getElementById('bairro').value,
                cidade: document.getElementById('cidade').value,
                estado: document.getElementById('estado').value,
                principal: document.getElementById('principal').checked
            };

            fetch('{{ route('enderecos.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        fecharModalEndereco();
                        location.reload(); // Recarregar para mostrar o novo endereço
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro ao salvar endereço:', error);
                    alert('Erro ao salvar endereço. Tente novamente.');
                });
        }

        function aplicarCupom() {
            const codigo = document.getElementById('cupom-codigo').value.trim();

            if (!codigo) {
                alert('Digite o código do cupom');
                return;
            }

            fetch('{{ route('cupons.aplicar') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        codigo: codigo
                    })
                })
                .then(response => response.json())
                .then(data => {
                    const resultado = document.getElementById('cupom-resultado');

                    if (data.success) {
                        cupomAplicado = data.cupom;
                        resultado.innerHTML = `
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <strong>${data.cupom.codigo}</strong> - ${data.cupom.descricao}<br>
                    Desconto: ${data.cupom.desconto_formatado}
                </div>
            `;
                        resultado.classList.remove('hidden');

                        // Atualizar resumo
                        document.getElementById('cupom-desconto').classList.remove('hidden');
                        document.getElementById('desconto-valor').textContent = `-${data.cupom.desconto_formatado}`;
                        document.getElementById('total-final').textContent = data.novo_total_formatado;

                        // Adicionar campo hidden para o cupom
                        if (!document.getElementById('cupom-hidden')) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'cupom_codigo';
                            input.value = data.cupom.codigo;
                            input.id = 'cupom-hidden';
                            document.getElementById('checkout-form').appendChild(input);
                        }
                    } else {
                        resultado.innerHTML = `
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    ${data.message}
                </div>
            `;
                        resultado.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Erro ao aplicar cupom:', error);
                    alert('Erro ao aplicar cupom. Tente novamente.');
                });
        }

        // Máscara para CEP
        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });

        // Validação do formulário
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const enderecoSelecionado = document.querySelector('input[name="endereco_id"]:checked');
            const formaPagamento = document.querySelector('input[name="forma_pagamento"]:checked');

            if (!enderecoSelecionado) {
                e.preventDefault();
                alert('Selecione um endereço de entrega');
                return;
            }

            if (!formaPagamento) {
                e.preventDefault();
                alert('Selecione uma forma de pagamento');
                return;
            }
        });
    </script>
@endsection
