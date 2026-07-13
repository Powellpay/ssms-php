import { useState } from 'react';
import { useStaffList, useCreateStaff, useUpdateStaff, useDeleteStaff } from '../../../shared/api/staff/staffQueries';
import { Plus, Pencil, Trash2, UserCircle } from 'lucide-react';
import type { Staff } from '../../../shared/types';
import PageHeader from '../../../shared/components/ui/PageHeader';
import DataTable from '../../../shared/components/ui/DataTable';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';

const defaultForm: Partial<Staff> = { staff_no: '', first_name: '', last_name: '', gender: 'Male', email: '', phone: '', designation: '', status: 'active', dob: '' };

const columns = [
  { key: 'staff_no', label: 'Staff No', className: 'font-medium text-gray-900' },
  { key: 'name', label: 'Name', render: (item: Staff) => <span className="text-gray-700">{item.first_name} {item.last_name}</span> },
  { key: 'gender', label: 'Gender' },
  { key: 'designation', label: 'Designation', render: (item: Staff) => item.designation || '-' },
  { key: 'email', label: 'Email', render: (item: Staff) => item.email || '-' },
  { key: 'phone', label: 'Phone', render: (item: Staff) => item.phone || '-' },
  {
    key: 'status', label: 'Status',
    render: (item: Staff) => (
      <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${item.status === 'active' ? 'bg-success-light text-success' : item.status === 'on leave' ? 'bg-warning-light text-warning' : 'bg-alert-error-bg text-alert-error-text'}`}>{item.status}</span>
    ),
  },
];

export default function StaffListPage() {
  const { data: staffList, isLoading } = useStaffList();
  const createStaff = useCreateStaff();
  const updateStaff = useUpdateStaff();
  const deleteStaff = useDeleteStaff();
  const [modal, setModal] = useState<{ open: boolean; edit?: Staff }>({ open: false });
  const [form, setForm] = useState(defaultForm);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(defaultForm); setModal({ open: true }); };
  const openEdit = (s: Staff) => { setForm({ staff_no: s.staff_no, first_name: s.first_name, last_name: s.last_name, gender: s.gender, email: s.email || '', phone: s.phone || '', designation: s.designation || '', status: s.status, dob: s.dob || '' }); setModal({ open: true, edit: s }); };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) {
      updateStaff.mutate({ ...form, id: modal.edit.id } as Partial<Staff> & { id: number }, { onSuccess: () => setModal({ open: false }) });
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
      <PageHeader
        icon={<UserCircle className="w-8 h-8 text-primary" />}
        title="Staff"
        description="Manage teacher and non-teaching staff"
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark transition-colors cursor-pointer"><Plus className="w-4 h-4" /> Add Staff</button>}
      />
      <DataTable
        columns={columns}
        data={staffList}
        isLoading={isLoading}
        emptyMessage='No staff records found. Click "Add Staff" to create one.'
        keyExtractor={(item: Staff) => item.id}
        actions={(item: Staff) => (
          <>
            <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
            <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-alert-error-text rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
          </>
        )}
      />
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Staff' : 'Add Staff'}>
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
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => handleDelete(deleteId!)} title="Delete Staff?" message="This action cannot be undone." isLoading={deleteStaff.isPending} />
    </div>
  );
}
