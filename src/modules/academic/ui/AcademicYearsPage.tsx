import { useState } from 'react';
import { useAcademicYears, useCreateAcademicYear, useUpdateAcademicYear, useDeleteAcademicYear } from '../../../shared/api/academic/academicQueries';
import { School, Plus, Pencil, Trash2 } from 'lucide-react';
import type { AcademicYear } from '../../../shared/types';
import PageHeader from '../../../shared/components/ui/PageHeader';
import DataTable from '../../../shared/components/ui/DataTable';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import { formatDate } from '../../../shared/utils/formatDate';

const df: Partial<AcademicYear> = { year_name: '', start_date: '', end_date: '', is_current: false };

const columns = [
  { key: 'year_name', label: 'Year Name', className: 'font-medium text-gray-900' },
  { key: 'start_date', label: 'Start Date', render: (item: AcademicYear) => formatDate(item.start_date) },
  { key: 'end_date', label: 'End Date', render: (item: AcademicYear) => formatDate(item.end_date) },
  {
    key: 'is_current', label: 'Current',
    render: (item: AcademicYear) => (
      <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${item.is_current ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-500'}`}>
        {item.is_current ? 'Yes' : 'No'}
      </span>
    ),
  },
];

export default function AcademicYearsPage() {
  const { data: list, isLoading } = useAcademicYears();
  const create = useCreateAcademicYear();
  const update = useUpdateAcademicYear();
  const del = useDeleteAcademicYear();
  const [modal, setModal] = useState<{ open: boolean; edit?: AcademicYear }>({ open: false });
  const [form, setForm] = useState<Partial<AcademicYear>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: AcademicYear) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<AcademicYear> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  const handleDelete = (id: number) => del.mutate(id, { onSuccess: () => setDeleteId(null) });

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<School className="w-8 h-8 text-primary" />}
        title="Academic Years"
        description="Manage academic years and set the current active year."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Year</button>}
      />
      <DataTable
        columns={columns}
        data={list}
        isLoading={isLoading}
        emptyMessage="No academic years found."
        keyExtractor={(item: AcademicYear) => item.id}
        actions={(item: AcademicYear) => (
          <>
            <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
            <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
          </>
        )}
      />
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Academic Year' : 'Add Academic Year'}>
        <form onSubmit={handleSubmit} className="p-6 space-y-4">
          <div><label className="block text-sm font-medium text-gray-700 mb-1">Year Name *</label><input value={form.year_name || ''} onChange={setF('year_name')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
          <div className="grid grid-cols-2 gap-4">
            <div><label className="block text-sm font-medium text-gray-700 mb-1">Start Date *</label><input type="date" value={form.start_date || ''} onChange={setF('start_date')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
            <div><label className="block text-sm font-medium text-gray-700 mb-1">End Date *</label><input type="date" value={form.end_date || ''} onChange={setF('end_date')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
          </div>
          <div><label className="block text-sm font-medium text-gray-700 mb-1">Current Year</label><select value={String(form.is_current ?? false)} onChange={e => setForm(p => ({ ...p, is_current: e.target.value === 'true' }))} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none"><option value="false">No</option><option value="true">Yes</option></select></div>
          <div className="flex justify-end gap-3 pt-2">
            <button type="button" onClick={() => setModal({ open: false })} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
            <button type="submit" disabled={create.isPending || update.isPending} className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer">{modal.edit ? 'Update' : 'Save'}</button>
          </div>
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => handleDelete(deleteId!)} title="Delete Academic Year?" message="This action cannot be undone." isLoading={del.isPending} />
    </div>
  );
}
