import { useMemo } from 'react';
import { useStudentList } from '../../shared/api/students/studentQueries';
import { useStaffList } from '../../shared/api/staff/staffQueries';
import { useStreams, useClassLevels, useTerms, useAcademicYears } from '../../shared/api/academic/academicQueries';

export interface GenderChartData {
  name: string;
  value: number;
  color: string;
}

export interface StatusChartData {
  name: string;
  value: number;
  color: string;
}

export function useGenderDistribution() {
  const { data: students, isLoading } = useStudentList();
  return useMemo(() => {
    if (!students) return { data: [] as GenderChartData[], isLoading };
    const male = students.filter((s) => s.gender === 'Male').length;
    const female = students.filter((s) => s.gender === 'Female').length;
    return {
      data: [
        { name: 'Male', value: male, color: '#3b82f6' },
        { name: 'Female', value: female, color: '#ec4899' },
      ],
      isLoading: false,
    };
  }, [students, isLoading]);
}

export function useStatusDistribution() {
  const { data: students, isLoading } = useStudentList();
  return useMemo(() => {
    if (!students) return { data: [] as StatusChartData[], isLoading };
    const active = students.filter((s) => s.status === 'active').length;
    const transferred = students.filter((s) => s.status === 'transferred').length;
    const graduated = students.filter((s) => s.status === 'graduated').length;
    const dropped = students.filter((s) => s.status === 'dropped').length;
    return {
      data: [
        { name: 'Active', value: active, color: '#1f6f43' },
        { name: 'Transferred', value: transferred, color: '#f59e0b' },
        { name: 'Graduated', value: graduated, color: '#3b82f6' },
        { name: 'Dropped', value: dropped, color: '#ef4444' },
      ],
      isLoading: false,
    };
  }, [students, isLoading]);
}

export function useActiveTerm() {
  const { data: terms } = useTerms();
  const { data: years } = useAcademicYears();
  return useMemo(() => {
    if (!terms || !years) return { term: null, year: null };
    const activeTerm = terms.find((t) => t.is_current);
    const activeYear = activeTerm ? years.find((y) => y.id === activeTerm.academic_year_id) : null;
    return { term: activeTerm ?? null, year: activeYear ?? null };
  }, [terms, years]);
}