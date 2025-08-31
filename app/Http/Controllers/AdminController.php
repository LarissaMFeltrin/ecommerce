<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
        // Estatísticas gerais
        $stats = [
            'total_produtos' => Produto::count(),
            'total_usuarios' => Usuario::count(),
            'total_pedidos' => Pedido::count(),
            'pedidos_pendentes' => Pedido::where('status', 'pendente')->count(),
            'pedidos_aprovados' => Pedido::where('status', 'aprovado')->count(),
            'pedidos_cancelados' => Pedido::where('status', 'cancelado')->count(),
            'total_vendas' => Pedido::where('status', 'aprovado')->sum('valor_total'),
            'avaliacoes_pendentes' => Avaliacao::where('status', 'pendente')->count(),
        ];

        // Produtos mais vendidos
        $produtosMaisVendidos = DB::table('itens_pedido')
            ->join('produtos', 'itens_pedido.id_produto', '=', 'produtos.id')
            ->select('produtos.nome', 'produtos.id', DB::raw('SUM(itens_pedido.quantidade) as total_vendido'))
            ->groupBy('produtos.id', 'produtos.nome')
            ->orderBy('total_vendido', 'desc')
            ->limit(5)
            ->get();

        // Pedidos recentes
        $pedidosRecentes = Pedido::with(['usuario', 'itens.produto'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Vendas por mês (últimos 6 meses)
        $vendasPorMes = Pedido::selectRaw('MONTH(created_at) as mes, YEAR(created_at) as ano, SUM(valor_total) as total')
            ->where('status', 'aprovado')
            ->where('created_at', '>=', now()->subMonths(6))
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
        $produtos = Produto::with('categoria')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categorias = Categoria::ativa()->get();

        return view('admin.produtos.index', compact('produtos', 'categorias'));
    }

    /**
     * Formulário para criar produto
     */
    public function criarProduto()
    {
        $categorias = Categoria::ativa()->get();
        return view('admin.produtos.create', compact('categorias'));
    }

    /**
     * Salvar novo produto
     */
    public function salvarProduto(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagens.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean',
        ]);

        $produto = Produto::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'estoque' => $request->estoque,
            'categoria_id' => $request->categoria_id,
            'ativo' => $request->has('ativo'),
            'slug' => Str::slug($request->nome),
        ]);

        // Upload de imagens
        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('produtos', 'public');
                $produto->imagens()->create([
                    'caminho' => $path,
                    'principal' => false,
                ]);
            }
        }

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto criado com sucesso!');
    }

    /**
     * Formulário para editar produto
     */
    public function editarProduto(Produto $produto)
    {
        $categorias = Categoria::ativa()->get();
        return view('admin.produtos.edit', compact('produto', 'categorias'));
    }

    /**
     * Atualizar produto
     */
    public function atualizarProduto(Request $request, Produto $produto)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'required|string',
            'preco' => 'required|numeric|min:0',
            'estoque' => 'required|integer|min:0',
            'categoria_id' => 'required|exists:categorias,id',
            'imagens.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean',
        ]);

        $produto->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'estoque' => $request->estoque,
            'categoria_id' => $request->categoria_id,
            'ativo' => $request->has('ativo'),
            'slug' => Str::slug($request->nome),
        ]);

        // Upload de novas imagens
        if ($request->hasFile('imagens')) {
            foreach ($request->file('imagens') as $imagem) {
                $path = $imagem->store('produtos', 'public');
                $produto->imagens()->create([
                    'caminho' => $path,
                    'principal' => false,
                ]);
            }
        }

        return redirect()->route('admin.produtos.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Excluir produto
     */
    public function excluirProduto(Produto $produto)
    {
        // Verificar se há pedidos com este produto
        $temPedidos = $produto->itensPedido()->exists();

        if ($temPedidos) {
            return back()->with('error', 'Não é possível excluir um produto que possui pedidos associados.');
        }

        // Excluir imagens
        foreach ($produto->imagens as $imagem) {
            Storage::disk('public')->delete($imagem->caminho);
            $imagem->delete();
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
        $usuarios = Usuario::withCount(['pedidos', 'avaliacoes'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    /**
     * Detalhes do usuário
     */
    public function detalhesUsuario(Usuario $usuario)
    {
        $pedidos = $usuario->pedidos()->with('itens.produto')->orderBy('created_at', 'desc')->get();
        $avaliacoes = $usuario->avaliacoes()->with('produto')->orderBy('created_at', 'desc')->get();
        $enderecos = $usuario->enderecos;

        return view('admin.usuarios.show', compact('usuario', 'pedidos', 'avaliacoes', 'enderecos'));
    }

    /**
     * Ativar/desativar usuário
     */
    public function toggleUsuario(Usuario $usuario)
    {
        $usuario->update(['ativo' => !$usuario->ativo]);

        $status = $usuario->ativo ? 'ativado' : 'desativado';
        return back()->with('success', "Usuário {$status} com sucesso!");
    }

    /**
     * Lista de pedidos
     */
    public function pedidos()
    {
        $pedidos = Pedido::with(['usuario', 'itens.produto'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.pedidos.index', compact('pedidos'));
    }

    /**
     * Detalhes do pedido
     */
    public function detalhesPedido(Pedido $pedido)
    {
        $pedido->load(['usuario', 'itens.produto', 'endereco', 'pagamento']);
        return view('admin.pedidos.show', compact('pedido'));
    }

    /**
     * Atualizar status do pedido
     */
    public function atualizarStatusPedido(Request $request, Pedido $pedido)
    {
        $request->validate([
            'status' => 'required|in:pendente,aprovado,em_preparo,enviado,entregue,cancelado',
            'observacao' => 'nullable|string',
        ]);

        $pedido->update([
            'status' => $request->status,
            'observacao' => $request->observacao,
        ]);

        // Enviar notificação para o usuário
        // TODO: Implementar notificação

        return back()->with('success', 'Status do pedido atualizado com sucesso!');
    }

    /**
     * Lista de categorias
     */
    public function categorias()
    {
        $categorias = Categoria::withCount('produtos')
            ->orderBy('nome')
            ->paginate(20);

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Salvar categoria
     */
    public function salvarCategoria(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias,nome',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        Categoria::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'slug' => Str::slug($request->nome),
            'ativo' => $request->has('ativo'),
        ]);

        return back()->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Atualizar categoria
     */
    public function atualizarCategoria(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias,nome,' . $categoria->id,
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $categoria->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'slug' => Str::slug($request->nome),
            'ativo' => $request->has('ativo'),
        ]);

        return back()->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Excluir categoria
     */
    public function excluirCategoria(Categoria $categoria)
    {
        if ($categoria->produtos()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui produtos.');
        }

        $categoria->delete();

        return back()->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Lista de avaliações pendentes
     */
    public function avaliacoes()
    {
        $avaliacoes = Avaliacao::with(['usuario', 'produto'])
            ->where('status', 'pendente')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.avaliacoes.index', compact('avaliacoes'));
    }

    /**
     * Aprovar avaliação
     */
    public function aprovarAvaliacao(Avaliacao $avaliacao)
    {
        $avaliacao->aprovar();
        return back()->with('success', 'Avaliação aprovada com sucesso!');
    }

    /**
     * Rejeitar avaliação
     */
    public function rejeitarAvaliacao(Avaliacao $avaliacao)
    {
        $avaliacao->rejeitar();
        return back()->with('success', 'Avaliação rejeitada com sucesso!');
    }

    /**
     * Relatórios
     */
    public function relatorios()
    {
        // Vendas por período
        $periodo = request('periodo', 'mes');

        switch ($periodo) {
            case 'semana':
                $inicio = now()->startOfWeek();
                $fim = now()->endOfWeek();
                break;
            case 'mes':
                $inicio = now()->startOfMonth();
                $fim = now()->endOfMonth();
                break;
            case 'ano':
                $inicio = now()->startOfYear();
                $fim = now()->endOfYear();
                break;
            default:
                $inicio = now()->startOfMonth();
                $fim = now()->endOfMonth();
        }

        $vendas = Pedido::where('status', 'aprovado')
            ->whereBetween('created_at', [$inicio, $fim])
            ->sum('valor_total');

        $pedidos = Pedido::where('status', 'aprovado')
            ->whereBetween('created_at', [$inicio, $fim])
            ->count();

        $produtosVendidos = DB::table('itens_pedido')
            ->join('pedidos', 'itens_pedido.id_pedido', '=', 'pedidos.id')
            ->where('pedidos.status', 'aprovado')
            ->whereBetween('pedidos.created_at', [$inicio, $fim])
            ->sum('itens_pedido.quantidade');

        return view('admin.relatorios.index', compact('vendas', 'pedidos', 'produtosVendidos', 'periodo'));
    }

    /**
     * Configurações do sistema
     */
    public function configuracoes()
    {
        return view('admin.configuracoes.index');
    }

    /**
     * Salvar configurações
     */
    public function salvarConfiguracoes(Request $request)
    {
        $request->validate([
            'nome_loja' => 'required|string|max:255',
            'email_contato' => 'required|email',
            'telefone_contato' => 'nullable|string',
            'endereco_loja' => 'nullable|string',
            'frete_gratis_acima' => 'nullable|numeric|min:0',
            'taxa_frete' => 'nullable|numeric|min:0',
        ]);

        // TODO: Implementar sistema de configurações
        // Por enquanto, vamos usar session flash para simular

        return back()->with('success', 'Configurações salvas com sucesso!');
    }

    // ===== ADMINISTRADORES =====

    public function administradores()
    {
        $administradores = Administrador::with('usuario')
            ->orderBy('nome')
            ->paginate(15);

        return view('admin.administradores.index', compact('administradores'));
    }

    public function criarAdministrador()
    {
        return view('admin.administradores.create');
    }

    public function salvarAdministrador(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:usuarios,email',
            'senha' => 'required|string|min:6|confirmed',
            'tipo' => 'required|in:admin,super_admin',
        ]);

        try {
            // Criar usuário
            $usuario = Usuario::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => Hash::make($request->senha),
                'ativo' => true,
                'email_verificado_em' => now(),
            ]);

            // Criar administrador
            Administrador::create([
                'id_usuario' => $usuario->id,
                'nome' => $request->nome,
                'email' => $request->email,
                'senha' => Hash::make($request->senha),
                'tipo' => $request->tipo,
                'ativo' => true,
            ]);

            session()->flash('success', 'Administrador criado com sucesso!');
            return redirect()->route('admin.administradores.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao criar administrador: ' . $e->getMessage());
            return back()->withInput();
        }
    }
}