<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Models\Pedido;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PagamentoController extends Controller
{
    /**
     * Processar pagamento do pedido
     */
    public function processar(Request $request, Pedido $pedido)
    {
        // Verificar se o usuário é dono do pedido
        if ($pedido->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se o pedido pode ser pago
        if ($pedido->status !== 'pendente') {
            return back()->with('error', 'Este pedido não pode ser pago.');
        }

        $pagamento = $pedido->pagamentos()->where('status', 'pendente')->first();

        if (!$pagamento) {
            return back()->with('error', 'Pagamento não encontrado.');
        }

        try {
            DB::beginTransaction();

            // Simular processamento do pagamento baseado no tipo
            $resultado = $this->simularProcessamento($pagamento, $request);

            if ($resultado['sucesso']) {
                // Atualizar status do pagamento
                $pagamento->update([
                    'status' => 'aprovado',
                    'referencia_externa' => $resultado['referencia'],
                    'processado_em' => now(),
                ]);

                // Atualizar status do pedido
                $pedido->update(['status' => 'aprovado']);

                // Enviar notificação de sucesso
                $this->enviarNotificacaoPagamento($pedido, 'aprovado');

                DB::commit();

                return redirect()->route('pedidos.show', $pedido)
                    ->with('success', 'Pagamento aprovado com sucesso! Seu pedido está sendo processado.');
            } else {
                // Atualizar status do pagamento para falha
                $pagamento->update([
                    'status' => 'falhou',
                    'referencia_externa' => $resultado['referencia'],
                    'processado_em' => now(),
                ]);

                DB::commit();

                return back()->with('error', 'Falha no processamento do pagamento: ' . $resultado['mensagem']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao processar pagamento: ' . $e->getMessage());

            return back()->with('error', 'Erro interno ao processar pagamento. Tente novamente.');
        }
    }

    /**
     * Simular processamento de pagamento (em produção, integraria com gateway real)
     */
    private function simularProcessamento(Pagamento $pagamento, Request $request)
    {
        $tipo = $pagamento->tipo;
        $referencia = 'SIM_' . Str::random(8) . '_' . time();

        switch ($tipo) {
            case 'pix':
                return $this->simularPix($pagamento, $referencia);

            case 'cartao_credito':
                return $this->simularCartaoCredito($pagamento, $request, $referencia);

            case 'cartao_debito':
                return $this->simularCartaoDebito($pagamento, $request, $referencia);

            case 'boleto':
                return $this->simularBoleto($pagamento, $referencia);

            default:
                return [
                    'sucesso' => false,
                    'mensagem' => 'Forma de pagamento não suportada.',
                    'referencia' => $referencia
                ];
        }
    }

    /**
     * Simular processamento PIX
     */
    private function simularPix(Pagamento $pagamento, string $referencia)
    {
        // Simular 95% de sucesso para PIX
        $sucesso = rand(1, 100) <= 95;

        if ($sucesso) {
            return [
                'sucesso' => true,
                'mensagem' => 'PIX processado com sucesso',
                'referencia' => $referencia
            ];
        }

        return [
            'sucesso' => false,
            'mensagem' => 'PIX expirado ou rejeitado',
            'referencia' => $referencia
        ];
    }

    /**
     * Simular processamento de cartão de crédito
     */
    private function simularCartaoCredito(Pagamento $pagamento, Request $request, string $referencia)
    {
        // Simular validação de cartão
        $numeroCartao = $request->input('numero_cartao', '');
        $cvv = $request->input('cvv', '');
        $validade = $request->input('validade', '');

        if (empty($numeroCartao) || empty($cvv) || empty($validade)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Dados do cartão incompletos',
                'referencia' => $referencia
            ];
        }

        // Simular 90% de sucesso para cartão de crédito
        $sucesso = rand(1, 100) <= 90;

        if ($sucesso) {
            return [
                'sucesso' => true,
                'mensagem' => 'Cartão de crédito aprovado',
                'referencia' => $referencia
            ];
        }

        return [
            'sucesso' => false,
            'mensagem' => 'Cartão de crédito recusado',
            'referencia' => $referencia
        ];
    }

    /**
     * Simular processamento de cartão de débito
     */
    private function simularCartaoDebito(Pagamento $pagamento, string $referencia)
    {
        // Simular 85% de sucesso para cartão de débito
        $sucesso = rand(1, 100) <= 85;

        if ($sucesso) {
            return [
                'sucesso' => true,
                'mensagem' => 'Cartão de débito aprovado',
                'referencia' => $referencia
            ];
        }

        return [
            'sucesso' => false,
            'mensagem' => 'Saldo insuficiente ou cartão recusado',
            'referencia' => $referencia
        ];
    }

    /**
     * Simular processamento de boleto
     */
    private function simularBoleto(Pagamento $pagamento, string $referencia)
    {
        // Boleto sempre é "processado" (aguardando pagamento)
        return [
            'sucesso' => true,
            'mensagem' => 'Boleto gerado com sucesso',
            'referencia' => $referencia
        ];
    }

    /**
     * Webhook para notificações de pagamento (simulado)
     */
    public function webhook(Request $request)
    {
        Log::info('Webhook recebido', $request->all());

        try {
            $tipo = $request->input('tipo');
            $pedidoId = $request->input('pedido_id');
            $status = $request->input('status');
            $referencia = $request->input('referencia');

            $pedido = Pedido::find($pedidoId);
            if (!$pedido) {
                return response()->json(['error' => 'Pedido não encontrado'], 404);
            }

            $pagamento = $pedido->pagamentos()->where('referencia_externa', $referencia)->first();
            if (!$pagamento) {
                return response()->json(['error' => 'Pagamento não encontrado'], 404);
            }

            DB::beginTransaction();

            // Atualizar status do pagamento
            $pagamento->update([
                'status' => $status,
                'processado_em' => now(),
            ]);

            // Atualizar status do pedido baseado no status do pagamento
            switch ($status) {
                case 'aprovado':
                    $pedido->update(['status' => 'aprovado']);
                    $this->enviarNotificacaoPagamento($pedido, 'aprovado');
                    break;

                case 'falhou':
                    $pedido->update(['status' => 'pendente']);
                    $this->enviarNotificacaoPagamento($pedido, 'falhou');
                    break;

                case 'cancelado':
                    $pedido->update(['status' => 'cancelado']);
                    $this->enviarNotificacaoPagamento($pedido, 'cancelado');
                    break;
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no webhook: ' . $e->getMessage());

            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    /**
     * Enviar notificação de pagamento
     */
    private function enviarNotificacaoPagamento(Pedido $pedido, string $status)
    {
        $usuario = $pedido->usuario;

        // Em produção, você enviaria email, SMS, push notification, etc.
        // Por enquanto, vamos apenas logar
        Log::info("Notificação de pagamento enviada para usuário {$usuario->id}: Status {$status}");

        // Aqui você pode implementar:
        // - Envio de email
        // - SMS
        // - Push notification
        // - Notificação no sistema
    }

    /**
     * Mostrar instruções de pagamento
     */
    public function instrucoes(Pedido $pedido)
    {
        // Verificar se o usuário é dono do pedido
        if ($pedido->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        $pagamento = $pedido->pagamentos()->latest()->first();

        if (!$pagamento) {
            return back()->with('error', 'Pagamento não encontrado.');
        }

        return view('pagamentos.instrucoes', compact('pedido', 'pagamento'));
    }

    /**
     * Cancelar pagamento
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

            // Cancelar pagamentos
            $pedido->pagamentos()->update(['status' => 'cancelado']);

            // Atualizar status do pedido
            $pedido->update(['status' => 'cancelado']);

            // Restaurar estoque
            foreach ($pedido->itens as $item) {
                $item->produto->increment('estoque', $item->quantidade);
            }

            // Enviar notificação
            $this->enviarNotificacaoPagamento($pedido, 'cancelado');

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Pagamento cancelado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao cancelar pagamento: ' . $e->getMessage());

            return back()->with('error', 'Erro ao cancelar pagamento. Tente novamente.');
        }
    }

    /**
     * Reembolsar pagamento
     */
    public function reembolsar(Pedido $pedido)
    {
        // Verificar se o usuário é dono do pedido
        if ($pedido->id_usuario !== Auth::id()) {
            abort(403, 'Acesso negado.');
        }

        // Verificar se pode reembolsar
        if ($pedido->status !== 'entregue') {
            return back()->with('error', 'Este pedido não pode ser reembolsado.');
        }

        try {
            DB::beginTransaction();

            // Criar reembolso
            $reembolso = Pagamento::create([
                'id_pedido' => $pedido->id,
                'tipo' => 'reembolso',
                'status' => 'processando',
                'valor' => -$pedido->valor_total, // Valor negativo para reembolso
                'referencia_externa' => 'REEMB_' . Str::random(8) . '_' . time(),
            ]);

            // Atualizar status do pedido
            $pedido->update(['status' => 'reembolsado']);

            // Enviar notificação
            $this->enviarNotificacaoPagamento($pedido, 'reembolsado');

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Solicitação de reembolso enviada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao solicitar reembolso: ' . $e->getMessage());

            return back()->with('error', 'Erro ao solicitar reembolso. Tente novamente.');
        }
    }
}