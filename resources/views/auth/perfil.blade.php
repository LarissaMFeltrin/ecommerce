@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Meu Perfil</h1>
            <p class="text-gray-600 mt-2">Gerencie suas informações pessoais e visualize seu histórico</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar com navegação -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $usuario->nome }}</h3>
                        <p class="text-gray-600">{{ $usuario->email }}</p>
                    </div>

                    <nav class="space-y-2">
                        <a href="#informacoes"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-primary">
                            <i class="fas fa-user-edit mr-3"></i> Informações Pessoais
                        </a>
                        <a href="#enderecos"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-primary">
                            <i class="fas fa-map-marker-alt mr-3"></i> Endereços
                        </a>
                        <a href="#pedidos"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-primary">
                            <i class="fas fa-shopping-bag mr-3"></i> Meus Pedidos
                        </a>
                        <a href="#senha"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-700 rounded-md hover:bg-gray-50 hover:text-primary">
                            <i class="fas fa-lock mr-3"></i> Alterar Senha
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Conteúdo principal -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Informações Pessoais -->
                <div id="informacoes" class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Informações Pessoais</h2>
                        <button onclick="editarInformacoes()"
                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                            <i class="fas fa-edit mr-2"></i> Editar
                        </button>
                    </div>

                    <form id="form-informacoes" action="{{ route('perfil.atualizar') }}" method="POST" class="hidden">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nome Completo</label>
                                <input type="text" name="nome" value="{{ $usuario->nome }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" name="email" value="{{ $usuario->email }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                                <input type="text" name="telefone" value="{{ $usuario->telefone }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                                <input type="text" name="cpf" value="{{ $usuario->cpf }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento</label>
                                <input type="date" name="data_nascimento"
                                    value="{{ $usuario->data_nascimento ? $usuario->data_nascimento->format('Y-m-d') : '' }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <div class="flex space-x-3 mt-6">
                            <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                Salvar Alterações
                            </button>
                            <button type="button" onclick="cancelarEdicao()"
                                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                Cancelar
                            </button>
                        </div>
                    </form>

                    <div id="info-display" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nome</span>
                            <p class="text-gray-900">{{ $usuario->nome }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Email</span>
                            <p class="text-gray-900">{{ $usuario->email }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Telefone</span>
                            <p class="text-gray-900">{{ $usuario->telefone ?: 'Não informado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">CPF</span>
                            <p class="text-gray-900">{{ $usuario->cpf ?: 'Não informado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Data de Nascimento</span>
                            <p class="text-gray-900">
                                {{ $usuario->data_nascimento ? $usuario->data_nascimento->format('d/m/Y') : 'Não informado' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Membro desde</span>
                            <p class="text-gray-900">
                                {{ $usuario->criado_em ? $usuario->criado_em->format('d/m/Y') : 'Data não disponível' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Endereços -->
                <div id="enderecos" class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Meus Endereços</h2>
                        <button onclick="adicionarEndereco()"
                            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                            <i class="fas fa-plus mr-2"></i> Adicionar Endereço
                        </button>
                    </div>

                    @if ($enderecos->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($enderecos as $endereco)
                                <div
                                    class="border border-gray-200 rounded-lg p-4 {{ $endereco->principal ? 'ring-2 ring-primary' : '' }}">
                                    @if ($endereco->principal)
                                        <span
                                            class="inline-block bg-primary text-white text-xs px-2 py-1 rounded-full mb-2">Principal</span>
                                    @endif
                                    <p class="font-medium text-gray-900">{{ $endereco->rua }}, {{ $endereco->numero }}</p>
                                    @if ($endereco->complemento)
                                        <p class="text-gray-600">{{ $endereco->complemento }}</p>
                                    @endif
                                    <p class="text-gray-600">{{ $endereco->bairro }}</p>
                                    <p class="text-gray-600">{{ $endereco->cidade }} - {{ $endereco->estado }}</p>
                                    <p class="text-gray-600">CEP: {{ $endereco->cep }}</p>

                                    <div class="flex space-x-2 mt-3">
                                        @if (!$endereco->principal)
                                            <button class="text-sm text-primary hover:text-blue-600">Definir como
                                                Principal</button>
                                        @endif
                                        <button class="text-sm text-gray-600 hover:text-gray-800">Editar</button>
                                        <button class="text-sm text-red-600 hover:text-red-800">Remover</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600">Você ainda não cadastrou nenhum endereço</p>
                            <button onclick="adicionarEndereco()"
                                class="mt-3 px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                Adicionar Primeiro Endereço
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Histórico de Pedidos -->
                <div id="pedidos" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Meus Pedidos</h2>

                    @if ($pedidos->count() > 0)
                        <div class="space-y-4">
                            @foreach ($pedidos as $pedido)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <h3 class="font-medium text-gray-900">Pedido #{{ $pedido->id }}</h3>
                                            <p class="text-sm text-gray-600">{{ $pedido->criado_em->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span
                                                class="inline-block px-3 py-1 text-xs font-medium rounded-full 
                                                   {{ $pedido->status === 'entregue'
                                                       ? 'bg-green-100 text-green-800'
                                                       : ($pedido->status === 'enviado'
                                                           ? 'bg-blue-100 text-blue-800'
                                                           : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $pedido->status_formatado }}
                                            </span>
                                            <p class="text-lg font-bold text-primary mt-1">
                                                {{ $pedido->valor_total_formatado }}</p>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 pt-3">
                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-600">
                                                {{ $pedido->itens->count() }}
                                                {{ $pedido->itens->count() == 1 ? 'item' : 'itens' }}
                                            </div>
                                            <a href="{{ route('pedidos.show', $pedido) }}"
                                                class="text-primary hover:text-blue-600 text-sm font-medium">
                                                Ver Detalhes <i class="fas fa-arrow-right ml-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Paginação -->
                        <div class="mt-6">
                            {{ $pedidos->links() }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-600">Você ainda não fez nenhum pedido</p>
                            <a href="{{ route('produtos.index') }}"
                                class="mt-3 inline-block px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                                Fazer Primeira Compra
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Alterar Senha -->
                <div id="senha" class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Alterar Senha</h2>

                    <form action="{{ route('perfil.senha') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Senha Atual</label>
                                <input type="password" name="senha_atual" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nova Senha</label>
                                <input type="password" name="nova_senha" required minlength="6"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar Nova Senha</label>
                                <input type="password" name="nova_senha_confirmation" required minlength="6"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        <button type="submit"
                            class="mt-6 px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition duration-200">
                            Alterar Senha
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function editarInformacoes() {
            document.getElementById('form-informacoes').classList.remove('hidden');
            document.getElementById('info-display').classList.add('hidden');
        }

        function cancelarEdicao() {
            document.getElementById('form-informacoes').classList.add('hidden');
            document.getElementById('info-display').classList.remove('hidden');
        }

        function adicionarEndereco() {
            // Implementar modal ou redirecionamento para adicionar endereço
            alert('Funcionalidade de adicionar endereço será implementada em breve!');
        }
    </script>
@endsection
