import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { AssessmentType, GradingScale, SkillRatingScale, AssessmentRecord, GenericSkillRating, SubjectTermResult } from '../../types';

export const asmKeys = {
  types: { list: () => ['assessment-types', 'list'] as const },
  grading: { list: () => ['grading-scale', 'list'] as const },
  skillRating: { list: () => ['skill-rating-scale', 'list'] as const },
  records: { list: () => ['assessment-records', 'list'] as const },
  skillRatings: { list: () => ['generic-skill-ratings', 'list'] as const },
  termResults: { list: () => ['subject-term-results', 'list'] as const },
};

export function useAssessmentTypes() { return useQuery<AssessmentType[]>({ queryKey: asmKeys.types.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.ASSESSMENT_TYPES); return data.data ?? data; } }); }
export function useCreateAssessmentType() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<AssessmentType>) => { const { data } = await api.post(ENDPOINTS.ASSESSMENT_TYPES, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.types.list() }) }); }
export function useUpdateAssessmentType() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<AssessmentType> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.ASSESSMENT_TYPES}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.types.list() }) }); }
export function useDeleteAssessmentType() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.ASSESSMENT_TYPES}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.types.list() }) }); }

export function useGradingScale() { return useQuery<GradingScale[]>({ queryKey: asmKeys.grading.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.GRADING_SCALE); return data.data ?? data; } }); }
export function useCreateGradingScale() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<GradingScale>) => { const { data } = await api.post(ENDPOINTS.GRADING_SCALE, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.grading.list() }) }); }
export function useUpdateGradingScale() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<GradingScale> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.GRADING_SCALE}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.grading.list() }) }); }
export function useDeleteGradingScale() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.GRADING_SCALE}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.grading.list() }) }); }

export function useSkillRatingScale() { return useQuery<SkillRatingScale[]>({ queryKey: asmKeys.skillRating.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.SKILL_RATING_SCALE); return data.data ?? data; } }); }
export function useCreateSkillRatingScale() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<SkillRatingScale>) => { const { data } = await api.post(ENDPOINTS.SKILL_RATING_SCALE, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.skillRating.list() }) }); }
export function useUpdateSkillRatingScale() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<SkillRatingScale> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.SKILL_RATING_SCALE}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.skillRating.list() }) }); }
export function useDeleteSkillRatingScale() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.SKILL_RATING_SCALE}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.skillRating.list() }) }); }

export function useAssessmentRecords() { return useQuery<AssessmentRecord[]>({ queryKey: asmKeys.records.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.ASSESSMENT_RECORDS); return data.data ?? data; } }); }
export function useCreateAssessmentRecord() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<AssessmentRecord>) => { const { data } = await api.post(ENDPOINTS.ASSESSMENT_RECORDS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.records.list() }) }); }
export function useUpdateAssessmentRecord() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<AssessmentRecord> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.ASSESSMENT_RECORDS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.records.list() }) }); }
export function useDeleteAssessmentRecord() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.ASSESSMENT_RECORDS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.records.list() }) }); }

export function useGenericSkillRatings() { return useQuery<GenericSkillRating[]>({ queryKey: asmKeys.skillRatings.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.GENERIC_SKILL_RATINGS); return data.data ?? data; } }); }
export function useCreateGenericSkillRating() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<GenericSkillRating>) => { const { data } = await api.post(ENDPOINTS.GENERIC_SKILL_RATINGS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.skillRatings.list() }) }); }
export function useUpdateGenericSkillRating() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<GenericSkillRating> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.GENERIC_SKILL_RATINGS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.skillRatings.list() }) }); }
export function useDeleteGenericSkillRating() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.GENERIC_SKILL_RATINGS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.skillRatings.list() }) }); }

export function useSubjectTermResults() { return useQuery<SubjectTermResult[]>({ queryKey: asmKeys.termResults.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.SUBJECT_TERM_RESULTS); return data.data ?? data; } }); }
export function useCreateSubjectTermResult() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<SubjectTermResult>) => { const { data } = await api.post(ENDPOINTS.SUBJECT_TERM_RESULTS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.termResults.list() }) }); }
export function useUpdateSubjectTermResult() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<SubjectTermResult> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.SUBJECT_TERM_RESULTS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.termResults.list() }) }); }
export function useDeleteSubjectTermResult() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.SUBJECT_TERM_RESULTS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: asmKeys.termResults.list() }) }); }
