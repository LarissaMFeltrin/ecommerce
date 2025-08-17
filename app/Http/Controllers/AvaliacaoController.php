<?php

namespace App\Http\Controllers;

use App\Models\Avaliacao;
use App\Models\Produto;
use App\Models\Pedido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AvaliacaoController extends Controller
{
    /**
     * Mostrar formulário para criar avaliação
     */
    public function create(Produto $produto)
    {
        // Verificar se o usuário comprou o produto
        $pedido = Pedido::where('id_usuario', Auth::id())
            ->whereHas('itens', function ($query) use ($produto) {
                $query->where('id_produto', $produto->id);
            })
            ->where('status', 'entregue')
            ->first();

        if (!$pedido) {
            return back()->with('error', 'Você precisa ter comprado e recebido este produto para avaliá-lo.');
        }

        // Verificar se já avaliou
        $avaliacaoExistente = Avaliacao::where('id_usuario', Auth::id())
            ->where('id_produto', $produto->id)
            ->first();

        if ($avaliacaoExistente) {
            return back()->with('error', 'Você já avaliou este produto.');
        }

        return view('avaliacoes.create', compact('produto'));
    }

    /**
     * Armazenar nova avaliação
     */
    public function store(Request $request, Produto $produto)
    {
        $validator = Validator::make($request->all(), [
            'nota' => 'required|integer|min:1|max:5',
            'titulo' => 'required|string|max:100',
            'comentario' => 'required|string|min:10|max:1000',
            'recomenda' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verificar se o usuário comprou o produto
        $pedido = Pedido::where('id_usuario', Auth::id())
            ->whereHas('itens', function ($query) use ($produto) {
                $query->where('id_produto', $produto->id);
            })
            ->where('status', 'entregue')
            ->first();

        if (!$pedido) {
            return back()->with('error', 'Você precisa ter comprado e recebido este produto para avaliá-lo.');
        }

        // Verificar se já avaliou
        $avaliacaoExistente = Avaliacao::where('id_usuario', Auth::id())
            ->where('id_produto', $produto->id)
            ->first();

        if ($avaliacaoExistente) {
            return back()->with('error', 'Você já avaliou este produto.');
        }

        try {
            $avaliacao = Avaliacao::create([
                'id_usuario' => Auth::id(),
                'id_produto' => $produto->id,
                'nota' => $request->nota,
                'titulo' => $request->titulo,
                'comentario' => $request->comentario,
                'recomenda' => $request->recomenda ?? false,
                'status' => 'pendente', // Em produção, pode ser 'aprovado' automaticamente
            ]);

            // Atualizar média de avaliações do produto
            $this->atualizarMediaProduto($produto);

            return redirect()->route('produtos.show', $produto)
                ->with('success', 'Avaliação enviada com sucesso! Aguardando aprovação.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar avaliação. Tente novamente.');
        }
    }

    /**
     * Mostrar formulário para editar avaliação
     */
    public function edit(Avaliacao $avaliacao)
    {
        // Verificar se o usuário é dono da avaliação
        if ($avaliacao->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se pode editar (apenas avaliações pendentes ou aprovadas)
        if (!in_array($avaliacao->status, ['pendente', 'aprovado'])) {
            return back()->with('error', 'Esta avaliação não pode mais ser editada.');
        }

        return view('avaliacoes.edit', compact('avaliacao'));
    }

    /**
     * Atualizar avaliação
     */
    public function update(Request $request, Avaliacao $avaliacao)
    {
        // Verificar se o usuário é dono da avaliação
        if ($avaliacao->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se pode editar
        if (!in_array($avaliacao->status, ['pendente', 'aprovado'])) {
            return back()->with('error', 'Esta avaliação não pode mais ser editada.');
        }

        $validator = Validator::make($request->all(), [
            'nota' => 'required|integer|min:1|max:5',
            'titulo' => 'required|string|max:100',
            'comentario' => 'required|string|min:10|max:1000',
            'recomenda' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $avaliacao->update([
                'nota' => $request->nota,
                'titulo' => $request->titulo,
                'comentario' => $request->comentario,
                'recomenda' => $request->recomenda ?? false,
                'status' => 'pendente', // Volta para pendente após edição
            ]);

            // Atualizar média de avaliações do produto
            $this->atualizarMediaProduto($avaliacao->produto);

            return redirect()->route('produtos.show', $avaliacao->produto)
                ->with('success', 'Avaliação atualizada com sucesso! Aguardando aprovação.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao atualizar avaliação. Tente novamente.');
        }
    }

    /**
     * Excluir avaliação
     */
    public function destroy(Avaliacao $avaliacao)
    {
        // Verificar se o usuário é dono da avaliação
        if ($avaliacao->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se pode excluir
        if ($avaliacao->status === 'rejeitada') {
            return back()->with('error', 'Esta avaliação não pode ser excluída.');
        }

        try {
            $produto = $avaliacao->produto;
            $avaliacao->delete();

            // Atualizar média de avaliações do produto
            $this->atualizarMediaProduto($produto);

            return redirect()->route('produtos.show', $produto)
                ->with('success', 'Avaliação excluída com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao excluir avaliação. Tente novamente.');
        }
    }

    /**
     * Marcar avaliação como útil
     */
    public function marcarUtil(Avaliacao $avaliacao)
    {
        try {
            $avaliacao->increment('votos_uteis');

            return response()->json([
                'success' => true,
                'votos_uteis' => $avaliacao->votos_uteis + 1
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como útil.'
            ]);
        }
    }

    /**
     * Marcar avaliação como não útil
     */
    public function marcarNaoUtil(Avaliacao $avaliacao)
    {
        try {
            $avaliacao->increment('votos_nao_uteis');

            return response()->json([
                'success' => true,
                'votos_nao_uteis' => $avaliacao->votos_nao_uteis + 1
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao marcar como não útil.'
            ]);
        }
    }

    /**
     * Responder a uma avaliação (para administradores)
     */
    public function responder(Request $request, Avaliacao $avaliacao)
    {
        // Verificar se é administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $validator = Validator::make($request->all(), [
            'resposta' => 'required|string|min:10|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $avaliacao->update([
                'resposta_admin' => $request->resposta,
                'respondido_em' => now(),
            ]);

            return back()->with('success', 'Resposta enviada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao enviar resposta. Tente novamente.');
        }
    }

    /**
     * Aprovar avaliação (para administradores)
     */
    public function aprovar(Avaliacao $avaliacao)
    {
        // Verificar se é administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        try {
            $avaliacao->update(['status' => 'aprovado']);

            // Atualizar média de avaliações do produto
            $this->atualizarMediaProduto($avaliacao->produto);

            return back()->with('success', 'Avaliação aprovada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao aprovar avaliação. Tente novamente.');
        }
    }

    /**
     * Rejeitar avaliação (para administradores)
     */
    public function rejeitar(Avaliacao $avaliacao)
    {
        // Verificar se é administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        try {
            $avaliacao->update(['status' => 'rejeitada']);

            // Atualizar média de avaliações do produto
            $this->atualizarMediaProduto($avaliacao->produto);

            return back()->with('success', 'Avaliação rejeitada com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao rejeitar avaliação. Tente novamente.');
        }
    }

    /**
     * Atualizar média de avaliações do produto
     */
    private function atualizarMediaProduto(Produto $produto)
    {
        $avaliacoesAprovadas = $produto->avaliacoes()
            ->where('status', 'aprovado')
            ->get();

        if ($avaliacoesAprovadas->count() > 0) {
            $media = $avaliacoesAprovadas->avg('nota');
            $total = $avaliacoesAprovadas->count();

            $produto->update([
                'avaliacao_media' => round($media, 1),
                'avaliacao_total' => $total,
            ]);
        } else {
            $produto->update([
                'avaliacao_media' => 0,
                'avaliacao_total' => 0,
            ]);
        }
    }

    /**
     * Listar avaliações do usuário
     */
    public function minhasAvaliacoes()
    {
        $avaliacoes = Avaliacao::with('produto')
            ->where('id_usuario', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('avaliacoes.minhas', compact('avaliacoes'));
    }

    /**
     * Listar todas as avaliações (para administradores)
     */
    public function todas()
    {
        // Verificar se é administrador
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Acesso negado.');
        }

        $avaliacoes = Avaliacao::with(['produto', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('avaliacoes.todas', compact('avaliacoes'));
    }
}