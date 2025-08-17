<?php

namespace App\Http\Controllers;

use App\Models\Carrinho;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarrinhoController extends Controller
{
    /**
     * Mostrar o carrinho do usuário
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Faça login para acessar seu carrinho');
        }

        $itens = Carrinho::with('produto')
            ->where('id_usuario', Auth::id())
            ->get();

        $total = $itens->sum('subtotal');

        return view('carrinho.index', compact('itens', 'total'));
    }

    /**
     * Adicionar produto ao carrinho
     */
    public function adicionar(Request $request)
    {
        // Verificar autenticação
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faça login para adicionar produtos ao carrinho',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Faça login para adicionar produtos ao carrinho');
        }

        try {
            $request->validate([
                'produto_id' => 'required|exists:produtos,id',
                'quantidade' => 'required|integer|min:1',
            ]);

            $produto = Produto::ativo()->findOrFail($request->produto_id);

            // Verificar se há estoque suficiente
            if ($produto->estoque < $request->quantidade) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantidade solicitada não disponível em estoque'
                    ], 422);
                }
                return back()->with('error', 'Quantidade solicitada não disponível em estoque');
            }

            // Verificar se o produto já está no carrinho
            $itemCarrinho = Carrinho::where('id_usuario', Auth::id())
                ->where('id_produto', $request->produto_id)
                ->first();

            if ($itemCarrinho) {
                // Atualizar quantidade
                $novaQuantidade = $itemCarrinho->quantidade + $request->quantidade;

                if ($produto->estoque < $novaQuantidade) {
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Quantidade solicitada não disponível em estoque'
                        ], 422);
                    }
                    return back()->with('error', 'Quantidade solicitada não disponível em estoque');
                }

                $itemCarrinho->quantidade = $novaQuantidade;
                $itemCarrinho->save();
            } else {
                // Adicionar novo item
                Carrinho::create([
                    'id_usuario' => Auth::id(),
                    'id_produto' => $request->produto_id,
                    'quantidade' => $request->quantidade,
                ]);
            }

            // Se for uma requisição AJAX, retornar JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Produto adicionado ao carrinho!',
                    'quantidade' => Carrinho::where('id_usuario', Auth::id())->sum('quantidade')
                ]);
            }

            return back()->with('success', 'Produto adicionado ao carrinho!');
        } catch (\Exception $e) {
            Log::error('Erro ao adicionar produto ao carrinho: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro interno do servidor. Tente novamente.'
                ], 500);
            }

            return back()->with('error', 'Erro interno do servidor. Tente novamente.');
        }
    }

    /**
     * Atualizar quantidade no carrinho
     */
    public function atualizar(Request $request, Carrinho $item)
    {
        if (!Auth::check() || $item->id_usuario != Auth::id()) {
            return back()->with('error', 'Acesso negado');
        }

        $request->validate([
            'quantidade' => 'required|integer|min:1',
        ]);

        $produto = $item->produto;

        if ($produto->estoque < $request->quantidade) {
            return back()->with('error', 'Quantidade solicitada não disponível em estoque');
        }

        $item->quantidade = $request->quantidade;
        $item->save();

        return back()->with('success', 'Quantidade atualizada!');
    }

    /**
     * Remover item do carrinho
     */
    public function remover(Carrinho $item)
    {
        if (!Auth::check() || $item->id_usuario != Auth::id()) {
            return back()->with('error', 'Acesso negado');
        }

        $item->delete();

        return back()->with('success', 'Item removido do carrinho!');
    }

    /**
     * Limpar carrinho
     */
    public function limpar()
    {
        if (!Auth::check()) {
            return back()->with('error', 'Acesso negado');
        }

        Carrinho::where('id_usuario', Auth::id())->delete();

        return back()->with('success', 'Carrinho limpo!');
    }

    /**
     * Obter quantidade de itens no carrinho (AJAX)
     */
    public function quantidade()
    {
        if (!Auth::check()) {
            return response()->json(['quantidade' => 0]);
        }

        $quantidade = Carrinho::where('id_usuario', Auth::id())->sum('quantidade');

        return response()->json(['quantidade' => $quantidade]);
    }
}
