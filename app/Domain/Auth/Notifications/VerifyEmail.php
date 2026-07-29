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
        $firstName = $notifiable->name ?? 'there';
        $frontendUrl = config('app.frontend_url');
        $verifyUrl = "{$frontendUrl}/verify-email";

        return (new MailMessage)
            ->subject("Activate Your {$appName} Account")
            ->greeting("Hi {$firstName},")
            ->line("Thank you for creating a {$appName} account. Use the verification code below to activate your account.")
            ->line("")
            ->line("<div style=\"text-align: center; margin: 24px 0;\">")
            ->line("<span style=\"font-size: 32px; font-weight: 700; letter-spacing: 8px; background: #f3f4f6; padding: 12px 24px; border-radius: 8px; color: #1e293b;\">{$this->otp}</span>")
            ->line("</div>")
            ->line("<p style=\"text-align: center; color: #64748b; font-size: 13px;\">Enter this code on the verification page to activate your account.</p>")
            ->line("")
            ->line("Or click the button below to open the verification page:")
            ->action('Activate Your Account', $verifyUrl)
            ->line("")
            ->line("This code expires in <strong>{$this->expiresInMinutes} minutes</strong>.")
            ->line("")
            ->line("If you did not create this account, you can safely ignore this email.")
            ->salutation("— The {$appName} Team");
    }
}
