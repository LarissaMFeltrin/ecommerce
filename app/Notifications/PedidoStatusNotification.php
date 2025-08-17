<?php

namespace App\Notifications;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PedidoStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pedido;
    protected $statusAnterior;
    protected $novoStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Pedido $pedido, string $statusAnterior, string $novoStatus)
    {
        $this->pedido = $pedido;
        $this->statusAnterior = $statusAnterior;
        $this->novoStatus = $novoStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'pendente' => 'Pendente',
            'aprovado' => 'Aprovado',
            'em_preparacao' => 'Em Preparação',
            'enviado' => 'Enviado',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
            'reembolsado' => 'Reembolsado'
        ];

        $statusAnteriorLabel = $statusLabels[$this->statusAnterior] ?? $this->statusAnterior;
        $novoStatusLabel = $statusLabels[$this->novoStatus] ?? $this->novoStatus;

        $subject = "Status do Pedido #{$this->pedido->id} Atualizado";

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting("Olá {$notifiable->nome}!")
            ->line("O status do seu pedido #{$this->pedido->id} foi atualizado.")
            ->line("Status anterior: **{$statusAnteriorLabel}**")
            ->line("Novo status: **{$novoStatusLabel}**");

        // Adicionar informações específicas baseadas no status
        switch ($this->novoStatus) {
            case 'aprovado':
                $message->line("Seu pagamento foi aprovado e estamos preparando seu pedido!")
                    ->line("Você receberá atualizações sobre o progresso da entrega.");
                break;

            case 'em_preparacao':
                $message->line("Seu pedido está sendo preparado para envio!")
                    ->line("Em breve você receberá informações sobre o rastreamento.");
                break;

            case 'enviado':
                $message->line("Seu pedido foi enviado!")
                    ->line("Acompanhe a entrega através do código de rastreamento.");
                break;

            case 'entregue':
                $message->line("Seu pedido foi entregue com sucesso!")
                    ->line("Esperamos que você tenha gostado! Não esqueça de avaliar os produtos.");
                break;

            case 'cancelado':
                $message->line("Seu pedido foi cancelado.")
                    ->line("Se você não solicitou o cancelamento, entre em contato conosco.");
                break;

            case 'reembolsado':
                $message->line("Seu pedido foi reembolsado.")
                    ->line("O valor será creditado em até 5 dias úteis.");
                break;
        }

        $message->action('Ver Pedido', route('pedidos.show', $this->pedido))
            ->line("Obrigado por escolher nossa loja!")
            ->salutation('Atenciosamente, Equipe da Loja');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'pedido_id' => $this->pedido->id,
            'status_anterior' => $this->statusAnterior,
            'novo_status' => $this->novoStatus,
            'valor_total' => $this->pedido->valor_total,
            'tipo' => 'status_pedido',
            'mensagem' => "Status do pedido #{$this->pedido->id} alterado de {$this->statusAnterior} para {$this->novoStatus}",
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'pedido_id' => $this->pedido->id,
            'status_anterior' => $this->statusAnterior,
            'novo_status' => $this->novoStatus,
            'valor_total' => $this->pedido->valor_total,
            'tipo' => 'status_pedido',
            'mensagem' => "Status do pedido #{$this->pedido->id} alterado de {$this->statusAnterior} para {$this->novoStatus}",
            'lida' => false,
            'lida_em' => null,
        ];
    }
}