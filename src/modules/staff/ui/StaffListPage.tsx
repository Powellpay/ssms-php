import { useState } from 'react';
import { useStaffList, useCreateStaff, useUpdateStaff, useDeleteStaff } from '../../../shared/api/staff/staffQueries';
import { Plus, Pencil, Trash2, UserCircle, CheckCircle2, Hash, User, Tag, Phone, Mail, Lock, Shield, CalendarDays } from 'lucide-react';
import type { ModuleSlug, Staff } from '../../../shared/types';
import PageHeader from '../../../shared/components/ui/PageHeader';
import DataTable from '../../../shared/components/ui/DataTable';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';

const MODULES: { slug: ModuleSlug; label: string }[] = [
  { slug: 'dashboard', label: 'Dashboard' },
  { slug: 'academic', label: 'Academic' },
  { slug: 'staff', label: 'Staff' },
  { slug: 'students', label: 'Students' },
  { slug: 'curriculum', label: 'Curriculum' },
  { slug: 'assessment', label: 'Assessment' },
  { slug: 'reports', label: 'Reports' },
  { slug: 'attendance', label: 'Attendance' },
  { slug: 'timetable', label: 'Timetable' },
  { slug: 'finance', label: 'Finance' },
  { slug: 'discipline', label: 'Discipline' },
  { slug: 'library', label: 'Library' },
  { slug: 'announcements', label: 'Announcements' },
];

interface StaffForm {
  staff_no: string; first_name: string; last_name: string; gender: 'Male' | 'Female';
  email: string; phone: string; designation: string; status: string; dob: string;
  password: string; password_confirmation: string;
}

const defaultForm: StaffForm = {
  staff_no: '', first_name: '', last_name: '', gender: 'Male', email: '',
  phone: '', designation: '', status: 'active', dob: '',
  password: '', password_confirmation: '',
};

const columns = [
  { key: 'staff_no', label: 'Staff No', className: 'font-medium text-gray-900' },
  { key: 'name', label: 'Name', render: (item: Staff) => <span className="text-gray-700">{item.first_name} {item.last_name}</span> },
  { key: 'gender', label: 'Gender' },
  { key: 'designation', label: 'Designation', render: (item: Staff) => item.designation || '-' },
  { key: 'email', label: 'Login Email', render: (item: Staff) => item.user?.email || item.email || '-' },
  {
    key: 'user_status', label: 'User',
    render: (item: Staff) => item.user
      ? <span className="inline-flex items-center gap-1 text-xs text-success"><CheckCircle2 className="w-3 h-3" /> Active</span>
      : <span className="text-xs text-muted">No account</span>,
  },
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
  const [form, setForm] = useState<StaffForm>(defaultForm);
  const [selectedModules, setSelectedModules] = useState<ModuleSlug[]>([]);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(defaultForm); setSelectedModules([]); setModal({ open: true }); };
  const openEdit = (s: Staff) => {
    setForm({
      staff_no: s.staff_no, first_name: s.first_name, last_name: s.last_name,
      gender: s.gender, email: s.user?.email || s.email || '',
      phone: s.phone || '', designation: s.designation || '',
      status: s.status, dob: s.dob || '',
      password: '', password_confirmation: '',
    });
    setSelectedModules(s.user?.modules || []);
    setModal({ open: true, edit: s });
  };

  const toggleModule = (slug: ModuleSlug) => {
    setSelectedModules(prev =>
      prev.includes(slug) ? prev.filter(m => m !== slug) : [...prev, slug],
    );
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const payload: Record<string, unknown> = { ...form, modules: selectedModules.length > 0 ? selectedModules : undefined };
    if (!payload.password) { delete payload.password; delete payload.password_confirmation; }
    if (modal.edit) {
      payload.id = modal.edit.id;
      updateStaff.mutate(payload as Parameters<typeof updateStaff.mutate>[0], { onSuccess: () => setModal({ open: false }) });
    } else {
      createStaff.mutate(payload as Parameters<typeof createStaff.mutate>[0], { onSuccess: () => setModal({ open: false }) });
    }
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
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Staff' : 'Add Staff'} subtitle="Register a staff member with login credentials" maxWidth="lg">
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Identity" icon={User} description="Staff number, name, and gender">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Staff No" icon={Hash} required>
                  <input value={form.staff_no} onChange={set('staff_no')} required className={inputClass} placeholder="e.g. STF-001" />
                </IconField>
                <IconField label="Designation" icon={Tag}>
                  <input value={form.designation} onChange={set('designation')} className={inputClass} placeholder="e.g. Teacher" />
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="First Name" icon={User} required>
                  <input value={form.first_name} onChange={set('first_name')} required className={inputClass} />
                </IconField>
                <IconField label="Last Name" icon={User} required>
                  <input value={form.last_name} onChange={set('last_name')} required className={inputClass} />
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Gender" icon={User} required>
                  <select value={form.gender} onChange={set('gender')} className={selectClass}><option value="Male">Male</option><option value="Female">Female</option></select>
                </IconField>
                <IconField label="Status" icon={User}>
                  <select value={form.status} onChange={set('status')} className={selectClass}><option value="active">Active</option><option value="on leave">On Leave</option><option value="left">Left</option></select>
                </IconField>
              </div>
            </FormSection>
            <FormSection title="Contact" icon={Mail} description="Email, phone, and date of birth">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Email (Login)" icon={Mail} required>
                  <input type="email" value={form.email} onChange={set('email')} className={inputClass} placeholder="email@example.com" />
                </IconField>
                <IconField label="Phone" icon={Phone}>
                  <input value={form.phone} onChange={set('phone')} className={inputClass} placeholder="+256..." />
                </IconField>
              </div>
              <IconField label="Date of Birth" icon={CalendarDays}>
                <input type="date" value={form.dob} onChange={set('dob')} className={inputClass} />
              </IconField>
            </FormSection>
            <FormSection title="Account" icon={Lock} description="Set login password">
              <div className="grid grid-cols-2 gap-4">
                <IconField label={`Password${!modal.edit?.user ? ' *' : ''}`} icon={Lock}>
                  <input type="password" value={form.password} onChange={set('password')} className={inputClass} placeholder={modal.edit?.user ? 'Leave blank to keep current' : 'Password'} />
                </IconField>
                <IconField label="Confirm Password" icon={Lock}>
                  <input type="password" value={form.password_confirmation} onChange={set('password_confirmation')} className={inputClass} />
                </IconField>
              </div>
            </FormSection>
            <FormSection title="Permissions" icon={Shield} description="Select modules this staff can access">
              <div className="grid grid-cols-3 gap-2">
                {MODULES.map(mod => (
                  <label key={mod.slug} className="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                    <input
                      type="checkbox"
                      checked={selectedModules.includes(mod.slug)}
                      onChange={() => toggleModule(mod.slug)}
                      className="rounded border-border text-primary focus:ring-primary"
                    />
                    {mod.label}
                  </label>
                ))}
              </div>
              <p className="text-xs text-muted mt-1">All modules if none selected.</p>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update Staff' : 'Save Staff'} submitting={createStaff.isPending || updateStaff.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => deleteStaff.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Staff?" message="This action cannot be undone." isLoading={deleteStaff.isPending} />
    </div>
  );
}
