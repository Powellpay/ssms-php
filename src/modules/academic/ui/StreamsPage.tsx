import { useState } from 'react';
import { useStreams, useCreateStream, useUpdateStream, useDeleteStream, useClassLevels, useAcademicYears } from '../../../shared/api/academic/academicQueries';
import { useStaffList } from '../../../shared/api/staff/staffQueries';
import { GitBranch, Plus, Pencil, Trash2, Users, BookOpen, CalendarDays, User } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { Stream } from '../../../shared/types';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<Stream> = { class_level_id: undefined, academic_year_id: undefined, stream_name: '', class_teacher_id: undefined };

export default function StreamsPage() {
  const { data: list, isLoading } = useStreams();
  const { data: levels } = useClassLevels();
  const { data: years } = useAcademicYears();
  const { data: staff } = useStaffList();
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
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<GitBranch className="w-8 h-8 text-primary" />}
        title="Streams"
        description="Create and manage class streams with teachers."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Stream</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No streams found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Stream</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Class Level</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Year</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Class Teacher</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((item: Stream) => {
              const lv = levels?.find((l) => l.id === item.class_level_id);
              const yr = years?.find((y) => y.id === item.academic_year_id);
              const t = staff?.find((s) => s.id === item.class_teacher_id);
              return <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm font-medium text-gray-900">{item.stream_name}</td>
                <td className="p-3 text-sm text-gray-600">{lv?.level_name || `Level #${item.class_level_id}`}</td>
                <td className="p-3 text-sm text-gray-600">{yr?.year_name || `Year #${item.academic_year_id}`}</td>
                <td className="p-3 text-sm text-gray-600">{t ? `${t.first_name} ${t.last_name}` : '-'}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>;
            })}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Stream' : 'Add Stream'} subtitle="Configure stream assignment and class teacher" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Stream Info" icon={GitBranch} description="Name, class level, and academic year">
              <div className="grid grid-cols-3 gap-4">
                <IconField label="Stream Name" icon={GitBranch} required>
                  <input value={form.stream_name || ''} onChange={setF('stream_name')} required className={inputClass} placeholder="e.g. A, B, East" />
                </IconField>
                <IconField label="Class Level" icon={BookOpen} required>
                  <select value={form.class_level_id || ''} onChange={(e) => setForm(p => ({ ...p, class_level_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select</option>{levels?.map((l) => <option key={l.id} value={l.id}>{l.level_name}</option>)}</select>
                </IconField>
                <IconField label="Year" icon={CalendarDays} required>
                  <select value={form.academic_year_id || ''} onChange={(e) => setForm(p => ({ ...p, academic_year_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select</option>{years?.map((y) => <option key={y.id} value={y.id}>{y.year_name}</option>)}</select>
                </IconField>
              </div>
            </FormSection>
            <FormSection title="Teacher" icon={Users} description="Assign a class teacher">
              <IconField label="Class Teacher" icon={User}>
                <select value={form.class_teacher_id || ''} onChange={(e) => setForm(p => ({ ...p, class_teacher_id: e.target.value ? Number(e.target.value) : undefined }))} className={selectClass}><option value="">-- None --</option>{staff?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select>
              </IconField>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Stream?" message="This cannot be undone." />
    </div>
  );
}
