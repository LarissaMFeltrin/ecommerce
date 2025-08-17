<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\AuthController;

// Página inicial
Route::get('/', function () {
    $produtos = \App\Models\Produto::ativo()->with('categoria')->orderBy('created_at', 'desc')->limit(8)->get();
    $categorias = \App\Models\Categoria::ativa()->get();
    return view('home', compact('produtos', 'categorias'));
})->name('home');

// Rotas de produtos
Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos.index');
Route::get('/produtos/buscar', [ProdutoController::class, 'buscar'])->name('produtos.buscar');
Route::get('/produtos/{produto}', [ProdutoController::class, 'show'])->name('produtos.show');
Route::get('/categoria/{slug}', [ProdutoController::class, 'porCategoria'])->name('produtos.categoria');

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rota de teste para verificar middleware
Route::get('/test-auth', function () {
    return response()->json([
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user' => \Illuminate\Support\Facades\Auth::user() ? \Illuminate\Support\Facades\Auth::user()->id : null
    ]);
})->middleware('ajax.auth');

// Rotas protegidas (requerem login)
Route::middleware('auth')->group(function () {
    // Perfil do usuário
    Route::get('/perfil', [AuthController::class, 'perfil'])->name('perfil');
    Route::put('/perfil', [AuthController::class, 'atualizarPerfil'])->name('perfil.atualizar');
    Route::put('/perfil/senha', [AuthController::class, 'alterarSenha'])->name('perfil.senha');

    // Carrinho
    Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho.index');
    Route::post('/carrinho/adicionar', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
    Route::put('/carrinho/{item}', [CarrinhoController::class, 'atualizar'])->name('carrinho.atualizar');
    Route::delete('/carrinho/{item}', [CarrinhoController::class, 'remover'])->name('carrinho.remover');
    Route::delete('/carrinho', [CarrinhoController::class, 'limpar'])->name('carrinho.limpar');
    Route::get('/carrinho/quantidade', [CarrinhoController::class, 'quantidade'])->name('carrinho.quantidade');

    // Pedidos
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/create', [PedidoController::class, 'create'])->name('pedidos.create');
    Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos/{pedido}/cancelar', [PedidoController::class, 'cancelar'])->name('pedidos.cancelar');

    // Pagamentos
    Route::get('/pagamentos/{pedido}/instrucoes', [PagamentoController::class, 'instrucoes'])->name('pagamentos.instrucoes');
    Route::post('/pagamentos/{pedido}/processar', [PagamentoController::class, 'processar'])->name('pagamentos.processar');
    Route::post('/pagamentos/{pedido}/cancelar', [PagamentoController::class, 'cancelar'])->name('pagamentos.cancelar');
    Route::post('/pagamentos/{pedido}/reembolsar', [PagamentoController::class, 'reembolsar'])->name('pagamentos.reembolsar');

    // Avaliações
    Route::get('/avaliacoes/minhas', [AvaliacaoController::class, 'minhasAvaliacoes'])->name('avaliacoes.minhas');
    Route::get('/produtos/{produto}/avaliar', [AvaliacaoController::class, 'create'])->name('avaliacoes.create');
    Route::post('/produtos/{produto}/avaliar', [AvaliacaoController::class, 'store'])->name('avaliacoes.store');
    Route::get('/avaliacoes/{avaliacao}/editar', [AvaliacaoController::class, 'edit'])->name('avaliacoes.edit');
    Route::put('/avaliacoes/{avaliacao}', [AvaliacaoController::class, 'update'])->name('avaliacoes.update');
    Route::delete('/avaliacoes/{avaliacao}', [AvaliacaoController::class, 'destroy'])->name('avaliacoes.destroy');

    // Cupons
    Route::post('/cupons/aplicar', [PedidoController::class, 'aplicarCupom'])->name('cupons.aplicar');

    // Endereços
    Route::post('/enderecos/buscar-cep', [PedidoController::class, 'buscarCep'])->name('enderecos.buscar-cep');
    Route::post('/enderecos', [PedidoController::class, 'salvarEndereco'])->name('enderecos.store');
});

// Rotas de administrador
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/avaliacoes', [AvaliacaoController::class, 'todas'])->name('admin.avaliacoes.todas');
    Route::post('/admin/avaliacoes/{avaliacao}/aprovar', [AvaliacaoController::class, 'aprovar'])->name('admin.avaliacoes.aprovar');
    Route::post('/admin/avaliacoes/{avaliacao}/rejeitar', [AvaliacaoController::class, 'rejeitar'])->name('admin.avaliacoes.rejeitar');
    Route::post('/admin/avaliacoes/{avaliacao}/responder', [AvaliacaoController::class, 'responder'])->name('admin.avaliacoes.responder');
});

// Webhook para notificações de pagamento (não requer autenticação)
Route::post('/webhooks/pagamento', [PagamentoController::class, 'webhook'])->name('webhooks.pagamento');

// Rotas AJAX para avaliações
Route::post('/avaliacoes/{avaliacao}/util', [AvaliacaoController::class, 'marcarUtil'])->name('avaliacoes.util');
Route::post('/avaliacoes/{avaliacao}/nao-util', [AvaliacaoController::class, 'marcarNaoUtil'])->name('avaliacoes.nao-util');