import { useState } from 'react';
import { useTerms, useCreateTerm, useUpdateTerm, useDeleteTerm, useAcademicYears } from '../../../shared/api/academic/academicQueries';
import { GraduationCap, Plus, Pencil, Trash2, BookOpen, CalendarDays, CheckCircle } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { Term } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<Term> = { academic_year_id: undefined, term_name: 'Term 1', start_date: '', end_date: '', is_current: false };

export default function TermsPage() {
  const { data: list, isLoading } = useTerms();
  const { data: years } = useAcademicYears();
  const create = useCreateTerm();
  const update = useUpdateTerm();
  const del = useDeleteTerm();
  const [modal, setModal] = useState<{ open: boolean; edit?: Term }>({ open: false });
  const [form, setForm] = useState<Partial<Term>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Term) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<GraduationCap className="w-8 h-8 text-primary" />}
        title="Terms"
        description="Configure terms within each academic year."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Term</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No terms found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Academic Year</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Start</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">End</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Current</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((item: Term) => {
              const y = years?.find((y) => y.id === item.academic_year_id);
              return <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm font-medium text-gray-900">{item.term_name}</td>
                <td className="p-3 text-sm text-gray-600">{y?.year_name || `Year #${item.academic_year_id}`}</td>
                <td className="p-3 text-sm text-gray-600">{formatDate(item.start_date)}</td>
                <td className="p-3 text-sm text-gray-600">{formatDate(item.end_date)}</td>
                <td className="p-3 text-sm"><span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${item.is_current ? 'bg-success-light text-success' : 'bg-gray-100 text-gray-500'}`}>{item.is_current ? 'Yes' : 'No'}</span></td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>;
            })}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Term' : 'Add Term'} subtitle="Set academic term dates and status" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Term Details" icon={BookOpen} description="Name, year, and date range">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Term Name" icon={BookOpen} required>
                  <select value={form.term_name || 'Term 1'} onChange={setF('term_name')} className={selectClass}><option value="Term 1">Term 1</option><option value="Term 2">Term 2</option><option value="Term 3">Term 3</option></select>
                </IconField>
                <IconField label="Academic Year" icon={GraduationCap} required>
                  <select value={form.academic_year_id || ''} onChange={(e) => setForm(p => ({ ...p, academic_year_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select year</option>{years?.map((y) => <option key={y.id} value={y.id}>{y.year_name}</option>)}</select>
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Start Date" icon={CalendarDays} required>
                  <input type="date" value={form.start_date || ''} onChange={setF('start_date')} required className={inputClass} />
                </IconField>
                <IconField label="End Date" icon={CalendarDays} required>
                  <input type="date" value={form.end_date || ''} onChange={setF('end_date')} required className={inputClass} />
                </IconField>
              </div>
            </FormSection>
            <FormSection title="Status" icon={CheckCircle} description="Mark as current term">
              <select value={String(form.is_current ?? false)} onChange={e => setForm(p => ({ ...p, is_current: e.target.value === 'true' }))} className={selectClass}><option value="false">Not current</option><option value="true">Current term</option></select>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Term?" message="This cannot be undone." />
    </div>
  );
}
