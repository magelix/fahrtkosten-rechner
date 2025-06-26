<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Passwort zurücksetzen - Fahrtkosten-Rechner')
            ->greeting('Hallo!')
            ->line('Sie erhalten diese E-Mail, weil wir eine Anfrage zum Zurücksetzen des Passworts für Ihr Konto erhalten haben.')
            ->action('Passwort zurücksetzen', $url)
            ->line('Dieser Link zum Zurücksetzen des Passworts läuft in 60 Minuten ab.')
            ->line('Falls Sie kein Passwort-Reset angefordert haben, ist keine weitere Aktion erforderlich.')
            ->salutation('Mit freundlichen Grüßen,
Das Fahrtkosten-Rechner Team');
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
