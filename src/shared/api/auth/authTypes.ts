import type { User } from '../../types';

export interface LoginRequest {
  email: string;
  password: string;
}

export interface RegisterRequest {
  name: string;
  email: string;
  password: string;
  role_id?: number;
  username?: string;
}

export interface AuthResponse {
  user: User;
  token: string;
}

export interface ForgotPasswordRequest {
  email: string;
}

export interface ResetPasswordRequest {
  email: string;
  token: string;
  password: string;
  password_confirmation: string;
}

export interface VerifyEmailRequest {
  user_id: number;
  code: string;
}

export interface ResendVerificationRequest {
  email: string;
}

export interface AuthApiResponse {
  success: boolean;
  code: string;
  message: string;
  user?: User;
  token?: string | null;
  user_id?: number;
  school?: unknown;
}

export interface ApiError {
  message: string;
  errors?: Record<string, string[]>;
}
