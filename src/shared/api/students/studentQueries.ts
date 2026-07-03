import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { Student, Guardian, Enrollment } from '../../types';

export const studentKeys = {
  all: ['students'] as const,
  list: () => ['students', 'list'] as const,
  detail: (id: number) => ['students', id] as const,
};

export function useStudents() {
  return useQuery<Student[]>({
    queryKey: studentKeys.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.STUDENTS);
      return data.data ?? data;
    },
  });
}

export function useStudent(id: number) {
  return useQuery<Student>({
    queryKey: studentKeys.detail(id),
    queryFn: async () => {
      const { data } = await api.get(`${ENDPOINTS.STUDENTS}/${id}`);
      return data.data ?? data;
    },
    enabled: !!id,
  });
}

export function useCreateStudent() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Student>) => {
      const { data } = await api.post(ENDPOINTS.STUDENTS, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: studentKeys.list() }),
  });
}

export function useUpdateStudent(id: number) {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Student>) => {
      const { data } = await api.put(`${ENDPOINTS.STUDENTS}/${id}`, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: studentKeys.list() }),
  });
}

export function useDeleteStudent() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (id: number) => {
      await api.delete(`${ENDPOINTS.STUDENTS}/${id}`);
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: studentKeys.list() }),
  });
}

export const guardianKeys = {
  all: ['guardians'] as const,
  list: () => ['guardians', 'list'] as const,
};

export function useGuardians() {
  return useQuery<Guardian[]>({
    queryKey: guardianKeys.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.GUARDIANS);
      return data.data ?? data;
    },
  });
}

export function useCreateGuardian() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Guardian>) => {
      const { data } = await api.post(ENDPOINTS.GUARDIANS, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: guardianKeys.list() }),
  });
}

export const enrollmentKeys = {
  all: ['enrollments'] as const,
  list: () => ['enrollments', 'list'] as const,
};

export function useEnrollments() {
  return useQuery<Enrollment[]>({
    queryKey: enrollmentKeys.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.ENROLLMENTS);
      return data.data ?? data;
    },
  });
}

export function useCreateEnrollment() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Enrollment>) => {
      const { data } = await api.post(ENDPOINTS.ENROLLMENTS, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: enrollmentKeys.list() }),
  });
}
