import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { AcademicYear, Term, ClassLevel, Stream } from '../../types';

export const acaKeys = {
  years: { list: () => ['academic-years', 'list'] as const },
  terms: { list: () => ['terms', 'list'] as const },
  classes: { list: () => ['class-levels', 'list'] as const },
  streams: { list: () => ['streams', 'list'] as const },
};

export function useAcademicYears() { return useQuery<AcademicYear[]>({ queryKey: acaKeys.years.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.ACADEMIC_YEARS); return data.data ?? data; } }); }
export function useCreateAcademicYear() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<AcademicYear>) => { const { data } = await api.post(ENDPOINTS.ACADEMIC_YEARS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.years.list() }) }); }
export function useUpdateAcademicYear() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<AcademicYear> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.ACADEMIC_YEARS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.years.list() }) }); }
export function useDeleteAcademicYear() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.ACADEMIC_YEARS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.years.list() }) }); }

export function useTerms() { return useQuery<Term[]>({ queryKey: acaKeys.terms.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.TERMS); return data.data ?? data; } }); }
export function useCreateTerm() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Term>) => { const { data } = await api.post(ENDPOINTS.TERMS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.terms.list() }) }); }
export function useUpdateTerm() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Term> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.TERMS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.terms.list() }) }); }
export function useDeleteTerm() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.TERMS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.terms.list() }) }); }

export function useClassLevels() { return useQuery<ClassLevel[]>({ queryKey: acaKeys.classes.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.CLASS_LEVELS); return data.data ?? data; } }); }
export function useCreateClassLevel() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<ClassLevel>) => { const { data } = await api.post(ENDPOINTS.CLASS_LEVELS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.classes.list() }) }); }
export function useUpdateClassLevel() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<ClassLevel> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.CLASS_LEVELS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.classes.list() }) }); }
export function useDeleteClassLevel() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.CLASS_LEVELS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.classes.list() }) }); }

export function useStreams() { return useQuery<Stream[]>({ queryKey: acaKeys.streams.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.STREAMS); return data.data ?? data; } }); }
export function useCreateStream() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Stream>) => { const { data } = await api.post(ENDPOINTS.STREAMS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.streams.list() }) }); }
export function useUpdateStream() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Stream> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.STREAMS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.streams.list() }) }); }
export function useDeleteStream() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.STREAMS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: acaKeys.streams.list() }) }); }
