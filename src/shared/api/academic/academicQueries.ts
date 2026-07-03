import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { AcademicYear, Term, ClassLevel, Stream } from '../../types';

export const academicKeys = {
  years: { all: ['academic-years'] as const, list: () => ['academic-years', 'list'] as const },
  terms: { all: ['terms'] as const, list: () => ['terms', 'list'] as const },
  classLevels: { all: ['class-levels'] as const, list: () => ['class-levels', 'list'] as const },
  streams: { all: ['streams'] as const, list: () => ['streams', 'list'] as const },
};

export function useAcademicYears() {
  return useQuery<AcademicYear[]>({
    queryKey: academicKeys.years.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.ACADEMIC_YEARS);
      return data.data ?? data;
    },
  });
}

export function useCreateAcademicYear() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<AcademicYear>) => {
      const { data } = await api.post(ENDPOINTS.ACADEMIC_YEARS, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: academicKeys.years.list() }),
  });
}

export function useSetCurrentAcademicYear() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (id: number) => {
      const { data } = await api.post(`${ENDPOINTS.ACADEMIC_YEARS}/${id}/set-current`);
      return data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: academicKeys.years.list() }),
  });
}

export function useTerms() {
  return useQuery<Term[]>({
    queryKey: academicKeys.terms.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.TERMS);
      return data.data ?? data;
    },
  });
}

export function useCreateTerm() {
  const qc = useQueryClient();
  return useMutation({
    mutationFn: async (payload: Partial<Term>) => {
      const { data } = await api.post(ENDPOINTS.TERMS, payload);
      return data.data ?? data;
    },
    onSuccess: () => qc.invalidateQueries({ queryKey: academicKeys.terms.list() }),
  });
}

export function useClassLevels() {
  return useQuery<ClassLevel[]>({
    queryKey: academicKeys.classLevels.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.CLASS_LEVELS);
      return data.data ?? data;
    },
  });
}

export function useStreams() {
  return useQuery<Stream[]>({
    queryKey: academicKeys.streams.list(),
    queryFn: async () => {
      const { data } = await api.get(ENDPOINTS.STREAMS);
      return data.data ?? data;
    },
  });
}
