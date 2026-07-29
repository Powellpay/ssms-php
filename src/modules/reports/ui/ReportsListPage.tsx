import { useState } from 'react';
import { useReportCards as useList, useCreateReportCard as useCreate, useUpdateReportCard as useUpdate, useDeleteReportCard as useDelete } from '../../../shared/api/reports/reportQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useTerms, useStreams } from '../../../shared/api/academic/academicQueries';
import { Plus, Pencil, Trash2, BarChart3, User, BookOpen, GitBranch, CalendarCheck, CalendarX } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { ReportCard } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<ReportCard> = { student_id: undefined, term_id: undefined, stream_id: undefined, days_present: 0, days_absent: 0 };
const reportStatuses = ['active', 'transferred', 'graduated', 'dropped'] as const;

export default function ReportsListPage() {
  const { data: list, isLoading } = useList();
  const { data: students } = useStudentList();
  const { data: terms } = useTerms();
  const { data: streams } = useStreams();
  const create = useCreate();
  const update = useUpdate();
  const del = useDelete();
  const [modal, setModal] = useState<{ open: boolean; edit?: ReportCard }>({ open: false });
  const [form, setForm] = useState<Partial<ReportCard>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: ReportCard) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  const studentMap = new Map(students?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));
  const streamMap = new Map(streams?.map(s => [s.id, s]));

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<BarChart3 className="w-8 h-8 text-primary" />}
        title="Report Cards"
        description="Generate and manage learner report cards"
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Report</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No report cards found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Stream</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Present</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Absent</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date Issued</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((r: ReportCard) => {
              const st = studentMap.get(r.student_id);
              const t = termMap.get(r.term_id);
              const str = streamMap.get(r.stream_id);
              return (
              <tr key={r.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm">{st ? `${st.first_name} ${st.last_name}` : r.student_id}</td>
                <td className="p-3 text-sm">{t?.term_name ?? r.term_id}</td>
                <td className="p-3 text-sm">{str?.stream_name ?? r.stream_id}</td>
                <td className="p-3 text-sm text-center">{r.days_present}</td>
                <td className="p-3 text-sm text-center">{r.days_absent}</td>
                <td className="p-3 text-sm">{formatDate(r.date_issued)}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(r)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(r.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Report' : 'Add Report'} subtitle="Generate a termly report card for a learner" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Report Info" icon={BarChart3} description="Student, term, and stream">
              <div className="grid grid-cols-3 gap-4">
                <IconField label="Student" icon={User} required>
                  <select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select>
                </IconField>
                <IconField label="Term" icon={BookOpen} required>
                  <select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select>
                </IconField>
                <IconField label="Stream" icon={GitBranch} required>
                  <select value={form.stream_id || ''} onChange={(e) => setForm(p => ({ ...p, stream_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select stream</option>{streams?.map((s) => <option key={s.id} value={s.id}>{s.stream_name}</option>)}</select>
                </IconField>
              </div>
            </FormSection>
            <FormSection title="Attendance" icon={CalendarCheck} description="Days present and absent">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Days Present" icon={CalendarCheck}>
                  <input type="number" value={form.days_present ?? 0} onChange={setF('days_present')} className={inputClass} />
                </IconField>
                <IconField label="Days Absent" icon={CalendarX}>
                  <input type="number" value={form.days_absent ?? 0} onChange={setF('days_absent')} className={inputClass} />
                </IconField>
              </div>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Report?" message="This cannot be undone." />
    </div>
  );
}
