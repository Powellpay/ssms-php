import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { ReportCard } from '../../types';

export const repKeys = {
  list: () => ['report-cards', 'list'] as const,
  detail: (id: number) => ['report-cards', id] as const,
};

export function useReportCards() { return useQuery<ReportCard[]>({ queryKey: repKeys.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.REPORT_CARDS); return data.data ?? data; } }); }
export function useCreateReportCard() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<ReportCard>) => { const { data } = await api.post(ENDPOINTS.REPORT_CARDS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: repKeys.list() }) }); }
export function useUpdateReportCard() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<ReportCard> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.REPORT_CARDS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: repKeys.list() }) }); }
export function useDeleteReportCard() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.REPORT_CARDS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: repKeys.list() }) }); }

export function useReportCard(id: number) {
  return useQuery<ReportCard>({
    queryKey: repKeys.detail(id),
    queryFn: async () => { const { data } = await api.get(`${ENDPOINTS.REPORT_CARDS}/${id}`); return data.data ?? data; },
    enabled: !!id,
  });
}

export function useGenerateReportCard() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: { student_id: number; term_id: number }) => {
      const { data } = await api.post(`${ENDPOINTS.REPORT_CARDS}/generate`, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: repKeys.list() }),
  });
}
