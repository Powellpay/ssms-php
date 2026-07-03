import { useState } from 'react';
import { useStreams, useCreateStream, useUpdateStream, useDeleteStream } from '../../../shared/api/academic/academicQueries';
import { GitBranch, Plus, Pencil, Trash2, X } from 'lucide-react';
import type { Stream } from '../../../shared/types';

const df: Partial<Stream> = { class_level_id: 0, academic_year_id: 0, stream_name: '', class_teacher_id: 0 };

export default function StreamsPage() {
  const { data: list, isLoading } = useStreams();
  const create = useCreateStream();
  const update = useUpdateStream();
  const del = useDeleteStream();
  const [modal, setModal] = useState<{ open: boolean; edit?: Stream }>({ open: false });
  const [form, setForm] = useState<Partial<Stream>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Stream) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<Stream> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  const handleDelete = (id: number) => del.mutate(id, { onSuccess: () => setDeleteId(null) });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><GitBranch className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Streams</h1><p className="text-muted text-sm mt-1">Create and manage class streams (A, B, East, etc.) with class teachers.</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Stream</button>
      </div>
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <div className="p-8 text-center text-muted">Loading...</div>
        : !list?.length ? <div className="p-8 text-center text-muted">No streams found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Stream Name</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Class Level ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Academic Year ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Class Teacher ID</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: Stream) => (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm font-medium text-gray-900">{item.stream_name}</td>
                <td className="p-3 text-sm text-gray-600">{item.class_level_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.academic_year_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.class_teacher_id ?? '-'}</td>
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
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Stream' : 'Add Stream'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Stream Name *</label><input value={form.stream_name || ''} onChange={setF('stream_name')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Class Level ID *</label><input type="number" value={form.class_level_id || 0} onChange={setF('class_level_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Academic Year ID *</label><input type="number" value={form.academic_year_id || 0} onChange={setF('academic_year_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Class Teacher ID</label><input type="number" value={form.class_teacher_id || 0} onChange={setF('class_teacher_id')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
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
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Stream?</h3>
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
