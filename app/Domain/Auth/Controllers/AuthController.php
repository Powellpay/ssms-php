<?php

namespace App\Domain\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Models\School;
use App\Domain\Auth\Models\Role;
use App\Domain\Auth\Requests\UserRequest;
use App\Domain\Auth\Requests\VerifyEmailRequest;
use App\Domain\Auth\Requests\ResendVerificationRequest;
use App\Domain\Auth\Requests\ForgotPasswordRequest;
use App\Domain\Auth\Requests\ResetPasswordRequest;
use App\Domain\Auth\Resources\UserResource;
use App\Domain\Auth\Services\Contracts\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(
        protected UserServiceInterface $userService
    ) {}

    public function register(UserRequest $request): JsonResponse
    {
        $data = $request->validated();

        $school = School::create([
            'name' => $data['school_name'] ?? ($data['name'] . '\'s School'),
            'email' => $data['email'] ?? null,
        ]);

        $data['school_id'] = $school->id;
        $roleSlug = $data['role_slug'] ?? 'administrator';
        $role = Role::findBySlug($roleSlug);
        $data['role_id'] = $role?->id ?? 1;
        unset($data['role_slug'], $data['school_name']);

        $user = $this->userService->create($data);
        $this->userService->sendVerificationCode($user);

        return response()->json([
            'success' => true,
            'code' => 'REGISTRATION_SUCCESS',
            'message' => 'Account created. Please verify your email.',
            'user' => new UserResource($user),
            'token' => null,
            'school' => $school,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = $this->userService->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_CREDENTIALS',
                'message' => 'Invalid credentials',
            ], 401);
        }

        if (!$user->hasVerifiedEmail()) {
            $this->userService->sendVerificationCode($user);

            return response()->json([
                'success' => false,
                'code' => 'EMAIL_NOT_VERIFIED',
                'message' => 'Please verify your email before logging in.',
                'user_id' => $user->id,
            ], 200);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'code' => 'LOGIN_SUCCESS',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function verifyEmail(VerifyEmailRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $user = $this->userService->verifyEmail(
                (int) $validated['user_id'],
                $validated['code']
            );

            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'code' => 'EMAIL_VERIFIED',
                'message' => 'Email verified successfully.',
                'user' => new UserResource($user),
                'token' => $token,
            ]);
        } catch (\RuntimeException $e) {
            $statusCode = $e->getCode() ?: 400;
            return response()->json([
                'success' => false,
                'code' => 'VERIFICATION_FAILED',
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }

    public function resendVerification(ResendVerificationRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->resendVerificationCode(
                $request->input('email')
            );

            return response()->json([
                'success' => true,
                'code' => 'VERIFICATION_SENT',
                'message' => $result['message'],
                'user_id' => $result['user_id'],
            ]);
        } catch (\RuntimeException $e) {
            $statusCode = $e->getCode() ?: 400;
            return response()->json([
                'success' => false,
                'code' => 'RESEND_FAILED',
                'message' => $e->getMessage(),
            ], $statusCode);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function me(Request $request): UserResource
    {
        return new UserResource($request->user()->load('school'));
    }

    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            $result = $this->userService->sendPasswordResetLink(
                $request->input('email')
            );

            return response()->json([
                'success' => true,
                'code' => 'RESET_LINK_SENT',
                'message' => $result['message'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'code' => 'RESET_LINK_FAILED',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $this->userService->resetPassword(
                $request->input('email'),
                $request->input('token'),
                $request->input('password')
            );

            return response()->json([
                'success' => true,
                'code' => 'PASSWORD_RESET',
                'message' => 'Password reset successfully.',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'code' => 'RESET_FAILED',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        }
    }
}
