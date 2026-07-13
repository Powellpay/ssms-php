import { useState } from 'react';
import { useSubjects, useCreateSubject, useUpdateSubject, useDeleteSubject } from '../../../shared/api/curriculum/curriculumQueries';
import { BookOpen, Plus, Pencil, Trash2 } from 'lucide-react';
import type { Subject } from '../../../shared/types';
import PageHeader from '../../../shared/components/ui/PageHeader';
import DataTable from '../../../shared/components/ui/DataTable';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';

const categories = ['Core', 'Elective', 'Pre-Vocational'] as const;
const df: Partial<Subject> = { subject_code: '', subject_name: '', category: 'Core' };

const columns = [
  { key: 'subject_code', label: 'Code', className: 'font-medium text-gray-900' },
  { key: 'subject_name', label: 'Subject Name' },
  {
    key: 'category', label: 'Category',
    render: (item: Subject) => (
      <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${item.category === 'Core' ? 'bg-primary-light text-primary' : item.category === 'Elective' ? 'bg-success-light text-success' : 'bg-warning-light text-warning'}`}>{item.category}</span>
    ),
  },
];

export default function SubjectsPage() {
  const { data: list, isLoading } = useSubjects();
  const create = useCreateSubject();
  const update = useUpdateSubject();
  const del = useDeleteSubject();
  const [modal, setModal] = useState<{ open: boolean; edit?: Subject }>({ open: false });
  const [form, setForm] = useState<Partial<Subject>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Subject) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<Subject> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  const handleDelete = (id: number) => del.mutate(id, { onSuccess: () => setDeleteId(null) });

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<BookOpen className="w-8 h-8 text-primary" />}
        title="Subjects"
        description="Manage subject catalogue with Core/Elective/Pre-Vocational categories."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Subject</button>}
      />
      <DataTable
        columns={columns}
        data={list}
        isLoading={isLoading}
        emptyMessage="No subjects found."
        keyExtractor={(item: Subject) => item.id}
        actions={(item: Subject) => (
          <>
            <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
            <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
          </>
        )}
      />
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Subject' : 'Add Subject'}>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          <div className="grid grid-cols-2 gap-4">
            <div><label className="block text-sm font-medium text-gray-700 mb-1">Subject Code *</label><input value={form.subject_code || ''} onChange={setF('subject_code')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
            <div><label className="block text-sm font-medium text-gray-700 mb-1">Category *</label><select value={form.category || 'Core'} onChange={setF('category')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">{categories.map(c => <option key={c} value={c}>{c}</option>)}</select></div>
          </div>
          <div><label className="block text-sm font-medium text-gray-700 mb-1">Subject Name *</label><input value={form.subject_name || ''} onChange={setF('subject_name')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => setModal({ open: false })} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
            <button type="submit" disabled={create.isPending || update.isPending} className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer">{modal.edit ? 'Update' : 'Save'}</button>
          </div>
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => handleDelete(deleteId!)} title="Delete Subject?" message="This action cannot be undone." isLoading={del.isPending} />
    </div>
  );
}
