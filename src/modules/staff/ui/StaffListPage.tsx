import { useState } from 'react';
import { useStaffList, useCreateStaff, useUpdateStaff, useDeleteStaff } from '../../shared/api/staff/staffQueries';
import { Plus, Pencil, Trash2, X, UserCircle } from 'lucide-react';
import type { Staff } from '../../shared/types';

const defaultForm = { staff_no: '', first_name: '', last_name: '', gender: 'Male' as const, email: '', phone: '', designation: '', status: 'active' as const, dob: '', date_joined: '' };

export default function StaffListPage() {
  const { data: staffList, isLoading } = useStaffList();
  const createStaff = useCreateStaff();
  const updateStaff = useUpdateStaff(0);
  const deleteStaff = useDeleteStaff();
  const [modal, setModal] = useState<{ open: boolean; edit?: Staff }>({ open: false });
  const [form, setForm] = useState(defaultForm);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(defaultForm); setModal({ open: true }); };
  const openEdit = (s: Staff) => { setForm({ staff_no: s.staff_no, first_name: s.first_name, last_name: s.last_name, gender: s.gender, email: s.email || '', phone: s.phone || '', designation: s.designation || '', status: s.status, dob: s.dob || '', date_joined: '' }); setModal({ open: true, edit: s }); };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) {
      updateStaff.mutate({ ...form, id: modal.edit.id } as unknown as Partial<Staff>, { onSuccess: () => setModal({ open: false }) });
    } else {
      createStaff.mutate(form, { onSuccess: () => setModal({ open: false }) });
    }
  };

  const handleDelete = (id: number) => {
    deleteStaff.mutate(id, { onSuccess: () => setDeleteId(null) });
  };

  const set = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><UserCircle className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Staff</h1><p className="text-muted text-sm mt-1">Manage teacher and non-teaching staff</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark transition-colors cursor-pointer"><Plus className="w-4 h-4" /> Add Staff</button>
      </div>

      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? (
          <div className="p-8 text-center text-muted">Loading...</div>
        ) : !staffList?.length ? (
          <div className="p-8 text-center text-muted">No staff records found. Click "Add Staff" to create one.</div>
        ) : (
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr className="bg-gray-50">
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Staff No</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Name</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Gender</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Designation</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Email</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Phone</th>
                  <th className="text-left p-3 text-sm font-semibold text-gray-600">Status</th>
                  <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
                </tr>
              </thead>
              <tbody>
                {staffList.map((s) => (
                  <tr key={s.id} className="border-t border-border hover:bg-gray-50/50">
                    <td className="p-3 text-sm font-medium text-gray-900">{s.staff_no}</td>
                    <td className="p-3 text-sm text-gray-700">{s.first_name} {s.last_name}</td>
                    <td className="p-3 text-sm text-gray-600">{s.gender}</td>
                    <td className="p-3 text-sm text-gray-600">{s.designation || '-'}</td>
                    <td className="p-3 text-sm text-gray-600">{s.email || '-'}</td>
                    <td className="p-3 text-sm text-gray-600">{s.phone || '-'}</td>
                    <td className="p-3 text-sm">
                      <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${s.status === 'active' ? 'bg-success-light text-success' : s.status === 'on leave' ? 'bg-warning-light text-warning' : 'bg-alert-error-bg text-alert-error-text'}`}>{s.status}</span>
                    </td>
                    <td className="p-3 text-right">
                      <button onClick={() => openEdit(s)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                      <button onClick={() => setDeleteId(s.id)} className="p-1.5 text-muted hover:text-alert-error-text rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Add/Edit Modal */}
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Staff' : 'Add Staff'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Staff No *</label><input value={form.staff_no} onChange={set('staff_no')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Designation</label><input value={form.designation} onChange={set('designation')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">First Name *</label><input value={form.first_name} onChange={set('first_name')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Last Name *</label><input value={form.last_name} onChange={set('last_name')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Gender *</label><select value={form.gender} onChange={set('gender')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none"><option value="Male">Male</option><option value="Female">Female</option></select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Status</label><select value={form.status} onChange={set('status')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none"><option value="active">Active</option><option value="on leave">On Leave</option><option value="left">Left</option></select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" value={form.email} onChange={set('email')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Phone</label><input value={form.phone} onChange={set('phone')} className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="flex justify-end gap-3 pt-2">
                <button type="button" onClick={() => setModal({ open: false })} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
                <button type="submit" disabled={createStaff.isPending || updateStaff.isPending} className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer">
                  {modal.edit ? 'Update' : 'Save'} Staff
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Delete confirmation */}
      {deleteId && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setDeleteId(null)}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 p-6" onClick={e => e.stopPropagation()}>
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Staff?</h3>
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
