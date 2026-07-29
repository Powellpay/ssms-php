import { useState } from 'react';
import { useDisciplineRecords as useList, useCreateDisciplineRecord as useCreate, useUpdateDisciplineRecord as useUpdate, useDeleteDisciplineRecord as useDelete } from '../../../shared/api/discipline/disciplineQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useTerms } from '../../../shared/api/academic/academicQueries';
import { Plus, Pencil, Trash2, Scale, User, BookOpen, CalendarDays, FileText, AlertTriangle } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { DisciplineRecord } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass, textareaClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<DisciplineRecord> = { student_id: undefined, term_id: undefined, incident_date: '', description: '', action_taken: '' };

export default function DisciplineListPage() {
  const { data: list, isLoading } = useList();
  const { data: students } = useStudentList();
  const { data: terms } = useTerms();
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

  const studentMap = new Map(students?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<Scale className="w-8 h-8 text-primary" />}
        title="Discipline"
        description="Learner conduct and incident records"
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Record</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No discipline records found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Description</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Action Taken</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((s: DisciplineRecord) => {
              const st = studentMap.get(s.student_id);
              const t = termMap.get(s.term_id);
              return (
              <tr key={s.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm">{st ? `${st.first_name} ${st.last_name}` : s.student_id}</td>
                <td className="p-3 text-sm">{t?.term_name ?? s.term_id}</td>
                <td className="p-3 text-sm">{formatDate(s.incident_date)}</td>
                <td className="p-3 text-sm max-w-[200px] truncate">{s.description}</td>
                <td className="p-3 text-sm">{s.action_taken || '-'}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(s)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(s.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Record' : 'Add Record'} subtitle="Record a discipline incident" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Incident Info" icon={AlertTriangle} description="Student, term, and date">
              <div className="grid grid-cols-3 gap-4">
                <IconField label="Student" icon={User} required>
                  <select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select>
                </IconField>
                <IconField label="Term" icon={BookOpen} required>
                  <select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select>
                </IconField>
                <IconField label="Date" icon={CalendarDays} required>
                  <input type="date" value={form.incident_date || ''} onChange={setF('incident_date')} required className={inputClass} />
                </IconField>
              </div>
            </FormSection>
            <FormSection title="Details" icon={FileText} description="Description and action taken">
              <IconField label="Description" icon={FileText} required>
                <textarea value={form.description || ''} onChange={setF('description')} required rows={3} className={textareaClass} />
              </IconField>
              <IconField label="Action Taken" icon={AlertTriangle}>
                <input value={form.action_taken || ''} onChange={setF('action_taken')} className={inputClass} placeholder="e.g. Verbal warning" />
              </IconField>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Record?" message="This cannot be undone." />
    </div>
  );
}
