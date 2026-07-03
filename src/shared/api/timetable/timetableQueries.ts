import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { Timetable } from '../../types';

export const ttKeys = {
  list: () => ['timetable', 'list'] as const,
};

export function useTimetableList() { return useQuery<Timetable[]>({ queryKey: ttKeys.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.TIMETABLE); return data.data ?? data; } }); }
export function useCreateTimetable() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Timetable>) => { const { data } = await api.post(ENDPOINTS.TIMETABLE, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: ttKeys.list() }) }); }
export function useUpdateTimetable() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Timetable> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.TIMETABLE}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: ttKeys.list() }) }); }
export function useDeleteTimetable() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.TIMETABLE}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: ttKeys.list() }) }); }
