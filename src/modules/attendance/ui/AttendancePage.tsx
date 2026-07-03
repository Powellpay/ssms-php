import { useState } from 'react';
import { useAttendanceList, useCreateAttendance, useUpdateAttendance, useDeleteAttendance } from '../../../shared/api/attendance/attendanceQueries';
import { CalendarCheck, Plus, Pencil, Trash2, X } from 'lucide-react';
import type { Attendance } from '../../../shared/types';

const statuses = ['Present', 'Absent', 'Late', 'Excused'] as const;
const df: Partial<Attendance> = { student_id: 0, term_id: 0, attendance_date: '', status: 'Present' };

export default function AttendancePage() {
  const { data: list, isLoading } = useAttendanceList();
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

  const handleDelete = (id: number) => del.mutate(id, { onSuccess: () => setDeleteId(null) });

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><CalendarCheck className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Attendance</h1><p className="text-muted text-sm mt-1">Daily attendance register per stream.</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Attendance</button>
      </div>
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <div className="p-8 text-center text-muted">Loading...</div>
        : !list?.length ? <div className="p-8 text-center text-muted">No attendance records found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term ID</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Status</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: Attendance) => (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm text-gray-600">{item.student_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.term_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.attendance_date}</td>
                <td className="p-3 text-sm"><span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${item.status === 'Present' ? 'bg-success-light text-success' : item.status === 'Late' ? 'bg-warning-light text-warning' : item.status === 'Excused' ? 'bg-primary-light text-primary' : 'bg-alert-error-bg text-alert-error-text'}`}>{item.status}</span></td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
            ))}</tbody></table></div>}
      </div>
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Attendance' : 'Add Attendance'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Student ID *</label><input type="number" value={form.student_id || 0} onChange={setF('student_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Term ID *</label><input type="number" value={form.term_id || 0} onChange={setF('term_id')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Date *</label><input type="date" value={form.attendance_date || ''} onChange={setF('attendance_date')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Status *</label><select value={form.status || 'Present'} onChange={setF('status')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">{statuses.map(s => <option key={s} value={s}>{s}</option>)}</select></div>
              </div>
              <div className="flex justify-end gap-3 pt-2">
                <button type="button" onClick={() => setModal({ open: false })} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
                <button type="submit" disabled={create.isPending || update.isPending} className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer">{modal.edit ? 'Update' : 'Save'}</button>
              </div>
            </form>
          </div>
        </div>
      )}
      {deleteId && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setDeleteId(null)}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6" onClick={e => e.stopPropagation()}>
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Attendance Record?</h3>
            <p className="text-sm text-muted mb-6">This action cannot be undone.</p>
            <div className="flex justify-end gap-3">
              <button onClick={() => setDeleteId(null)} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
              <button onClick={() => handleDelete(deleteId)} className="px-4 py-2 rounded-lg bg-alert-error-text text-white text-sm font-medium hover:bg-red-700 cursor-pointer">Delete</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
