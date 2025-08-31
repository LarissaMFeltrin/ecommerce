<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\AdminController;
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
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Produtos
    Route::get('/produtos', [AdminController::class, 'produtos'])->name('produtos.index');
    Route::get('/produtos/criar', [AdminController::class, 'criarProduto'])->name('produtos.create');
    Route::post('/produtos', [AdminController::class, 'salvarProduto'])->name('produtos.store');
    Route::get('/produtos/{produto}/editar', [AdminController::class, 'editarProduto'])->name('produtos.edit');
    Route::put('/produtos/{produto}', [AdminController::class, 'atualizarProduto'])->name('produtos.update');
    Route::delete('/produtos/{produto}', [AdminController::class, 'excluirProduto'])->name('produtos.destroy');

    // Usuários
    Route::get('/usuarios', [AdminController::class, 'usuarios'])->name('usuarios.index');
    Route::get('/usuarios/{usuario}', [AdminController::class, 'detalhesUsuario'])->name('usuarios.show');
    Route::post('/usuarios/{usuario}/toggle', [AdminController::class, 'toggleUsuario'])->name('usuarios.toggle');

    // Pedidos
    Route::get('/pedidos', [AdminController::class, 'pedidos'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [AdminController::class, 'detalhesPedido'])->name('pedidos.show');
    Route::put('/pedidos/{pedido}/status', [AdminController::class, 'atualizarStatusPedido'])->name('pedidos.status');

    // Categorias
    Route::get('/categorias', [AdminController::class, 'categorias'])->name('categorias.index');
    Route::post('/categorias', [AdminController::class, 'salvarCategoria'])->name('categorias.store');
    Route::put('/categorias/{categoria}', [AdminController::class, 'atualizarCategoria'])->name('categorias.update');
    Route::delete('/categorias/{categoria}', [AdminController::class, 'excluirCategoria'])->name('categorias.destroy');

    // Avaliações (usando AdminController)
    Route::get('/avaliacoes', [AdminController::class, 'avaliacoes'])->name('avaliacoes.index');
    Route::post('/avaliacoes/{avaliacao}/aprovar', [AdminController::class, 'aprovarAvaliacao'])->name('avaliacoes.aprovar');
    Route::post('/avaliacoes/{avaliacao}/rejeitar', [AdminController::class, 'rejeitarAvaliacao'])->name('avaliacoes.rejeitar');

    // Relatórios
    Route::get('/relatorios', [AdminController::class, 'relatorios'])->name('relatorios.index');

    // Administradores
    Route::get('/administradores', [AdminController::class, 'administradores'])->name('administradores.index');
    Route::get('/administradores/criar', [AdminController::class, 'criarAdministrador'])->name('administradores.create');
    Route::post('/administradores', [AdminController::class, 'salvarAdministrador'])->name('administradores.store');

    // Configurações
    Route::get('/configuracoes', [AdminController::class, 'configuracoes'])->name('configuracoes.index');
    Route::post('/configuracoes', [AdminController::class, 'salvarConfiguracoes'])->name('configuracoes.store');
});

// Webhook para notificações de pagamento (não requer autenticação)
Route::post('/webhooks/pagamento', [PagamentoController::class, 'webhook'])->name('webhooks.pagamento');

// Rotas AJAX para avaliações
Route::post('/avaliacoes/{avaliacao}/util', [AvaliacaoController::class, 'marcarUtil'])->name('avaliacoes.util');
Route::post('/avaliacoes/{avaliacao}/nao-util', [AvaliacaoController::class, 'marcarNaoUtil'])->name('avaliacoes.nao-util');
