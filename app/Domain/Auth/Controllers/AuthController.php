<?php

namespace App\Domain\Auth\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Auth\Models\School;
use App\Domain\Auth\Requests\UserRequest;
use App\Domain\Auth\Requests\VerifyEmailRequest;
use App\Domain\Auth\Requests\ResendVerificationRequest;
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
        $data['role_id'] ??= 1;

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
        return new UserResource($request->user());
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        return response()->json(['message' => 'Password reset link sent']);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        return response()->json(['message' => 'Password reset successfully']);
    }
}
