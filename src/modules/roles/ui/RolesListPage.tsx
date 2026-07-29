import { useState } from 'react';
import { useRoleList, useCreateRole, useUpdateRole, useDeleteRole } from '../../../shared/api/roles/roleQueries';
import PageHeader from '../../../shared/components/ui/PageHeader';
import DataTable from '../../../shared/components/ui/DataTable';
import type { Column } from '../../../shared/components/ui/DataTable';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass } from '../../../shared/components/ui/IconField';
import LoadingSpinner from '../../../shared/components/ui/LoadingSpinner';
import { Shield, Plus, Pencil, Trash2, Hash, FileText } from 'lucide-react';
import type { Role } from '../../../shared/types';

interface RoleForm {
  role_name: string;
  slug?: string;
  description?: string;
}

const emptyForm: RoleForm = { role_name: '', slug: '', description: '' };

function generateSlug(name: string): string {
  return name.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
}

export default function RolesListPage() {
  const { data: roles, isLoading } = useRoleList();
  const createRole = useCreateRole();
  const updateRole = useUpdateRole();
  const deleteRole = useDeleteRole();

  const [modalOpen, setModalOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [form, setForm] = useState<RoleForm>(emptyForm);
  const [deleteTarget, setDeleteTarget] = useState<{ id: number; name: string } | null>(null);

  const openAdd = () => {
    setEditingId(null);
    setForm(emptyForm);
    setModalOpen(true);
  };

  const openEdit = (role: Role) => {
    setEditingId(role.id);
    setForm({
      role_name: role.role_name,
      slug: generateSlug(role.role_name),
      description: role.description ?? '',
    });
    setModalOpen(true);
  };

  const handleSave = async () => {
    if (!form.role_name.trim()) return;
    const payload = { ...form, slug: generateSlug(form.role_name) };
    if (editingId) {
      await updateRole.mutateAsync({ id: editingId, payload });
    } else {
      await createRole.mutateAsync(payload);
    }
    setModalOpen(false);
  };

  const handleDelete = async () => {
    if (!deleteTarget) return;
    await deleteRole.mutateAsync(deleteTarget.id);
    setDeleteTarget(null);
  };

  if (isLoading) return <LoadingSpinner />;

  const columns: Column<Role>[] = [
    { key: 'role_name', label: 'Role Name' },
    { key: 'description', label: 'Description', render: (r) => r.description || '-' },
  ];

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<Shield className="w-6 h-6 text-primary" />}
        title="Roles"
        description="Manage user roles and permissions"
        action={
          <button
            onClick={openAdd}
            className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"
          >
            <Plus className="w-4 h-4" />
            Add Role
          </button>
        }
      />

      <DataTable
        columns={columns}
        data={roles ?? []}
        isLoading={false}
        keyExtractor={(r) => r.id}
        actions={(role) => (
          <div className="flex items-center justify-end gap-2">
            <button
              onClick={() => openEdit(role)}
              className="p-1.5 text-gray-400 hover:text-primary rounded-lg hover:bg-primary-light cursor-pointer"
              title="Edit"
            >
              <Pencil className="w-4 h-4" />
            </button>
            <button
              onClick={() => setDeleteTarget({ id: role.id, name: role.role_name })}
              className="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-red-50 cursor-pointer"
              title="Delete"
            >
              <Trash2 className="w-4 h-4" />
            </button>
          </div>
        )}
      />

      <Modal
        open={modalOpen}
        onClose={() => setModalOpen(false)}
        title={editingId ? 'Edit Role' : 'Add Role'}
        subtitle={editingId ? 'Update role details' : 'Create a new user role'}
      >
        <div className="p-6 space-y-4">
          <FormSection title="Role Details" icon={Shield}>
            <IconField label="Role Name" icon={Hash} required>
              <input
                className={inputClass}
                value={form.role_name}
                onChange={(e) => {
                  const name = e.target.value;
                  setForm({ ...form, role_name: name, slug: generateSlug(name) });
                }}
                placeholder="e.g. Head Teacher"
              />
            </IconField>

            <IconField label="Slug" icon={Hash} hint="Auto-generated from role name">
              <input
                className={`${inputClass} bg-gray-50 text-gray-500`}
                value={form.slug ?? ''}
                readOnly
                placeholder="head-teacher"
              />
            </IconField>

            <IconField label="Description" icon={FileText}>
              <textarea
                className={`${inputClass} min-h-[80px]`}
                value={form.description ?? ''}
                onChange={(e) => setForm({ ...form, description: e.target.value })}
                placeholder="Optional description"
              />
            </IconField>
          </FormSection>

          <div className="flex justify-end gap-3 pt-2">
            <button
              onClick={() => setModalOpen(false)}
              className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer"
            >
              Cancel
            </button>
            <button
              onClick={handleSave}
              disabled={createRole.isPending || updateRole.isPending || !form.role_name.trim()}
              className="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer"
            >
              {createRole.isPending || updateRole.isPending ? 'Saving...' : 'Save'}
            </button>
          </div>
        </div>
      </Modal>

      <ConfirmDialog
        open={!!deleteTarget}
        onClose={() => setDeleteTarget(null)}
        onConfirm={handleDelete}
        title="Delete Role"
        message={`Are you sure you want to delete "${deleteTarget?.name}"? This action cannot be undone.`}
        isLoading={deleteRole.isPending}
      />
    </div>
  );
}
