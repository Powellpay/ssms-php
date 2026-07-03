import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { DisciplineRecord } from '../../types';

export const discKeys = {
  list: () => ['discipline-records', 'list'] as const,
};

export function useDisciplineRecords() { return useQuery<DisciplineRecord[]>({ queryKey: discKeys.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.DISCIPLINE_RECORDS); return data.data ?? data; } }); }
export function useCreateDisciplineRecord() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<DisciplineRecord>) => { const { data } = await api.post(ENDPOINTS.DISCIPLINE_RECORDS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: discKeys.list() }) }); }
export function useUpdateDisciplineRecord() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<DisciplineRecord> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.DISCIPLINE_RECORDS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: discKeys.list() }) }); }
export function useDeleteDisciplineRecord() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.DISCIPLINE_RECORDS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: discKeys.list() }) }); }
