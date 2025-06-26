<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('E-Mail-Adresse bestätigen - Fahrtkosten-Rechner')
            ->greeting('Hallo!')
            ->line('Willkommen beim Fahrtkosten-Rechner! Bitte bestätigen Sie Ihre E-Mail-Adresse, um Ihr Konto zu aktivieren.')
            ->action('E-Mail-Adresse bestätigen', $verificationUrl)
            ->line('Falls Sie kein Konto erstellt haben, ist keine weitere Aktion erforderlich.')
            ->salutation('Mit freundlichen Grüßen,
Das Fahrtkosten-Rechner Team');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
