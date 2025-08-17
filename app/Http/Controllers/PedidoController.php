<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Carrinho;
use App\Models\Endereco;
use App\Models\Cupom;
use App\Models\ItemPedido;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PedidoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pedido::with(['itens.produto', 'endereco', 'cupom'])
            ->porUsuario(Auth::id());

        // Filtro por status
        if ($request->has('status') && $request->status !== '') {
            $query->porStatus($request->status);
        }

        // Ordenação
        $order = $request->get('order', 'recente');
        switch ($order) {
            case 'antigo':
                $query->orderBy('criado_em', 'asc');
                break;
            case 'valor_maior':
                $query->orderBy('valor_total', 'desc');
                break;
            case 'valor_menor':
                $query->orderBy('valor_total', 'asc');
                break;
            case 'recente':
            default:
                $query->orderBy('criado_em', 'desc');
                break;
        }

        $pedidos = $query->paginate(10);

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Verificar se há itens no carrinho
        $itensCarrinho = Carrinho::with('produto.categoria')
            ->porUsuario(Auth::id())
            ->get();

        if ($itensCarrinho->isEmpty()) {
            return redirect()->route('carrinho.index')
                ->with('error', 'Seu carrinho está vazio. Adicione produtos antes de finalizar a compra.');
        }

        // Verificar estoque
        foreach ($itensCarrinho as $item) {
            if ($item->produto->estoque < $item->quantidade) {
                return redirect()->route('carrinho.index')
                    ->with('error', "Produto '{$item->produto->nome}' não possui estoque suficiente.");
            }
        }

        // Buscar endereços do usuário
        $enderecos = Endereco::porUsuario(Auth::id())->get();

        // Buscar cupons válidos
        $cupons = Cupom::ativo()->valido()->get();

        // Calcular totais
        $subtotal = $itensCarrinho->sum('subtotal');
        $frete = 0; // Frete grátis por enquanto
        $total = $subtotal + $frete;

        return view('pedidos.create', compact('itensCarrinho', 'enderecos', 'cupons', 'subtotal', 'frete', 'total'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endereco_id' => 'required|exists:enderecos,id',
            'cupom_codigo' => 'nullable|exists:cupons,codigo',
            'forma_pagamento' => 'required|in:pix,cartao_credito,cartao_debito,boleto',
            'observacoes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verificar se há itens no carrinho
        $itensCarrinho = Carrinho::with('produto')
            ->porUsuario(Auth::id())
            ->get();

        if ($itensCarrinho->isEmpty()) {
            return redirect()->route('carrinho.index')
                ->with('error', 'Seu carrinho está vazio.');
        }

        // Verificar estoque novamente
        foreach ($itensCarrinho as $item) {
            if ($item->produto->estoque < $item->quantidade) {
                return redirect()->route('carrinho.index')
                    ->with('error', "Produto '{$item->produto->nome}' não possui estoque suficiente.");
            }
        }

        try {
            DB::beginTransaction();

            // Buscar cupom se aplicado
            $cupom = null;
            $desconto = 0;
            if ($request->cupom_codigo) {
                $cupom = Cupom::ativo()->where('codigo', $request->cupom_codigo)->first();
                if ($cupom && $cupom->isValido()) {
                    $subtotal = $itensCarrinho->sum('subtotal');
                    $desconto = $cupom->calcularDesconto($subtotal);
                }
            }

            // Calcular totais
            $subtotal = $itensCarrinho->sum('subtotal');
            $frete = 0; // Frete grátis por enquanto
            $total = $subtotal + $frete - $desconto;

            // Criar pedido
            $pedido = Pedido::create([
                'id_usuario' => Auth::id(),
                'id_endereco' => $request->endereco_id,
                'id_cupom' => $cupom ? $cupom->id : null,
                'status' => 'pendente',
                'valor_total' => $total,
                'observacoes' => $request->observacoes,
            ]);

            // Criar itens do pedido
            foreach ($itensCarrinho as $item) {
                ItemPedido::create([
                    'id_pedido' => $pedido->id,
                    'id_produto' => $item->id_produto,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->produto->preco,
                ]);

                // Atualizar estoque
                $item->produto->decrement('estoque', $item->quantidade);
            }

            // Criar pagamento
            Pagamento::create([
                'id_pedido' => $pedido->id,
                'tipo' => $request->forma_pagamento,
                'status' => 'pendente',
                'valor' => $total,
                'referencia_externa' => 'PED' . $pedido->id . '_' . time(),
            ]);

            // Limpar carrinho
            $itensCarrinho->each->delete();

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Pedido realizado com sucesso! Aguardando confirmação do pagamento.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao processar pedido. Tente novamente.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pedido $pedido)
    {
        // Verificar se o usuário é dono do pedido
        if ($pedido->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $pedido->load(['itens.produto.categoria', 'endereco', 'cupom', 'pagamentos']);

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * Cancelar pedido
     */
    public function cancelar(Pedido $pedido)
    {
        // Verificar se o usuário é dono do pedido
        if ($pedido->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se pode cancelar
        if (!in_array($pedido->status, ['pendente', 'aprovado'])) {
            return back()->with('error', 'Este pedido não pode mais ser cancelado.');
        }

        try {
            DB::beginTransaction();

            // Atualizar status
            $pedido->update(['status' => 'cancelado']);

            // Restaurar estoque
            foreach ($pedido->itens as $item) {
                $item->produto->increment('estoque', $item->quantidade);
            }

            // Atualizar status do pagamento
            $pedido->pagamentos()->update(['status' => 'cancelado']);

            DB::commit();

            return back()->with('success', 'Pedido cancelado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cancelar pedido. Tente novamente.');
        }
    }

    /**
     * Aplicar cupom
     */
    public function aplicarCupom(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|exists:cupons,codigo',
        ]);

        $cupom = Cupom::ativo()->where('codigo', $request->codigo)->first();

        if (!$cupom || !$cupom->isValido()) {
            return response()->json([
                'success' => false,
                'message' => 'Cupom inválido ou expirado.'
            ]);
        }

        // Calcular desconto baseado no carrinho atual
        $itensCarrinho = Carrinho::with('produto')
            ->porUsuario(Auth::id())
            ->get();

        $subtotal = $itensCarrinho->sum('subtotal');
        $desconto = $cupom->calcularDesconto($subtotal);

        if ($desconto == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Valor mínimo não atingido para este cupom.'
            ]);
        }

        return response()->json([
            'success' => true,
            'cupom' => [
                'codigo' => $cupom->codigo,
                'descricao' => $cupom->descricao,
                'desconto' => $desconto,
                'desconto_formatado' => 'R$ ' . number_format($desconto, 2, ',', '.'),
            ],
            'novo_total' => $subtotal - $desconto,
            'novo_total_formatado' => 'R$ ' . number_format($subtotal - $desconto, 2, ',', '.'),
        ]);
    }

    /**
     * Buscar endereço por CEP (simulado)
     */
    public function buscarCep(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|size:8',
        ]);

        // Simulação de busca de CEP
        // Em produção, você integraria com uma API de CEP
        $cep = $request->cep;

        // Exemplo de resposta simulada
        $endereco = [
            'rua' => 'Rua Exemplo',
            'bairro' => 'Bairro Exemplo',
            'cidade' => 'Cidade Exemplo',
            'estado' => 'SP',
        ];

        return response()->json([
            'success' => true,
            'endereco' => $endereco,
        ]);
    }

    /**
     * Salvar endereço
     */
    public function salvarEndereco(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|size:8',
            'rua' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:255',
            'bairro' => 'required|string|max:255',
            'cidade' => 'required|string|max:255',
            'estado' => 'required|string|size:2',
            'principal' => 'boolean',
        ]);

        try {
            // Se for principal, desmarcar outros endereços
            if ($request->principal) {
                Endereco::porUsuario(Auth::id())->update(['principal' => false]);
            }

            $endereco = Endereco::create([
                'id_usuario' => Auth::id(),
                'cep' => $request->cep,
                'rua' => $request->rua,
                'numero' => $request->numero,
                'complemento' => $request->complemento,
                'bairro' => $request->bairro,
                'cidade' => $request->cidade,
                'estado' => $request->estado,
                'principal' => $request->principal ?? false,
            ]);

            return response()->json([
                'success' => true,
                'endereco' => $endereco,
                'message' => 'Endereço salvo com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar endereço. Tente novamente.'
            ]);
        }
    }
}
