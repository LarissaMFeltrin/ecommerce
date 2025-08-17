<div class="bg-white rounded-lg shadow-md hover:shadow-lg transition duration-300 overflow-hidden">
    <!-- Product Image -->
    <div class="relative">
        <img src="{{ $produto->imagem }}" alt="{{ $produto->nome }}" class="w-full h-48 object-cover">
        @if ($produto->estoque <= 0)
            <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold">
                Esgotado
            </div>
        @elseif($produto->estoque <= 5)
            <div class="absolute top-2 right-2 bg-orange-500 text-white px-2 py-1 rounded text-xs font-semibold">
                Últimas unidades
            </div>
        @endif
    </div>

    <!-- Product Info -->
    <div class="p-4">
        <!-- Category -->
        <div class="mb-2">
            <span class="text-xs text-primary font-semibold uppercase tracking-wide">
                {{ $produto->categoria->nome }}
            </span>
        </div>

        <!-- Product Name -->
        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
            <a href="{{ route('produtos.show', $produto) }}" class="hover:text-primary">
                {{ $produto->nome }}
            </a>
        </h3>

        <!-- Price -->
        <div class="mb-3">
            <span class="text-2xl font-bold text-primary">
                {{ $produto->preco_formatado }}
            </span>
        </div>

        <!-- Stock Info -->
        <div class="mb-4">
            @if ($produto->estoque > 0)
                <span class="text-sm text-green-600">
                    <i class="fas fa-check-circle mr-1"></i>
                    Em estoque ({{ $produto->estoque }})
                </span>
            @else
                <span class="text-sm text-red-600">
                    <i class="fas fa-times-circle mr-1"></i>
                    Fora de estoque
                </span>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex space-x-2">
            <a href="{{ route('produtos.show', $produto) }}"
                class="flex-1 bg-gray-100 text-gray-700 px-3 py-2 rounded-md text-sm font-medium hover:bg-gray-200 transition duration-200 text-center">
                <i class="fas fa-eye mr-1"></i>
                <span class="hidden sm:inline">Ver</span>
            </a>

            @auth
                @if ($produto->estoque > 0)
                    <form action="{{ route('carrinho.adicionar') }}" method="POST" class="flex-1"
                        onsubmit="return adicionarAoCarrinho(this)">
                        @csrf
                        <input type="hidden" name="produto_id" value="{{ $produto->id }}">
                        <input type="hidden" name="quantidade" value="1">
                        <button type="submit"
                            class="w-full bg-primary text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-600 transition duration-200">
                            <i class="fas fa-cart-plus mr-1"></i>
                            <span class="hidden sm:inline">Adicionar</span>
                        </button>
                    </form>
                @else
                    <button disabled
                        class="flex-1 bg-gray-300 text-gray-500 px-3 py-2 rounded-md text-sm font-medium cursor-not-allowed">
                        <i class="fas fa-ban mr-1"></i>
                        <span class="hidden sm:inline">Indisponível</span>
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}"
                    class="flex-1 bg-primary text-white px-3 py-2 rounded-md text-sm font-medium hover:bg-blue-600 transition duration-200 text-center">
                    <i class="fas fa-sign-in-alt mr-1"></i>
                    <span class="hidden sm:inline">Login</span>
                </a>
            @endauth
        </div>
    </div>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
