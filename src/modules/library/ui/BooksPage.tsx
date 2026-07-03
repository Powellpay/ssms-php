import { useState } from 'react';
import { useLibraryBooks as useList, useCreateLibraryBook as useCreate, useUpdateLibraryBook as useUpdate, useDeleteLibraryBook as useDelete } from '../../../shared/api/library/libraryQueries';
import { Plus, Pencil, Trash2, X, Library } from 'lucide-react';
import type { LibraryBook } from '../../../shared/types';

const df: Partial<LibraryBook> = { title: '', author: '', isbn: '', category: '', total_copies: 1, available_copies: 1 };

export default function BooksPage() {
  const { data: list, isLoading } = useList();
  const create = useCreate();
  const update = useUpdate();
  const del = useDelete();
  const [modal, setModal] = useState<{ open: boolean; edit?: LibraryBook }>({ open: false });
  const [form, setForm] = useState<Partial<LibraryBook>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: LibraryBook) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><Library className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Library</h1><p className="text-muted text-sm mt-1">Book catalogue and management</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Book</button>
      </div>
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <div className="p-8 text-center text-muted">Loading...</div>
        : !list?.length ? <div className="p-8 text-center text-muted">No books in catalogue.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Title</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Author</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">ISBN</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Category</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Total</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Available</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((b: LibraryBook) => (
              <tr key={b.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm font-medium text-gray-900">{b.title}</td>
                <td className="p-3 text-sm text-gray-600">{b.author || '-'}</td>
                <td className="p-3 text-sm text-gray-600">{b.isbn || '-'}</td>
                <td className="p-3 text-sm text-gray-600">{b.category || '-'}</td>
                <td className="p-3 text-sm text-center">{b.total_copies}</td>
                <td className="p-3 text-sm text-center"><span className={b.available_copies > 0 ? 'text-success font-medium' : 'text-alert-error-text font-medium'}>{b.available_copies}</span></td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(b)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(b.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
            ))}</tbody></table></div>}
      </div>
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Book' : 'Add Book'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div><label className="block text-sm font-medium text-gray-700 mb-1">Title *</label><input value={form.title || ''} onChange={setF('title')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Author</label><input value={form.author || ''} onChange={setF('author')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">ISBN</label><input value={form.isbn || ''} onChange={setF('isbn')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
              </div>
              <div className="grid grid-cols-3 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Category</label><input value={form.category || ''} onChange={setF('category')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Total Copies *</label><input type="number" min="1" value={form.total_copies || 1} onChange={setF('total_copies')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Available</label><input type="number" min="0" value={form.available_copies ?? form.total_copies} onChange={setF('available_copies')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
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
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Book?</h3>
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
