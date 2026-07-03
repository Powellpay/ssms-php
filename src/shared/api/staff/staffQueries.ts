import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../../axiosConfig';
import { ENDPOINTS } from '../../endpoints';
import type { Staff } from '../../../types';

export const staffKeys = {
  all: ['staff'] as const,
  list: () => ['staff', 'list'] as const,
  detail: (id: number) => ['staff', id] as const,
};

export function useStaffList() {
  return useQuery<Staff[]>({
    queryKey: staffKeys.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.STAFF);
      return data.data ?? data;
    },
  });
}

export function useStaff(id: number) {
  return useQuery<Staff>({
    queryKey: staffKeys.detail(id),
    queryFn: async () => {
      const { data } = await api.get(`${ENDPOINTS.STAFF}/${id}`);
      return data.data ?? data;
    },
    enabled: !!id,
  });
}

export function useCreateStaff() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Staff>) => {
      const { data } = await api.post(ENDPOINTS.STAFF, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: staffKeys.list() }),
  });
}

export function useUpdateStaff(id: number) {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Staff>) => {
      const { data } = await api.put(`${ENDPOINTS.STAFF}/${id}`, payload);
      return data.data ?? data;
    },
    onSuccess: () => { qc.invalidateQueries({ queryKey: staffKeys.list() }); qc.invalidateQueries({ queryKey: staffKeys.detail(id) }); },
  });
}

export function useDeleteStaff() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.STAFF}/${id}`); },
    onSuccess: () => qc.invalidateQueries({ queryKey: staffKeys.list() }),
  });
}
