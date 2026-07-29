<?php

namespace App\Domain\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

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

    public function toMail(object $notifiable): void
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $resetUrl = $frontendUrl . '/reset-password?token=' . $this->token . '&email=' . urlencode($notifiable->email);

        Mail::send('emails.standard', [
            'title' => 'Reset Your Password',
            'mailBody' => '
                <p>Hello <strong>' . e($notifiable->name) . '</strong>,</p>
                <p>You are receiving this email because we received a password reset request for your ' . config('brand.name') . ' account.</p>
                <p style="font-size:14px; color:#64748b;">This password reset link will expire in ' . $this->expiresInMinutes . ' minutes.</p>
                <p style="font-size:14px; color:#64748b;">If you did not request a password reset, no further action is required. Your account is safe.</p>
            ',
            'ctaUrl' => $resetUrl,
            'ctaLabel' => 'Reset My Password',
            'tip' => 'Never share this email with anyone. ' . config('brand.name') . ' will never ask for your password.',
            'isHtml' => true,
        ], function ($message) use ($notifiable) {
            $message->to($notifiable->email)
                    ->subject('Reset Your ' . config('brand.name') . ' Password');
        });
    }
}
