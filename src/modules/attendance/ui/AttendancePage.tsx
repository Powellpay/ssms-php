import { useState } from 'react';
import { useAttendanceList, useCreateAttendance, useUpdateAttendance, useDeleteAttendance } from '../../../shared/api/attendance/attendanceQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useTerms } from '../../../shared/api/academic/academicQueries';
import { CalendarCheck, Plus, Pencil, Trash2, User, BookOpen, CalendarDays } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { Attendance } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const statuses = ['Present', 'Absent', 'Late', 'Excused'] as const;
const df: Partial<Attendance> = { student_id: 0, term_id: 0, attendance_date: '', status: 'Present' };

export default function AttendancePage() {
  const { data: list, isLoading } = useAttendanceList();
  const { data: students } = useStudentList();
  const { data: terms } = useTerms();
  const create = useCreateAttendance();
  const update = useUpdateAttendance();
  const del = useDeleteAttendance();
  const [modal, setModal] = useState<{ open: boolean; edit?: Attendance }>({ open: false });
  const [form, setForm] = useState<Partial<Attendance>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Attendance) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<Attendance> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  const studentMap = new Map(students?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<CalendarCheck className="w-8 h-8 text-primary" />}
        title="Attendance"
        description="Daily attendance register per stream."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Attendance</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No attendance records found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Status</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: Attendance) => {
              const st = studentMap.get(item.student_id);
              const t = termMap.get(item.term_id);
              return (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm text-gray-600">{st ? `${st.first_name} ${st.last_name}` : item.student_id}</td>
                <td className="p-3 text-sm text-gray-600">{t?.term_name ?? item.term_id}</td>
                <td className="p-3 text-sm text-gray-600">{formatDate(item.attendance_date)}</td>
                <td className="p-3 text-sm"><span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${item.status === 'Present' ? 'bg-success-light text-success' : item.status === 'Late' ? 'bg-warning-light text-warning' : item.status === 'Excused' ? 'bg-primary-light text-primary' : 'bg-alert-error-bg text-alert-error-text'}`}>{item.status}</span></td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Attendance' : 'Add Attendance'} subtitle="Record daily attendance for a learner" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="p-6 space-y-5">
            <FormSection title="Attendance Record" icon={CalendarCheck} description="Student, term, date, and status">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Student" icon={User} required>
                  <select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select>
                </IconField>
                <IconField label="Term" icon={BookOpen} required>
                  <select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select>
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Date" icon={CalendarDays} required>
                  <input type="date" value={form.attendance_date || ''} onChange={setF('attendance_date')} required className={inputClass} />
                </IconField>
                <IconField label="Status" icon={CalendarCheck} required>
                  <select value={form.status || 'Present'} onChange={setF('status')} required className={selectClass}>{statuses.map(s => <option key={s} value={s}>{s}</option>)}</select>
                </IconField>
              </div>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Attendance Record?" message="This action cannot be undone." />
    </div>
  );
}
