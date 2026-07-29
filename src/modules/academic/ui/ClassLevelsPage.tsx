import { useState } from 'react';
import { useClassLevels, useCreateClassLevel, useUpdateClassLevel, useDeleteClassLevel } from '../../../shared/api/academic/academicQueries';
import { Layers, Plus, Pencil, Trash2, Hash, FileText } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { ClassLevel } from '../../../shared/types';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<ClassLevel> = { level_name: '', numeric_level: 1, description: '' };

export default function ClassLevelsPage() {
  const { data: list, isLoading } = useClassLevels();
  const create = useCreateClassLevel();
  const update = useUpdateClassLevel();
  const del = useDeleteClassLevel();
  const [modal, setModal] = useState<{ open: boolean; edit?: ClassLevel }>({ open: false });
  const [form, setForm] = useState<Partial<ClassLevel>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: ClassLevel) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<ClassLevel> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<Layers className="w-8 h-8 text-primary" />}
        title="Class Levels"
        description="Set up S1–S4 class levels for your school."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Level</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No class levels found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Level Name</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Numeric Level</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Description</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: ClassLevel) => (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm font-medium text-gray-900">{item.level_name}</td>
                <td className="p-3 text-sm text-gray-600">{item.numeric_level}</td>
                <td className="p-3 text-sm text-gray-600">{item.description || '-'}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
            ))}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Class Level' : 'Add Class Level'} subtitle="Configure level name and numeric order" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="p-6 space-y-5">
            <FormSection title="Level Details" icon={Layers} description="Name, numeric level, and optional description">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Level Name" icon={Layers} required>
                  <input value={form.level_name || ''} onChange={setF('level_name')} required className={inputClass} placeholder="e.g. Senior 1" />
                </IconField>
                <IconField label="Numeric Level" icon={Hash} required>
                  <input type="number" min="1" max="6" value={form.numeric_level ?? 1} onChange={(e) => setForm(p => ({ ...p, numeric_level: Number(e.target.value) }))} required className={inputClass} />
                </IconField>
              </div>
              <IconField label="Description" icon={FileText}>
                <input value={form.description || ''} onChange={setF('description')} className={inputClass} placeholder="Optional description" />
              </IconField>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Class Level?" message="This action cannot be undone." />
    </div>
  );
}
