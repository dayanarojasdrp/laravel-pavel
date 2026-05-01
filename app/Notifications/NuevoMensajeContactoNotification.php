<?php

namespace App\Notifications;

use App\Models\MensajeContacto;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoMensajeContactoNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly MensajeContacto $mensajeContacto) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo mensaje de contacto')
            ->greeting('Nuevo mensaje recibido')
            ->line("Nombre: {$this->mensajeContacto->nombre}")
            ->line("Email: {$this->mensajeContacto->email}")
            ->line("Asunto: {$this->mensajeContacto->asunto}")
            ->line($this->mensajeContacto->mensaje);
    }
}
