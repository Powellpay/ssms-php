<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\EmailVerification;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Domain\Auth\Services\Contracts\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService implements UserServiceInterface
{
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {}

    public function all(): Collection
    {
        return $this->userRepository->all();
    }

    public function find(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['modules'] = ModuleAccessService::ALL_MODULES;
        return $this->userRepository->create($data);
    }

    public function update(int $id, array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->userRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->userRepository->delete($id);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->userRepository->findByEmail($email);
    }

    public function authenticate(string $username, string $password): ?User
    {
        $user = $this->userRepository->findByEmail($username);

        if (!$user || !Hash::check($password, $user->password)) {
            return null;
        }

        return $user;
    }

    public function findByRole(string $roleName): Collection
    {
        return User::whereHas('role', function ($query) use ($roleName) {
            $query->where('role_name', $roleName);
        })->get();
    }

    public function sendVerificationCode(User $user): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(64);
        $expiresAt = now()->addMinutes(30);

        EmailVerification::where('user_id', $user->id)
            ->where('type', 'email_verification')
            ->whereNull('used_at')
            ->delete();

        EmailVerification::create([
            'user_id' => $user->id,
            'token_hash' => Hash::make($token),
            'otp_code' => $otp,
            'type' => 'email_verification',
            'expires_at' => $expiresAt,
        ]);

        try {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            Mail::send('emails.standard', [
                'title' => 'Activate Your Account',
                'mailBody' => '
                    <p>Hello <strong>' . e($user->name) . '</strong>,</p>
                    <p>Thank you for creating a ' . config('brand.name') . ' account. Use the verification code below to activate your account.</p>
                    <p style="font-size:14px; color:#64748b;">Enter this code on the verification page to activate your account.</p>
                ',
                'logoUrl' => url('/logo.png'),
                'otp' => $otp,
                'ctaUrl' => $frontendUrl . '/verify-email',
                'ctaLabel' => 'Activate Your Account',
                'tip' => 'This code expires in 30 minutes. If you did not create this account, you can safely ignore this email.',
                'isHtml' => true,
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Activate Your ' . config('brand.name') . ' Account');
            });
        } catch (\Throwable $e) {
            Log::warning('Verification email failed to send for user ' . $user->id . ': ' . $e->getMessage());
        }

        return $otp;
    }

    public function verifyEmail(int $userId, string $code): User
    {
        $user = $this->find($userId);

        if (!$user) {
            throw new \RuntimeException('User not found.', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return $user;
        }

        $record = EmailVerification::where('user_id', $userId)
            ->where('type', 'email_verification')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->where('otp_code', $code)
            ->first();

        if (!$record) {
            throw new \RuntimeException('Invalid or expired verification code.', 400);
        }

        $record->markAsUsed();
        $user->markEmailAsVerified();

        return $user;
    }

    public function resendVerificationCode(string $email): array
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            throw new \RuntimeException('User not found.', 404);
        }

        if ($user->hasVerifiedEmail()) {
            throw new \RuntimeException('Email already verified.', 400);
        }

        $otp = $this->sendVerificationCode($user);

        return [
            'user_id' => $user->id,
            'message' => 'Verification code sent successfully.',
        ];
    }

    public function sendPasswordResetLink(string $email): array
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            return ['message' => 'If the email exists, a reset link has been sent.'];
        }

        $token = Str::random(64);
        $expiresAt = now()->addMinutes(60);

        EmailVerification::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->delete();

        EmailVerification::create([
            'user_id' => $user->id,
            'token_hash' => Hash::make($token),
            'otp_code' => '',
            'type' => 'password_reset',
            'expires_at' => $expiresAt,
        ]);

        try {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $resetUrl = $frontendUrl . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);
            Mail::send('emails.standard', [
                'title' => 'Reset Your Password',
                'mailBody' => '
                    <p>Hello <strong>' . e($user->name) . '</strong>,</p>
                    <p>You are receiving this email because we received a password reset request for your ' . config('brand.name') . ' account.</p>
                    <p style="font-size:14px; color:#64748b;">This password reset link will expire in 60 minutes.</p>
                    <p style="font-size:14px; color:#64748b;">If you did not request a password reset, no further action is required. Your account is safe.</p>
                ',
                'logoUrl' => url('/logo.png'),
                'ctaUrl' => $resetUrl,
                'ctaLabel' => 'Reset My Password',
                'tip' => 'Never share this email with anyone. ' . config('brand.name') . ' will never ask for your password.',
                'isHtml' => true,
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Reset Your ' . config('brand.name') . ' Password');
            });
        } catch (\Throwable $e) {
            Log::warning('Password reset email failed to send for user ' . $user->id . ': ' . $e->getMessage());
        }

        return ['message' => 'If the email exists, a reset link has been sent.'];
    }

    public function resetPassword(string $email, string $token, string $password): User
    {
        $user = $this->findByEmail($email);
        if (!$user) {
            throw new \RuntimeException('Invalid or expired reset link.', 400);
        }

        $record = EmailVerification::where('user_id', $user->id)
            ->where('type', 'password_reset')
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$record || !Hash::check($token, $record->token_hash)) {
            throw new \RuntimeException('Invalid or expired reset link.', 400);
        }

        $record->markAsUsed();
        $user->update(['password' => Hash::make($password)]);

        return $user;
    }
}
