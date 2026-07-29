import { useState } from 'react';
import { useSubjects, useCreateSubject, useUpdateSubject, useDeleteSubject } from '../../../shared/api/curriculum/curriculumQueries';
import { BookOpen, Plus, Pencil, Trash2, Hash, Type, Bookmark } from 'lucide-react';
import type { Subject } from '../../../shared/types';
import PageHeader from '../../../shared/components/ui/PageHeader';
import DataTable from '../../../shared/components/ui/DataTable';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';

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

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<BookOpen className="w-8 h-8 text-primary" />}
        title="Subjects"
        description="Manage subject catalogue with Core/Elective/Pre-Vocational categories."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Subject</button>}
      />
      <DataTable columns={columns} data={list} isLoading={isLoading} emptyMessage="No subjects found." keyExtractor={(item: Subject) => item.id}
        actions={(item: Subject) => (
          <>
            <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
            <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
          </>
        )}
      />
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Subject' : 'Add Subject'} subtitle="Add a new subject to the curriculum" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="p-6 space-y-5">
            <FormSection title="Subject Info" icon={BookOpen} description="Code, name, and category">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Subject Code" icon={Hash} required>
                  <input value={form.subject_code || ''} onChange={setF('subject_code')} required className={inputClass} placeholder="e.g. SCI" />
                </IconField>
                <IconField label="Category" icon={Bookmark} required>
                  <select value={form.category || 'Core'} onChange={setF('category')} required className={selectClass}>{categories.map(c => <option key={c} value={c}>{c}</option>)}</select>
                </IconField>
              </div>
              <IconField label="Subject Name" icon={Type} required>
                <input value={form.subject_name || ''} onChange={setF('subject_name')} required className={inputClass} placeholder="e.g. General Science" />
              </IconField>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Subject?" message="This action cannot be undone." isLoading={del.isPending} />
    </div>
  );
}
