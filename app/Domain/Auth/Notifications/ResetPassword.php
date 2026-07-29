<?php

namespace App\Domain\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $token,
        public readonly int $expiresInMinutes
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');
        $firstName = $notifiable->name ?? 'there';
        $frontendUrl = config('app.frontend_url');
        $resetUrl = "{$frontendUrl}/reset-password?token={$this->token}&email={$notifiable->email}";

        return (new MailMessage)
            ->subject("Reset Your {$appName} Password")
            ->greeting("Hi {$firstName},")
            ->line("We received a request to reset the password for your {$appName} account.")
            ->line("")
            ->action('Reset Your Password', $resetUrl)
            ->line("")
            ->line("This password reset link will expire in <strong>{$this->expiresInMinutes} minutes</strong>.")
            ->line("")
            ->line("If you did not request a password reset, no further action is required. Your account is safe.")
            ->salutation("— The {$appName} Team");
    }
}
