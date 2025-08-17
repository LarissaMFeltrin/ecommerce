<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'E-commerce') }}</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#3B82F6',
                        secondary: '#1F2937',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    @include('components.header')

    <!-- Main Content -->
    <main class="flex-1">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4"
                role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4"
                role="alert">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    @include('components.footer')

    <!-- Modal de Confirmação do Carrinho -->
    <div id="modal-carrinho" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                            <i class="fas fa-check text-green-600 text-xl"></i>
                        </div>
                    </div>

                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">
                            Produto Adicionado!
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            O produto foi adicionado ao seu carrinho com sucesso.
                        </p>

                        <div class="flex space-x-3">
                            <button onclick="continuarComprando()"
                                class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition duration-200">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Continuar Comprando
                            </button>
                            <a href="{{ route('carrinho.index') }}"
                                class="flex-1 bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Ver Carrinho
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // Atualizar contador do carrinho
            atualizarContadorCarrinho();
        });

        // Função para mostrar o modal do carrinho
        function mostrarModalCarrinho() {
            const modal = document.getElementById('modal-carrinho');
            if (modal) {
                modal.classList.remove('hidden');
                // Atualizar contador do carrinho
                atualizarContadorCarrinho();
            }
        }

        // Função para esconder o modal do carrinho
        function esconderModalCarrinho() {
            const modal = document.getElementById('modal-carrinho');
            if (modal) {
                modal.classList.add('hidden');
            }
        }

        // Função para continuar comprando
        function continuarComprando() {
            esconderModalCarrinho();
        }

        // Função para atualizar o contador do carrinho
        function atualizarContadorCarrinho() {
            fetch('{{ route('carrinho.quantidade') }}')
                .then(response => response.json())
                .then(data => {
                    const cartCounter = document.getElementById('cart-counter');
                    if (cartCounter) {
                        cartCounter.textContent = data.quantidade;
                    }
                })
                .catch(error => {
                    console.error('Erro ao atualizar contador do carrinho:', error);
                });
        }

        // Função para adicionar ao carrinho e mostrar modal
        function adicionarAoCarrinho(form) {
            const formData = new FormData(form);

            console.log('Enviando requisição para:', form.action);
            console.log('Dados do formulário:', Object.fromEntries(formData));
            console.log('Usuário autenticado:', {{ Auth::check() ? 'true' : 'false' }});

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Status da resposta:', response.status);
                    console.log('Headers da resposta:', response.headers);

                    // Verificar se a resposta é JSON
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);

                    if (!contentType || !contentType.includes('application/json')) {
                        // Se não for JSON, tentar ler como texto para debug
                        return response.text().then(text => {
                            console.error('Resposta não é JSON:', text);
                            throw new Error(
                                `Resposta não é JSON. Content-Type: ${contentType}. Resposta: ${text.substring(0, 200)}...`
                            );
                        });
                    }

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Resposta do servidor:', data);
                    if (data.success) {
                        mostrarModalCarrinho();
                        // Atualizar contador do carrinho
                        atualizarContadorCarrinho();
                    } else {
                        alert(data.message || 'Erro ao adicionar produto ao carrinho');
                    }
                })
                .catch(error => {
                    console.error('Erro detalhado:', error);
                    console.error('Stack trace:', error.stack);

                    // Verificar se é um erro de autenticação
                    if (error.message.includes('401') || error.message.includes('Unauthorized')) {
                        alert(
                            'Você precisa estar logado para adicionar produtos ao carrinho. Redirecionando para a página de login...'
                            );
                        window.location.href = '{{ route('login') }}';
                        return;
                    }

                    // Verificar se é um erro de validação
                    if (error.message.includes('422') || error.message.includes('Validation')) {
                        alert('Erro de validação. Verifique os dados informados.');
                        return;
                    }

                    alert('Erro ao adicionar produto ao carrinho: ' + error.message);
                });

            return false; // Previne o envio normal do formulário
        }
    </script>
</body>

</html>
