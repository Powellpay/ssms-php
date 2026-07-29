import { useState } from 'react';
import { useAssessmentRecords, useCreateAssessmentRecord, useUpdateAssessmentRecord, useDeleteAssessmentRecord } from '../../../shared/api/assessment/assessmentQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useSubjects } from '../../../shared/api/curriculum/curriculumQueries';
import { useTerms } from '../../../shared/api/academic/academicQueries';
import { useAssessmentTypes } from '../../../shared/api/assessment/assessmentQueries';
import { ClipboardCheck, Plus, Pencil, Trash2, X } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { AssessmentRecord } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';

const df: Partial<AssessmentRecord> = { student_id: 0, subject_id: 0, assessment_type_id: 0, term_id: 0, score: 0, max_score: 100, date_recorded: '' };

export default function AssessmentRecordsPage() {
  const { data: list, isLoading } = useAssessmentRecords();
  const { data: students } = useStudentList();
  const { data: subjects } = useSubjects();
  const { data: terms } = useTerms();
  const { data: assessmentTypes } = useAssessmentTypes();
  const create = useCreateAssessmentRecord();
  const update = useUpdateAssessmentRecord();
  const del = useDeleteAssessmentRecord();
  const [modal, setModal] = useState<{ open: boolean; edit?: AssessmentRecord }>({ open: false });
  const [form, setForm] = useState<Partial<AssessmentRecord>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: AssessmentRecord) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<AssessmentRecord> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  const handleDelete = (id: number) => del.mutate(id, { onSuccess: () => setDeleteId(null) });

  const studentMap = new Map(students?.map(s => [s.id, s]));
  const subjectMap = new Map(subjects?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));
  const typeMap = new Map(assessmentTypes?.map(t => [t.id, t]));

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><ClipboardCheck className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Assessment Records</h1><p className="text-muted text-sm mt-1">Record CA and exam scores per learner per subject.</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Record</button>
      </div>
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No assessment records found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Subject</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Assessment Type</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Score</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Max Score</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: AssessmentRecord) => {
              const st = studentMap.get(item.student_id);
              const sub = subjectMap.get(item.subject_id);
              const t = termMap.get(item.term_id);
              const at = typeMap.get(item.assessment_type_id);
              return (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm text-gray-600">{st ? `${st.first_name} ${st.last_name}` : item.student_id}</td>
                <td className="p-3 text-sm text-gray-600">{sub?.subject_name ?? item.subject_id}</td>
                <td className="p-3 text-sm text-gray-600">{at?.type_name ?? item.assessment_type_id}</td>
                <td className="p-3 text-sm text-gray-600">{t?.term_name ?? item.term_id}</td>
                <td className="p-3 text-sm text-gray-900 font-medium">{item.score ?? '-'}</td>
                <td className="p-3 text-sm text-gray-600">{item.max_score}</td>
                <td className="p-3 text-sm text-gray-600">{formatDate(item.date_recorded)}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Assessment Record' : 'Add Assessment Record'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Student *</label><select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Subject *</label><select value={form.subject_id || ''} onChange={(e) => setForm(p => ({ ...p, subject_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select subject</option>{subjects?.map((s) => <option key={s.id} value={s.id}>{s.subject_name}</option>)}</select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Assessment Type *</label><select value={form.assessment_type_id || ''} onChange={(e) => setForm(p => ({ ...p, assessment_type_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select type</option>{assessmentTypes?.map((t) => <option key={t.id} value={t.id}>{t.type_name}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Term *</label><select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Score</label><input type="number" value={form.score ?? 0} onChange={setF('score')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Max Score *</label><input type="number" value={form.max_score || 100} onChange={setF('max_score')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div><label className="block text-sm font-medium text-gray-700 mb-1">Date Recorded *</label><input type="date" value={form.date_recorded || ''} onChange={setF('date_recorded')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              <div className="flex justify-end gap-3 pt-2">
                <button type="button" onClick={() => setModal({ open: false })} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
                <button type="submit" disabled={create.isPending || update.isPending} className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer">{modal.edit ? 'Update' : 'Save'}</button>
              </div>
            </form>
          </div>
        </div>
      )}
      {deleteId && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setDeleteId(null)}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6" onClick={e => e.stopPropagation()}>
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Assessment Record?</h3>
            <p className="text-sm text-muted mb-6">This action cannot be undone.</p>
            <div className="flex justify-end gap-3">
              <button onClick={() => setDeleteId(null)} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
              <button onClick={() => handleDelete(deleteId)} className="px-4 py-2 rounded-lg bg-alert-error-text text-white text-sm font-medium hover:bg-red-700 cursor-pointer">Delete</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
