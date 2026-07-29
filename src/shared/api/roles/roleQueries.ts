import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import type { AxiosError } from 'axios';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { ApiError } from '../auth/authTypes';
import type { Role } from '../../types';

export const roleKeys = {
  all: () => ['roles'] as const,
  list: () => ['roles', 'list'] as const,
  detail: (id: number) => ['roles', id] as const,
};

export function useRoleList() {
  return useQuery<Role[]>({
    queryKey: roleKeys.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.ROLES);
      return data?.data ?? data;
    },
  });
}

export function useRole(id: number) {
  return useQuery<Role>({
    queryKey: roleKeys.detail(id),
    queryFn: async () => {
      const { data } = await api.get(`${ENDPOINTS.ROLES}/${id}`);
      return data?.data ?? data;
    },
    enabled: !!id,
  });
}

export function useCreateRole() {
  const queryClient = useQueryClient();
  return useMutation<Role, AxiosError<ApiError>, Partial<Role>>({
    mutationFn: async (payload) => {
      const { data } = await api.post(ENDPOINTS.ROLES, payload);
      return data?.data ?? data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: roleKeys.list() });
    },
  });
}

export function useUpdateRole() {
  const queryClient = useQueryClient();
  return useMutation<Role, AxiosError<ApiError>, { id: number; payload: Partial<Role> }>({
    mutationFn: async ({ id, payload }) => {
      const { data } = await api.put(`${ENDPOINTS.ROLES}/${id}`, payload);
      return data?.data ?? data;
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: roleKeys.all() });
    },
  });
}

export function useDeleteRole() {
  const queryClient = useQueryClient();
  return useMutation<void, AxiosError<ApiError>, number>({
    mutationFn: async (id) => {
      await api.delete(`${ENDPOINTS.ROLES}/${id}`);
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: roleKeys.list() });
    },
  });
}
