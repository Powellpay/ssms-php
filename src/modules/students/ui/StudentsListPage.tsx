import { useState } from 'react';
import { useStudentList, useCreateStudent, useUpdateStudent, useDeleteStudent } from '../../../shared/api/students/studentQueries';
import { Users, Plus, Pencil, Trash2, X, Eye, Upload } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { ROUTES } from '../../../app/routes/constants';
import type { Student } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';

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
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><Users className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Students</h1><p className="text-muted text-sm mt-1">Admissions, profiles, and enrollment</p></div>
        </div>
        <div className="flex items-center gap-2">
          <button onClick={() => navigate(ROUTES.STUDENTS.IMPORT)} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-border text-sm font-medium text-gray-600 hover:bg-gray-50 cursor-pointer"><Upload className="w-4 h-4" /> Import</button>
          <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Student</button>
        </div>
      </div>

      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <div className="p-8 text-center text-muted">Loading...</div>
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

      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Student' : 'Add Student'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Admission No *</label><input value={form.admission_no || ''} onChange={setF('admission_no')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Status</label><select value={form.status || 'active'} onChange={setF('status')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">{statuses.map(s => <option key={s} value={s}>{s}</option>)}</select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">First Name *</label><input value={form.first_name || ''} onChange={setF('first_name')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Last Name *</label><input value={form.last_name || ''} onChange={setF('last_name')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Gender *</label><select value={form.gender || 'Male'} onChange={setF('gender')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">{genders.map(g => <option key={g} value={g}>{g}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label><input type="date" value={form.dob || ''} onChange={setF('dob')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div><label className="block text-sm font-medium text-gray-700 mb-1">Admission Date *</label><input type="date" value={form.admission_date || ''} onChange={setF('admission_date')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              <div className="flex justify-end gap-3 pt-2">
                <button type="button" onClick={() => setModal({ open: false })} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
                <button type="submit" disabled={create.isPending || update.isPending} className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer">{modal.edit ? 'Update' : 'Save'} Student</button>
              </div>
            </form>
          </div>
        </div>
      )}

      {deleteId && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setDeleteId(null)}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6" onClick={e => e.stopPropagation()}>
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Student?</h3>
            <p className="text-sm text-muted mb-6">This will also remove enrollments and linked records.</p>
            <div className="flex justify-end gap-3">
              <button onClick={() => setDeleteId(null)} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
              <button onClick={() => del.mutate(deleteId)} className="px-4 py-2 rounded-lg bg-alert-error-text text-white text-sm font-medium hover:bg-red-700 cursor-pointer">Delete</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
