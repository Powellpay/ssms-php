import { useState } from 'react';
import { useDisciplineRecords as useList, useCreateDisciplineRecord as useCreate, useUpdateDisciplineRecord as useUpdate, useDeleteDisciplineRecord as useDelete } from '../../../shared/api/discipline/disciplineQueries';
import { Plus, Pencil, Trash2, X, Scale } from 'lucide-react';
import type { DisciplineRecord } from '../../../shared/types';

const df: Partial<DisciplineRecord> = { student_id: undefined, term_id: undefined, incident_date: '', description: '', action_taken: '' };

export default function DisciplineListPage() {
  const { data: list, isLoading } = useList();
  const create = useCreate();
  const update = useUpdate();
  const del = useDelete();
  const [modal, setModal] = useState<{ open: boolean; edit?: DisciplineRecord }>({ open: false });
  const [form, setForm] = useState<Partial<DisciplineRecord>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: DisciplineRecord) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><Scale className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Discipline</h1><p className="text-muted text-sm mt-1">Learner conduct and incident records</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Record</button>
      </div>
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <div className="p-8 text-center text-muted">Loading...</div>
        : !list?.length ? <div className="p-8 text-center text-muted">No discipline records found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Description</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Action Taken</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((s: DisciplineRecord) => (
              <tr key={s.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm">{s.student_id}</td>
                <td className="p-3 text-sm">{s.term_id}</td>
                <td className="p-3 text-sm">{s.incident_date}</td>
                <td className="p-3 text-sm max-w-[200px] truncate">{s.description}</td>
                <td className="p-3 text-sm">{s.action_taken || '-'}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(s)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(s.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
            ))}</tbody></table></div>}
      </div>
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Record' : 'Add Record'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-3 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Student ID *</label><input type="number" value={form.student_id || ''} onChange={setF('student_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Term ID *</label><input type="number" value={form.term_id || ''} onChange={setF('term_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Date *</label><input type="date" value={form.incident_date || ''} onChange={setF('incident_date')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
              </div>
              <div><label className="block text-sm font-medium text-gray-700 mb-1">Description *</label><textarea value={form.description || ''} onChange={setF('description')} required rows={3} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
              <div><label className="block text-sm font-medium text-gray-700 mb-1">Action Taken</label><input value={form.action_taken || ''} onChange={setF('action_taken')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
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
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Record?</h3>
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
