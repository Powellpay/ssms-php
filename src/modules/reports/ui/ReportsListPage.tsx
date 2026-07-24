import { useState } from 'react';
import { useReportCards as useList, useCreateReportCard as useCreate, useUpdateReportCard as useUpdate, useDeleteReportCard as useDelete } from '../../../shared/api/reports/reportQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useTerms, useStreams } from '../../../shared/api/academic/academicQueries';
import { Plus, Pencil, Trash2, X, BarChart3 } from 'lucide-react';
import type { ReportCard } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';

const df: Partial<ReportCard> = { student_id: undefined, term_id: undefined, stream_id: undefined, days_present: 0, days_absent: 0 };

export default function ReportsListPage() {
  const { data: list, isLoading } = useList();
  const { data: students } = useStudentList();
  const { data: terms } = useTerms();
  const { data: streams } = useStreams();
  const create = useCreate();
  const update = useUpdate();
  const del = useDelete();
  const [modal, setModal] = useState<{ open: boolean; edit?: ReportCard }>({ open: false });
  const [form, setForm] = useState<Partial<ReportCard>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: ReportCard) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  const studentMap = new Map(students?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));
  const streamMap = new Map(streams?.map(s => [s.id, s]));

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><BarChart3 className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Report Cards</h1><p className="text-muted text-sm mt-1">Generate and manage learner report cards</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Report</button>
      </div>
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <div className="p-8 text-center text-muted">Loading...</div>
        : !list?.length ? <div className="p-8 text-center text-muted">No report cards found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Stream</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Present</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Absent</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date Issued</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((r: ReportCard) => {
              const st = studentMap.get(r.student_id);
              const t = termMap.get(r.term_id);
              const str = streamMap.get(r.stream_id);
              return (
              <tr key={r.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm">{st ? `${st.first_name} ${st.last_name}` : r.student_id}</td>
                <td className="p-3 text-sm">{t?.term_name ?? r.term_id}</td>
                <td className="p-3 text-sm">{str?.stream_name ?? r.stream_id}</td>
                <td className="p-3 text-sm text-center">{r.days_present}</td>
                <td className="p-3 text-sm text-center">{r.days_absent}</td>
                <td className="p-3 text-sm">{formatDate(r.date_issued)}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(r)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(r.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Report' : 'Add Report'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-3 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Student *</label><select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Term *</label><select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Stream *</label><select value={form.stream_id || ''} onChange={(e) => setForm(p => ({ ...p, stream_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select stream</option>{streams?.map((s) => <option key={s.id} value={s.id}>{s.stream_name}</option>)}</select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Days Present</label><input type="number" value={form.days_present ?? 0} onChange={setF('days_present')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Days Absent</label><input type="number" value={form.days_absent ?? 0} onChange={setF('days_absent')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
              </div>
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
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Report?</h3>
            <p className="text-sm text-muted mb-6">This cannot be undone.</p>
            <div className="flex justify-end gap-3">
              <button onClick={() => setDeleteId(null)} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
              <button onClick={() => del.mutate(deleteId)} className="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 cursor-pointer">Delete</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
