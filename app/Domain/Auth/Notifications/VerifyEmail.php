<?php

namespace App\Domain\Auth\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;

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

    public function toMail(object $notifiable): void
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

        Mail::send('emails.standard', [
            'title' => 'Activate Your Account',
            'mailBody' => '
                <p>Hello <strong>' . e($notifiable->name) . '</strong>,</p>
                <p>Thank you for creating a ' . config('brand.name') . ' account. Use the verification code below to activate your account.</p>
                <p style="font-size:14px; color:#64748b;">Enter this code on the verification page to activate your account.</p>
            ',
            'otp' => $this->otp,
            'ctaUrl' => $frontendUrl . '/verify-email',
            'ctaLabel' => 'Activate Your Account',
            'tip' => 'This code expires in ' . $this->expiresInMinutes . ' minutes. If you did not create this account, you can safely ignore this email.',
            'isHtml' => true,
        ], function ($message) use ($notifiable) {
            $message->to($notifiable->email)
                    ->subject('Activate Your ' . config('brand.name') . ' Account');
        });
    }
}
