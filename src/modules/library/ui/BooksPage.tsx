import { useState } from 'react';
import { useLibraryBooks as useList, useCreateLibraryBook as useCreate, useUpdateLibraryBook as useUpdate, useDeleteLibraryBook as useDelete } from '../../../shared/api/library/libraryQueries';
import { Plus, Pencil, Trash2, Library, BookOpen, User, Hash, Bookmark, Layers } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { LibraryBook } from '../../../shared/types';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<LibraryBook> = { title: '', author: '', isbn: '', category: '', total_copies: 1, available_copies: 1 };

export default function BooksPage() {
  const { data: list, isLoading } = useList();
  const create = useCreate();
  const update = useUpdate();
  const del = useDelete();
  const [modal, setModal] = useState<{ open: boolean; edit?: LibraryBook }>({ open: false });
  const [form, setForm] = useState<Partial<LibraryBook>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: LibraryBook) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<Library className="w-8 h-8 text-primary" />}
        title="Library"
        description="Book catalogue and management"
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Book</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No books in catalogue.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Title</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Author</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">ISBN</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Category</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Total</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Available</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((b: LibraryBook) => (
              <tr key={b.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm font-medium text-gray-900">{b.title}</td>
                <td className="p-3 text-sm text-gray-600">{b.author || '-'}</td>
                <td className="p-3 text-sm text-gray-600">{b.isbn || '-'}</td>
                <td className="p-3 text-sm text-gray-600">{b.category || '-'}</td>
                <td className="p-3 text-sm text-center">{b.total_copies}</td>
                <td className="p-3 text-sm text-center"><span className={b.available_copies > 0 ? 'text-success font-medium' : 'text-alert-error-text font-medium'}>{b.available_copies}</span></td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(b)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(b.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
            ))}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Book' : 'Add Book'} subtitle="Add a book to the library catalogue" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="p-6 space-y-5">
            <FormSection title="Book Details" icon={BookOpen} description="Title, author, ISBN, and category">
              <IconField label="Title" icon={BookOpen} required>
                <input value={form.title || ''} onChange={setF('title')} required className={inputClass} placeholder="Book title" />
              </IconField>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Author" icon={User}>
                  <input value={form.author || ''} onChange={setF('author')} className={inputClass} placeholder="Author name" />
                </IconField>
                <IconField label="ISBN" icon={Hash}>
                  <input value={form.isbn || ''} onChange={setF('isbn')} className={inputClass} placeholder="ISBN number" />
                </IconField>
              </div>
              <IconField label="Category" icon={Bookmark}>
                <input value={form.category || ''} onChange={setF('category')} className={inputClass} placeholder="e.g. Fiction, Textbook" />
              </IconField>
            </FormSection>
            <FormSection title="Inventory" icon={Layers} description="Total and available copies">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Total Copies" icon={Layers} required>
                  <input type="number" min="1" value={form.total_copies || 1} onChange={setF('total_copies')} required className={inputClass} />
                </IconField>
                <IconField label="Available" icon={Layers}>
                  <input type="number" min="0" value={form.available_copies ?? form.total_copies} onChange={setF('available_copies')} className={inputClass} />
                </IconField>
              </div>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Book?" message="This cannot be undone." />
    </div>
  );
}
