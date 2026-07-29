import { useState } from 'react';
import { useAnnouncements, useCreateAnnouncement, useUpdateAnnouncement, useDeleteAnnouncement } from '../../../shared/api/announcements/announcementQueries';
import { Megaphone, Plus, Pencil, Trash2, MessageSquare, Target, Type } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { Announcement } from '../../../shared/types';
import { formatDateTime } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, textareaClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<Announcement> = { title: '', message: '', target_role: '' };

export default function AnnouncementsListPage() {
  const { data: list, isLoading } = useAnnouncements();
  const create = useCreateAnnouncement();
  const update = useUpdateAnnouncement();
  const del = useDeleteAnnouncement();
  const [modal, setModal] = useState<{ open: boolean; edit?: Announcement }>({ open: false });
  const [form, setForm] = useState<Partial<Announcement>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Announcement) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<Announcement> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<Megaphone className="w-8 h-8 text-primary" />}
        title="Announcements"
        description="Post school-wide or role-targeted notices."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Announcement</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No announcements found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Title</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Message</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Target Role</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Created At</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: Announcement) => (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm font-medium text-gray-900">{item.title}</td>
                <td className="p-3 text-sm text-gray-700 max-w-xs truncate">{item.message}</td>
                <td className="p-3 text-sm text-gray-600">{item.target_role}</td>
                <td className="p-3 text-sm text-gray-600">{formatDateTime(item.created_at)}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
            ))}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Announcement' : 'Add Announcement'} subtitle="Create a school-wide or role-targeted notice" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="p-6 space-y-5">
            <FormSection title="Announcement" icon={Megaphone} description="Title, message, and target audience">
              <IconField label="Title" icon={Type} required>
                <input value={form.title || ''} onChange={setF('title')} required className={inputClass} placeholder="Announcement title" />
              </IconField>
              <IconField label="Message" icon={MessageSquare} required>
                <textarea value={form.message || ''} onChange={setF('message')} required rows={4} className={textareaClass + ' resize-y'} />
              </IconField>
              <IconField label="Target Role" icon={Target} required hint="e.g. all, admin, teacher, parent">
                <input value={form.target_role || ''} onChange={setF('target_role')} required placeholder="e.g. all" className={inputClass} />
              </IconField>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Announcement?" message="This action cannot be undone." />
    </div>
  );
}
