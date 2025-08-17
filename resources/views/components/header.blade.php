<header class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-primary">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    <span class="hidden sm:inline">Minha Loja</span>
                    <span class="sm:hidden">ML</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}"
                    class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">
                    Início
                </a>
                <a href="{{ route('produtos.index') }}"
                    class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">
                    Produtos
                </a>
                <div class="relative group">
                    <button
                        class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium flex items-center">
                        Categorias
                        <i class="fas fa-chevron-down ml-1"></i>
                    </button>
                    <div
                        class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                        @foreach (\App\Models\Categoria::ativa()->get() as $categoria)
                            <a href="{{ route('produtos.categoria', $categoria->slug) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                {{ $categoria->nome }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </nav>

            <!-- User Menu & Cart -->
            <div class="flex items-center space-x-4">
                <!-- Search (Desktop) -->
                <div class="hidden md:block">
                    <form action="{{ route('produtos.buscar') }}" method="GET" class="flex">
                        <input type="text" name="q" placeholder="Buscar produtos..."
                            class="w-64 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-primary">
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-r-md hover:bg-blue-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Cart -->
                @auth
                    <a href="{{ route('carrinho.index') }}" class="relative text-gray-700 hover:text-primary">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-counter"
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            0
                        </span>
                    </a>
                @endauth

                <!-- User Menu -->
                @auth
                    <div class="relative group">
                        <button class="flex items-center text-gray-700 hover:text-primary">
                            <i class="fas fa-user-circle text-xl mr-2"></i>
                            <span class="hidden sm:inline">{{ Auth::user()->nome }}</span>
                        </button>
                        <div
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="{{ route('perfil') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Perfil
                            </a>
                            <a href="{{ route('pedidos.index') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-list mr-2"></i> Meus Pedidos
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Sair
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex space-x-2">
                        <a href="{{ route('login') }}"
                            class="text-gray-700 hover:text-primary px-3 py-2 rounded-md text-sm font-medium">
                            <span class="hidden sm:inline">Entrar</span>
                            <i class="fas fa-sign-in-alt sm:hidden"></i>
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-primary text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-600">
                            <span class="hidden sm:inline">Cadastrar</span>
                            <i class="fas fa-user-plus sm:hidden"></i>
                        </a>
                    </div>
                @endauth

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-primary">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <!-- Mobile Search -->
                <form action="{{ route('produtos.buscar') }}" method="GET" class="mb-4">
                    <div class="flex">
                        <input type="text" name="q" placeholder="Buscar produtos..."
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-primary">
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-r-md hover:bg-blue-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>

                <a href="{{ route('home') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                    <i class="fas fa-home mr-2"></i> Início
                </a>
                <a href="{{ route('produtos.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                    <i class="fas fa-box mr-2"></i> Produtos
                </a>

                <!-- Mobile Categories -->
                <div class="border-t border-gray-200 pt-4">
                    <p class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Categorias</p>
                    @foreach (\App\Models\Categoria::ativa()->get() as $categoria)
                        <a href="{{ route('produtos.categoria', $categoria->slug) }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                            {{ $categoria->nome }}
                        </a>
                    @endforeach
                </div>

                @auth
                    <div class="border-t border-gray-200 pt-4">
                        <a href="{{ route('carrinho.index') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                            <i class="fas fa-shopping-cart mr-2"></i> Carrinho
                        </a>
                        <a href="{{ route('perfil') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                            <i class="fas fa-user mr-2"></i> Perfil
                        </a>
                        <a href="{{ route('pedidos.index') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                            <i class="fas fa-list mr-2"></i> Meus Pedidos
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="block">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                                <i class="fas fa-sign-out-alt mr-2"></i> Sair
                            </button>
                        </form>
                    </div>
                @else
                    <div class="border-t border-gray-200 pt-4">
                        <a href="{{ route('login') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-primary hover:bg-gray-50">
                            <i class="fas fa-sign-in-alt mr-2"></i> Entrar
                        </a>
                        <a href="{{ route('register') }}"
                            class="block px-3 py-2 rounded-md text-base font-medium text-primary hover:bg-gray-50">
                            <i class="fas fa-user-plus mr-2"></i> Cadastrar
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>
