<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Produto;
use App\Models\Usuario;
use App\Models\Pedido;
use App\Models\Categoria;
use App\Models\Avaliacao;
use App\Models\Administrador;
use App\Models\Pagamento;

class AdminController extends Controller
{
    /**
     * Dashboard principal do administrador
     */
    public function dashboard()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Estatísticas gerais - filtradas por empresa se não for super admin
        $queryProdutos = Produto::query();
        $queryUsuarios = Usuario::query();
        $queryPedidos = Pedido::query();
        $queryAvaliacoes = Avaliacao::query();

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryProdutos->porEmpresa($empresaId);
            $queryUsuarios->porEmpresa($empresaId);
            $queryPedidos->porEmpresa($empresaId);
            $queryAvaliacoes->porEmpresa($empresaId);
        }

        $stats = [
            'total_produtos' => $queryProdutos->count(),
            'total_usuarios' => $queryUsuarios->count(),
            'total_pedidos' => $queryPedidos->count(),
            'pedidos_pendentes' => $queryPedidos->clone()->porStatus('pendente')->count(),
            'pedidos_aprovados' => $queryPedidos->clone()->porStatus('aprovado')->count(),
            'pedidos_cancelados' => $queryPedidos->clone()->porStatus('cancelado')->count(),
            'total_vendas' => $queryPedidos->clone()->sum('valor_total'),
            'avaliacoes_pendentes' => $queryAvaliacoes->clone()->pendente()->count(),
        ];

        // Produtos mais vendidos - filtrados por empresa
        $queryProdutosVendidos = DB::table('itens_pedido')
            ->join('produtos', 'itens_pedido.id_produto', '=', 'produtos.id')
            ->join('pedidos', 'itens_pedido.id_pedido', '=', 'pedidos.id');

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryProdutosVendidos->where('produtos.empresa_id', $empresaId);
        }

        $produtosMaisVendidos = $queryProdutosVendidos
            ->select('produtos.nome', 'produtos.id', DB::raw('SUM(itens_pedido.quantidade) as total_vendido'))
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderBy('total_vendido', 'desc')
            ->limit(5)
            ->get();

        // Pedidos recentes - filtrados por empresa
        $queryPedidosRecentes = Pedido::with('usuario');

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryPedidosRecentes->porEmpresa($empresaId);
        }

        $pedidosRecentes = $queryPedidosRecentes
            ->orderBy('criado_em', 'desc')
            ->limit(10)
            ->get();

        // Vendas por mês - filtradas por empresa
        $queryVendasPorMes = Pedido::selectRaw('MONTH(criado_em) as mes, YEAR(criado_em) as ano, SUM(valor_total) as total')
            ->where('criado_em', '>=', now()->subMonths(6));

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryVendasPorMes->porEmpresa($empresaId);
        }

        $vendasPorMes = $queryVendasPorMes
            ->groupBy('mes', 'ano')
            ->orderBy('ano', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view('admin.dashboard', compact('stats', 'produtosMaisVendidos', 'pedidosRecentes', 'vendasPorMes'));
    }

    /**
     * Lista de produtos
     */
    public function produtos()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryProdutos = Produto::with('categoria');

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryProdutos->porEmpresa($empresaId);
        }

        $produtos = $queryProdutos->orderBy('created_at', 'desc')->paginate(20);

        $queryCategorias = Categoria::ativa();

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryCategorias->porEmpresa($empresaId);
        }

        $categorias = $queryCategorias->get();

        return view('admin.produtos.index', compact('produtos', 'categorias'));
    }

    /**
     * Formulário para criar produto
     */
    public function criarProduto()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryCategorias = Categoria::ativa();

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryCategorias->porEmpresa($empresaId);
        }

        $categorias = $queryCategorias->get();

        return view('admin.produtos.create', compact('categorias'));
    }

    /**
     * Salvar novo produto
     */
    public function salvarProduto(Request $request)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagens.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean',
        ]);

        // Verificar se a categoria pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            $categoria = Categoria::find($request->categoria_id);
            if (!$categoria || $categoria->empresa_id != $empresaId) {
                return back()->with('error', 'Categoria inválida para esta empresa.');
            }
        }

        $data = $request->all();
        $data['ativo'] = $request->has('ativo');

        // Adicionar empresa_id se for administrador de empresa
        if ($empresaId && $adminTipo === 'empresa_admin') {
            $data['empresa_id'] = $empresaId;
        }

        // Upload de imagens
        if ($request->hasFile('imagens')) {
            $imagens = [];
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('produtos', 'public');
                $imagens[] = $path;
            }
            $data['imagem'] = json_encode($imagens);
        }

        $produto = Produto::create($data);

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Formulário para editar produto
     */
    public function editarProduto(Produto $produto)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se o produto pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($produto->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Produto não pertence à sua empresa.');
            }
        }

        $queryCategorias = Categoria::ativa();

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryCategorias->porEmpresa($empresaId);
        }

        $categorias = $queryCategorias->get();

        return view('admin.produtos.edit', compact('produto', 'categorias'));
    }

    /**
     * Atualizar produto
     */
    public function atualizarProduto(Request $request, Produto $produto)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se o produto pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($produto->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Produto não pertence à sua empresa.');
            }
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagens.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean',
        ]);

        // Verificar se a categoria pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            $categoria = Categoria::find($request->categoria_id);
            if (!$categoria || $categoria->empresa_id != $empresaId) {
                return back()->with('error', 'Categoria inválida para esta empresa.');
            }
        }

        $data = $request->all();
        $data['ativo'] = $request->has('ativo');

        // Upload de novas imagens
        if ($request->hasFile('imagens')) {
            $imagens = [];
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('produtos', 'public');
                $imagens[] = $path;
            }
            $data['imagem'] = json_encode($imagens);
        }

        $produto->update($data);

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Excluir produto
     */
    public function excluirProduto(Produto $produto)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se o produto pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($produto->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Produto não pertence à sua empresa.');
            }
        }

        // Verificar se há pedidos com este produto
        if ($produto->itensPedido()->exists()) {
            return back()->with('error', 'Não é possível excluir um produto que possui pedidos.');
        }

        $produto->delete();

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto excluído com sucesso!');
    }

    /**
     * Lista de usuários
     */
    public function usuarios()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryUsuarios = Usuario::withCount(['pedidos', 'avaliacoes']);

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryUsuarios->porEmpresa($empresaId);
        }

        $usuarios = $queryUsuarios->orderBy('nome')->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Detalhes do usuário
     */
    public function detalhesUsuario(Usuario $usuario)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se o usuário pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($usuario->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Usuário não pertence à sua empresa.');
            }
        }

        $pedidos = $usuario->pedidos()->with('itens.produto')->orderBy('criado_em', 'desc')->get();
        $avaliacoes = $usuario->avaliacoes()->with('produto')->orderBy('created_at', 'desc')->get();
        $enderecos = $usuario->enderecos;

        return view('admin.usuarios.show', compact('usuario', 'pedidos', 'avaliacoes', 'enderecos'));
    }

    /**
     * Ativar/desativar usuário
     */
    public function toggleUsuario(Usuario $usuario)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se o usuário pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($usuario->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Usuário não pertence à sua empresa.');
            }
        }

        $usuario->ativo = !$usuario->ativo;
        $usuario->save();

        $status = $usuario->ativo ? 'ativado' : 'desativado';
        return back()->with('success', "Usuário {$status} com sucesso!");
    }

    /**
     * Lista de pedidos
     */
    public function pedidos()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryPedidos = Pedido::with(['usuario', 'endereco', 'cupom']);

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryPedidos->porEmpresa($empresaId);
        }

        $pedidos = $queryPedidos->orderBy('criado_em', 'desc')->paginate(20);

        return view('admin.pedidos.index', compact('pedidos'));
    }

    /**
     * Detalhes do pedido
     */
    public function detalhesPedido(Pedido $pedido)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se o pedido pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($pedido->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Pedido não pertence à sua empresa.');
            }
        }

        $pedido->load(['usuario', 'endereco', 'cupom', 'itens.produto', 'pagamento']);

        return view('admin.pedidos.show', compact('pedido'));
    }

    /**
     * Atualizar status do pedido
     */
    public function atualizarStatusPedido(Request $request, Pedido $pedido)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se o pedido pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($pedido->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Pedido não pertence à sua empresa.');
            }
        }

        $request->validate([
            'status' => 'required|in:pendente,aprovado,enviado,entregue,cancelado',
            'observacoes' => 'nullable|string|max:500',
        ]);

        $pedido->update([
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return back()->with('success', 'Status do pedido atualizado com sucesso!');
    }

    /**
     * Lista de categorias
     */
    public function categorias()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryCategorias = Categoria::withCount('produtos');

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryCategorias->porEmpresa($empresaId);
        }

        $categorias = $queryCategorias->orderBy('nome')->paginate(20);

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Salvar categoria
     */
    public function salvarCategoria(Request $request)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['ativa'] = true;

        // Gerar slug automaticamente baseado no nome
        $data['slug'] = Str::slug($request->nome);

        // Adicionar empresa_id se for administrador de empresa
        if ($empresaId && $adminTipo === 'empresa_admin') {
            $data['empresa_id'] = $empresaId;
        }

        Categoria::create($data);

        return back()->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Atualizar categoria
     */
    public function atualizarCategoria(Request $request, Categoria $categoria)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se a categoria pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($categoria->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Categoria não pertence à sua empresa.');
            }
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
        ]);

        $data = $request->all();

        // Atualizar slug se o nome foi alterado
        if ($request->nome !== $categoria->nome) {
            $data['slug'] = Str::slug($request->nome);
        }

        $categoria->update($data);

        return back()->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Excluir categoria
     */
    public function excluirCategoria(Categoria $categoria)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se a categoria pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($categoria->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Categoria não pertence à sua empresa.');
            }
        }

        // Verificar se há produtos nesta categoria
        if ($categoria->produtos()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui produtos.');
        }

        $categoria->delete();

        return back()->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Ativar/desativar categoria
     */
    public function toggleStatusCategoria(Categoria $categoria)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se a categoria pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($categoria->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Categoria não pertence à sua empresa.');
            }
        }

        $categoria->ativa = !$categoria->ativa;
        $categoria->save();

        $status = $categoria->ativa ? 'ativada' : 'desativada';

        return back()->with('success', "Categoria {$status} com sucesso!");
    }

    /**
     * Lista de avaliações
     */
    public function avaliacoes()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryAvaliacoes = Avaliacao::with(['usuario', 'produto']);

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryAvaliacoes->porEmpresa($empresaId);
        }

        $avaliacoes = $queryAvaliacoes->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.avaliacoes.index', compact('avaliacoes'));
    }

    /**
     * Aprovar avaliação
     */
    public function aprovarAvaliacao(Avaliacao $avaliacao)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se a avaliação pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($avaliacao->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Avaliação não pertence à sua empresa.');
            }
        }

        $avaliacao->update(['aprovada' => true]);

        return back()->with('success', 'Avaliação aprovada com sucesso!');
    }

    /**
     * Rejeitar avaliação
     */
    public function rejeitarAvaliacao(Avaliacao $avaliacao)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Verificar se a avaliação pertence à empresa (se não for super admin)
        if ($empresaId && $adminTipo === 'empresa_admin') {
            if ($avaliacao->empresa_id != $empresaId) {
                abort(403, 'Acesso negado. Avaliação não pertence à sua empresa.');
            }
        }

        $avaliacao->update(['aprovada' => false]);

        return back()->with('success', 'Avaliação rejeitada com sucesso!');
    }

    /**
     * Relatórios
     */
    public function relatorios()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Implementar relatórios filtrados por empresa
        return view('admin.relatorios.index');
    }

    /**
     * Lista de administradores
     */
    public function administradores()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        $queryAdministradores = Administrador::with('usuario');

        if ($empresaId && $adminTipo === 'empresa_admin') {
            $queryAdministradores->porEmpresa($empresaId);
        }

        $administradores = $queryAdministradores->orderBy('nome')->paginate(20);

        return view('admin.administradores.index', compact('administradores'));
    }

    /**
     * Formulário para criar administrador
     */
    public function criarAdministrador()
    {
        return view('admin.administradores.create');
    }

    /**
     * Salvar administrador
     */
    public function salvarAdministrador(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|string|min:6',
            'tipo' => 'required|in:admin,super_admin',
            'empresa_id' => 'nullable|exists:empresas,id',
        ]);

        // Criar usuário
        $usuario = Usuario::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'ativo' => true,
            'empresa_id' => $request->empresa_id,
        ]);

        // Criar administrador
        Administrador::create([
            'id_usuario' => $usuario->id,
            'nome' => $request->nome,
            'email' => $request->email,
            'senha' => Hash::make($request->senha),
            'tipo' => $request->tipo,
            'empresa_id' => $request->empresa_id,
            'ativo' => true,
        ]);

        return redirect()->route('admin.administradores.index')
            ->with('success', 'Administrador criado com sucesso!');
    }

    /**
     * Configurações
     */
    public function configuracoes()
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Implementar configurações filtradas por empresa
        return view('admin.configuracoes.index');
    }

    /**
     * Salvar configurações
     */
    public function salvarConfiguracoes(Request $request)
    {
        $empresaId = request()->get('empresa_id');
        $adminTipo = request()->attributes->get('admin_tipo');

        // Implementar salvamento de configurações por empresa
        return back()->with('success', 'Configurações salvas com sucesso!');
    }
}