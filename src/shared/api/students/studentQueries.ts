import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { Student, Guardian, Enrollment } from '../../types';

export const studentKeys = {
  all: ['students'] as const,
  list: () => ['students', 'list'] as const,
  detail: (id: number) => ['students', id] as const,
};

export function useStudentList() {
  return useQuery<Student[]>({
    queryKey: studentKeys.list(),
    queryFn: async () => { const { data } = await api.get(ENDPOINTS.STUDENTS); return data.data ?? data; },
  });
}

export function useStudent(id: number) {
  return useQuery<Student>({
    queryKey: studentKeys.detail(id),
    queryFn: async () => { const { data } = await api.get(`${ENDPOINTS.STUDENTS}/${id}`); return data.data ?? data; },
    enabled: !!id,
  });
}

export function useCreateStudent() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Student>) => { const { data } = await api.post(ENDPOINTS.STUDENTS, payload); return data.data ?? data; },
    onSuccess: () => qc.invalidateQueries({ queryKey: studentKeys.list() }),
  });
}

export function useUpdateStudent() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async ({ id, ...payload }: Partial<Student> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.STUDENTS}/${id}`, payload); return data.data ?? data; },
    onSuccess: (_data, vars) => { qc.invalidateQueries({ queryKey: studentKeys.list() }); qc.invalidateQueries({ queryKey: studentKeys.detail(vars.id) }); },
  });
}

export function useDeleteStudent() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.STUDENTS}/${id}`); },
    onSuccess: () => qc.invalidateQueries({ queryKey: studentKeys.list() }),
  });
}

export function useImportStudents() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (formData: FormData) => {
      const { data } = await api.post(ENDPOINTS.STUDENTS_IMPORT, formData);
      return data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: studentKeys.list() }),
  });
}

export function useDownloadTemplate() {
  return useMutation({
    mutationFn: async () => {
      const { data } = await api.get(ENDPOINTS.STUDENTS_IMPORT_TEMPLATE, { responseType: 'blob' });
      return data;
    },
  });
}

export function useGuardianList() {
  return useQuery<Guardian[]>({
    queryKey: ['guardians', 'list'],
    queryFn: async () => { const { data } = await api.get(ENDPOINTS.GUARDIANS); return data.data ?? data; },
  });
}

export function useEnrollmentList() {
  return useQuery<Enrollment[]>({
    queryKey: ['enrollments', 'list'],
    queryFn: async () => { const { data } = await api.get(ENDPOINTS.ENROLLMENTS); return data.data ?? data; },
  });
}
