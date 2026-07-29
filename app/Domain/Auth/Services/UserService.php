<?php

namespace App\Domain\Auth\Services;

use App\Domain\Auth\Models\EmailVerification;
use App\Domain\Auth\Models\User;
use App\Domain\Auth\Notifications\ResetPassword;
use App\Domain\Auth\Notifications\VerifyEmail;
use App\Domain\Auth\Repositories\Contracts\UserRepositoryInterface;
use App\Domain\Auth\Services\Contracts\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
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

        $user->sendEmailVerificationNotification(new VerifyEmail($otp, 30));

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

        $user->sendEmailVerificationNotification(new ResetPassword($token, 60));

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
