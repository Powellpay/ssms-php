import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { Attendance } from '../../types';

export const attKeys = {
  list: () => ['attendance', 'list'] as const,
  register: (termId: number, date: string, streamId: number) =>
    ['attendance', 'register', termId, date, streamId] as const,
};

export function useAttendanceList() { return useQuery<Attendance[]>({ queryKey: attKeys.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.ATTENDANCE); return data.data ?? data; } }); }
export function useCreateAttendance() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Attendance>) => { const { data } = await api.post(ENDPOINTS.ATTENDANCE, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: attKeys.list() }) }); }
export function useUpdateAttendance() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Attendance> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.ATTENDANCE}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: attKeys.list() }) }); }
export function useDeleteAttendance() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.ATTENDANCE}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: attKeys.list() }) }); }

export function useAttendanceRegister(termId: number, date: string, streamId: number) {
  return useQuery<AttendanceRegisterRecord[]>({
    queryKey: attKeys.register(termId, date, streamId),
    queryFn: async () => {
      const { data } = await api.get(`${ENDPOINTS.ATTENDANCE}/register`, {
        params: { term_id: termId, attendance_date: date, stream_id: streamId },
      });
      return data.data ?? data;
    },
    enabled: !!termId && !!date && !!streamId,
  });
}

export function useBulkMarkAttendance() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: {
      term_id: number;
      attendance_date: string;
      stream_id: number;
      records: Array<{ student_id: number; status: string }>;
    }) => {
      const { data } = await api.post(`${ENDPOINTS.ATTENDANCE}/register`, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: ['attendance'] }),
  });
}

export interface AttendanceRegisterRecord {
  student_id: number;
  first_name: string;
  last_name: string;
  admission_no: string;
  status: 'Present' | 'Absent' | 'Late' | 'Excused' | '';
}
