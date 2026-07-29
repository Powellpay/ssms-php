import { useMutation, useQuery } from '@tanstack/react-query';
import type { AxiosError } from 'axios';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type {
  LoginRequest, RegisterRequest,
  ForgotPasswordRequest, ResetPasswordRequest,
  VerifyEmailRequest, ResendVerificationRequest,
  AuthApiResponse, ApiError,
} from './authTypes';
import type { User } from '../../types';

export const authKeys = {
  all: ['auth'] as const,
  profile: () => ['auth', 'profile'] as const,
};

export function useLogin() {
  return useMutation<AuthApiResponse, AxiosError<ApiError>, LoginRequest>({
    mutationFn: async (credentials) => {
      const { data } = await api.post<AuthApiResponse>(ENDPOINTS.AUTH.LOGIN, credentials);
      if (data.token) {
        localStorage.setItem('token', data.token);
      }
      return data;
    },
  });
}

export function useRegister() {
  return useMutation<AuthApiResponse, AxiosError<ApiError>, RegisterRequest>({
    mutationFn: async (payload) => {
      const { data } = await api.post<AuthApiResponse>(ENDPOINTS.AUTH.REGISTER, payload);
      return data;
    },
  });
}

export function useVerifyEmail() {
  return useMutation<AuthApiResponse, AxiosError<ApiError>, VerifyEmailRequest>({
    mutationFn: async (payload) => {
      const { data } = await api.post<AuthApiResponse>(ENDPOINTS.AUTH.VERIFY_EMAIL, payload);
      if (data.token) {
        localStorage.setItem('token', data.token);
      }
      return data;
    },
  });
}

export function useResendVerification() {
  return useMutation<AuthApiResponse, AxiosError<ApiError>, ResendVerificationRequest>({
    mutationFn: async (payload) => {
      const { data } = await api.post<AuthApiResponse>(ENDPOINTS.AUTH.RESEND_VERIFICATION, payload);
      return data;
    },
  });
}

export function useLogout() {
  return useMutation({
    mutationFn: async () => {
      await api.post(ENDPOINTS.AUTH.LOGOUT);
    },
    onSettled: () => {
      localStorage.removeItem('token');
    },
  });
}

export function useProfile() {
  return useQuery<User>({
    queryKey: authKeys.profile(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.AUTH.ME);
      return data;
    },
    staleTime: 1000 * 60 * 5,
  });
}

export function useForgotPassword() {
  return useMutation({
    mutationFn: async (payload: ForgotPasswordRequest) => {
      const { data } = await api.post(ENDPOINTS.AUTH.FORGOT_PASSWORD, payload);
      return data;
    },
  });
}

export function useResetPassword() {
  return useMutation({
    mutationFn: async (payload: ResetPasswordRequest) => {
      const { data } = await api.post(ENDPOINTS.AUTH.RESET_PASSWORD, payload);
      return data;
    },
  });
}
