import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import type { AxiosError } from 'axios';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { ApiError } from '../auth/authTypes';
import type { User } from '../../types';

export const accountKeys = {
  profile: () => ['account', 'profile'] as const,
};

export function useProfile() {
  return useQuery<User>({
    queryKey: accountKeys.profile(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.AUTH.ME);
      return data;
    },
  });
}

export function useUpdateProfile() {
  const queryClient = useQueryClient();
  return useMutation<User, AxiosError<ApiError>, FormData>({
    mutationFn: async (formData) => {
      const { data } = await api.post(ENDPOINTS.AUTH.PROFILE, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: accountKeys.profile() });
    },
  });
}

export function useUpdatePassword() {
  const queryClient = useQueryClient();
  return useMutation<void, AxiosError<ApiError>, { current_password: string; new_password: string; new_password_confirmation: string }>({
    mutationFn: async (payload) => {
      const { data } = await api.put(ENDPOINTS.AUTH.PASSWORD, payload);
      return data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: accountKeys.profile() });
    },
  });
}
