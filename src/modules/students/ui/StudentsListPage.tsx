import { useState } from 'react';
import { useStudentList, useCreateStudent, useUpdateStudent, useDeleteStudent } from '../../../shared/api/students/studentQueries';
import { Users, Plus, Pencil, Trash2, Eye, Upload, GraduationCap, User, CalendarDays, Fingerprint } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import { useNavigate } from 'react-router-dom';
import { ROUTES } from '../../../app/routes/constants';
import type { Student } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';
import StudentImportModal from './StudentImportModal';

const genders = ['Male', 'Female'] as const;
const statuses = ['active', 'transferred', 'graduated', 'dropped'] as const;
const df: Partial<Student> = { admission_no: '', first_name: '', last_name: '', gender: 'Male', dob: '', admission_date: '', status: 'active' };

export default function StudentsListPage() {
  const { data: list, isLoading } = useStudentList();
  const create = useCreateStudent();
  const update = useUpdateStudent();
  const del = useDeleteStudent();
  const navigate = useNavigate();
  const [modal, setModal] = useState<{ open: boolean; edit?: Student }>({ open: false });
  const [form, setForm] = useState<Partial<Student>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [importOpen, setImportOpen] = useState(false);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Student) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<Student> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<Users className="w-8 h-8 text-primary" />}
        title="Students"
        description="Admissions, profiles, and enrollment"
        action={
          <div className="flex items-center gap-2">
            <button onClick={() => setImportOpen(true)} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-border text-sm font-medium text-gray-600 hover:bg-gray-50 cursor-pointer"><Upload className="w-4 h-4" /> Import</button>
            <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Student</button>
          </div>
        }
      />

      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No students found. Click "Add Student" to register one.</div>
        : <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-50">
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Admission No</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Name</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Gender</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">DOB</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Status</th>
                  <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
                </tr>
              </thead>
              <tbody>
                {list.map((s: Student) => (
                  <tr key={s.id} className="border-t border-border hover:bg-gray-50/50">
                    <td className="p-3 text-sm font-medium text-gray-900">{s.admission_no}</td>
                    <td className="p-3 text-sm text-gray-700">{s.first_name} {s.last_name}</td>
                    <td className="p-3 text-sm text-gray-600">{s.gender}</td>
                    <td className="p-3 text-sm text-gray-600">{formatDate(s.dob)}</td>
                    <td className="p-3 text-sm"><span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${s.status === 'active' ? 'bg-success-light text-success' : s.status === 'graduated' ? 'bg-primary-light text-primary' : 'bg-alert-error-bg text-alert-error-text'}`}>{s.status}</span></td>
                    <td className="p-3 text-right">
                      <button onClick={() => navigate(`${ROUTES.STUDENTS.VIEW.replace(':id', String(s.id))}`)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer" title="View"><Eye className="w-4 h-4" /></button>
                      <button onClick={() => openEdit(s)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer" title="Edit"><Pencil className="w-4 h-4" /></button>
                      <button onClick={() => setDeleteId(s.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer" title="Delete"><Trash2 className="w-4 h-4" /></button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        }
      </div>

      <Modal
        open={modal.open}
        onClose={() => setModal({ open: false })}
        title={modal.edit ? 'Edit Student' : 'Add Student'}
        subtitle="Register a new student or update existing details"
        maxWidth="lg"
      >
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Identity" icon={GraduationCap} description="Admission number and full name">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Admission No" icon={Fingerprint} required>
                  <input value={form.admission_no || ''} onChange={setF('admission_no')} required className={inputClass} placeholder="e.g. STD-2026-0001" />
                </IconField>
                <IconField label="Status" icon={User}>
                  <select value={form.status || 'active'} onChange={setF('status')} className={selectClass}>{statuses.map(s => <option key={s} value={s}>{s}</option>)}</select>
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="First Name" icon={User} required>
                  <input value={form.first_name || ''} onChange={setF('first_name')} required className={inputClass} placeholder="First name" />
                </IconField>
                <IconField label="Last Name" icon={User} required>
                  <input value={form.last_name || ''} onChange={setF('last_name')} required className={inputClass} placeholder="Last name" />
                </IconField>
              </div>
            </FormSection>

            <FormSection title="Personal Details" icon={User} description="Gender, date of birth, and admission date">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Gender" icon={User} required>
                  <select value={form.gender || 'Male'} onChange={setF('gender')} className={selectClass}>{genders.map(g => <option key={g} value={g}>{g}</option>)}</select>
                </IconField>
                <IconField label="Date of Birth" icon={CalendarDays}>
                  <input type="date" value={form.dob || ''} onChange={setF('dob')} className={inputClass} />
                </IconField>
              </div>
              <IconField label="Admission Date" icon={CalendarDays} required>
                <input type="date" value={form.admission_date || ''} onChange={setF('admission_date')} required className={inputClass} />
              </IconField>
            </FormSection>
          </div>

          <ModalFooter
            onCancel={() => setModal({ open: false })}
            submitLabel={modal.edit ? 'Update Student' : 'Save Student'}
            submitting={create.isPending || update.isPending}
          />
        </form>
      </Modal>

      <StudentImportModal
        open={importOpen}
        onClose={() => setImportOpen(false)}
        onSuccess={() => {}}
      />

      <ConfirmDialog
        open={deleteId !== null}
        onClose={() => setDeleteId(null)}
        onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })}
        title="Delete Student?"
        message="This will also remove enrollments and linked records. This action cannot be undone."
        isLoading={del.isPending}
      />
    </div>
  );
}
