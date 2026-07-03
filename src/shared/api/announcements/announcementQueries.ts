import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { Announcement } from '../../types';

export const annKeys = {
  list: () => ['announcements', 'list'] as const,
};

export function useAnnouncements() { return useQuery<Announcement[]>({ queryKey: annKeys.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.ANNOUNCEMENTS); return data.data ?? data; } }); }
export function useCreateAnnouncement() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Announcement>) => { const { data } = await api.post(ENDPOINTS.ANNOUNCEMENTS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: annKeys.list() }) }); }
export function useUpdateAnnouncement() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Announcement> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.ANNOUNCEMENTS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: annKeys.list() }) }); }
export function useDeleteAnnouncement() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.ANNOUNCEMENTS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: annKeys.list() }) }); }
