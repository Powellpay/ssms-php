import { useState } from 'react';
import { useAssessmentRecords, useCreateAssessmentRecord, useUpdateAssessmentRecord, useDeleteAssessmentRecord } from '../../../shared/api/assessment/assessmentQueries';
import { ClipboardCheck, Plus, Pencil, Trash2, X } from 'lucide-react';
import type { AssessmentRecord } from '../../../shared/types';

const df: Partial<AssessmentRecord> = { student_id: 0, subject_id: 0, assessment_type_id: 0, term_id: 0, score: 0, max_score: 100, date_recorded: '' };

export default function AssessmentRecordsPage() {
  const { data: list, isLoading } = useAssessmentRecords();
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
        {isLoading ? <div className="p-8 text-center text-muted">Loading...</div>
        : !list?.length ? <div className="p-8 text-center text-muted">No assessment records found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Subject ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Assessment Type ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Score</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Max Score</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: AssessmentRecord) => (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm text-gray-600">{item.student_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.subject_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.assessment_type_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.term_id}</td>
                <td className="p-3 text-sm text-gray-900 font-medium">{item.score ?? '-'}</td>
                <td className="p-3 text-sm text-gray-600">{item.max_score}</td>
                <td className="p-3 text-sm text-gray-600">{item.date_recorded}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
            ))}</tbody></table></div>}
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
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Student ID *</label><input type="number" value={form.student_id || 0} onChange={setF('student_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Subject ID *</label><input type="number" value={form.subject_id || 0} onChange={setF('subject_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Assessment Type ID *</label><input type="number" value={form.assessment_type_id || 0} onChange={setF('assessment_type_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Term ID *</label><input type="number" value={form.term_id || 0} onChange={setF('term_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
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
