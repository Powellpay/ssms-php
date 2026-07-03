import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { Attendance } from '../../types';

export const attKeys = {
  list: () => ['attendance', 'list'] as const,
};

export function useAttendanceList() { return useQuery<Attendance[]>({ queryKey: attKeys.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.ATTENDANCE); return data.data ?? data; } }); }
export function useCreateAttendance() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Attendance>) => { const { data } = await api.post(ENDPOINTS.ATTENDANCE, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: attKeys.list() }) }); }
export function useUpdateAttendance() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Attendance> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.ATTENDANCE}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: attKeys.list() }) }); }
export function useDeleteAttendance() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.ATTENDANCE}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: attKeys.list() }) }); }
