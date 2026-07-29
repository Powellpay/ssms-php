<?php

namespace App\Domain\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
{
    use Queueable;

    public function __construct(
        public readonly string $otp,
        public readonly int $expiresInMinutes
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appName = config('app.name');

        return (new MailMessage)
            ->subject("Verify Your Email Address — {$appName}")
            ->greeting("Welcome to {$appName}!")
            ->line('Use the verification code below to activate your account.')
            ->line("**{$this->otp}**")
            ->line("This code expires in {$this->expiresInMinutes} minutes.")
            ->line('If you did not create an account, no further action is required.');
    }
}
