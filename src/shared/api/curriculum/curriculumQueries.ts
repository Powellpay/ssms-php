import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { Subject, ClassSubject, SubjectTeacher, CurriculumTheme, LearningOutcome, GenericSkill } from '../../types';

export const curKeys = {
  subjects: { list: () => ['subjects', 'list'] as const },
  classSubjects: { list: () => ['class-subjects', 'list'] as const },
  subjectTeachers: { list: () => ['subject-teachers', 'list'] as const },
  themes: { list: () => ['curriculum-themes', 'list'] as const },
  outcomes: { list: () => ['learning-outcomes', 'list'] as const },
  skills: { list: () => ['generic-skills', 'list'] as const },
};

export function useSubjects() { return useQuery<Subject[]>({ queryKey: curKeys.subjects.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.SUBJECTS); return data.data ?? data; } }); }
export function useCreateSubject() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Subject>) => { const { data } = await api.post(ENDPOINTS.SUBJECTS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.subjects.list() }) }); }
export function useUpdateSubject() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Subject> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.SUBJECTS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.subjects.list() }) }); }
export function useDeleteSubject() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.SUBJECTS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.subjects.list() }) }); }

export function useClassSubjects() { return useQuery<ClassSubject[]>({ queryKey: curKeys.classSubjects.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.CLASS_SUBJECTS); return data.data ?? data; } }); }
export function useCreateClassSubject() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<ClassSubject>) => { const { data } = await api.post(ENDPOINTS.CLASS_SUBJECTS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.classSubjects.list() }) }); }
export function useUpdateClassSubject() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<ClassSubject> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.CLASS_SUBJECTS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.classSubjects.list() }) }); }
export function useDeleteClassSubject() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.CLASS_SUBJECTS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.classSubjects.list() }) }); }

export function useSubjectTeachers() { return useQuery<SubjectTeacher[]>({ queryKey: curKeys.subjectTeachers.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.SUBJECT_TEACHERS); return data.data ?? data; } }); }
export function useCreateSubjectTeacher() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<SubjectTeacher>) => { const { data } = await api.post(ENDPOINTS.SUBJECT_TEACHERS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.subjectTeachers.list() }) }); }
export function useUpdateSubjectTeacher() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<SubjectTeacher> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.SUBJECT_TEACHERS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.subjectTeachers.list() }) }); }
export function useDeleteSubjectTeacher() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.SUBJECT_TEACHERS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.subjectTeachers.list() }) }); }

export function useCurriculumThemes() { return useQuery<CurriculumTheme[]>({ queryKey: curKeys.themes.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.CURRICULUM_THEMES); return data.data ?? data; } }); }
export function useCreateCurriculumTheme() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<CurriculumTheme>) => { const { data } = await api.post(ENDPOINTS.CURRICULUM_THEMES, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.themes.list() }) }); }
export function useUpdateCurriculumTheme() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<CurriculumTheme> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.CURRICULUM_THEMES}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.themes.list() }) }); }
export function useDeleteCurriculumTheme() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.CURRICULUM_THEMES}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.themes.list() }) }); }

export function useLearningOutcomes() { return useQuery<LearningOutcome[]>({ queryKey: curKeys.outcomes.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.LEARNING_OUTCOMES); return data.data ?? data; } }); }
export function useCreateLearningOutcome() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<LearningOutcome>) => { const { data } = await api.post(ENDPOINTS.LEARNING_OUTCOMES, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.outcomes.list() }) }); }
export function useUpdateLearningOutcome() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<LearningOutcome> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.LEARNING_OUTCOMES}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.outcomes.list() }) }); }
export function useDeleteLearningOutcome() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.LEARNING_OUTCOMES}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.outcomes.list() }) }); }

export function useGenericSkills() { return useQuery<GenericSkill[]>({ queryKey: curKeys.skills.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.GENERIC_SKILLS); return data.data ?? data; } }); }
export function useCreateGenericSkill() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<GenericSkill>) => { const { data } = await api.post(ENDPOINTS.GENERIC_SKILLS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.skills.list() }) }); }
export function useUpdateGenericSkill() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<GenericSkill> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.GENERIC_SKILLS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.skills.list() }) }); }
export function useDeleteGenericSkill() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.GENERIC_SKILLS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: curKeys.skills.list() }) }); }
