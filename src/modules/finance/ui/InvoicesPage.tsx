import { useState } from 'react';
import { useInvoices as useList, useCreateInvoice as useCreate, useUpdateInvoice as useUpdate, useDeleteInvoice as useDelete } from '../../../shared/api/finance/financeQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useTerms } from '../../../shared/api/academic/academicQueries';
import { Plus, Pencil, Trash2, Wallet, User, BookOpen, DollarSign, CalendarDays } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { Invoice } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<Invoice> = { student_id: undefined, term_id: undefined, total_amount: undefined, amount_paid: 0, issue_date: '', status: 'unpaid' };
const statuses = ['unpaid', 'partial', 'paid'] as const;

export default function InvoicesPage() {
  const { data: list, isLoading } = useList();
  const { data: students } = useStudentList();
  const { data: terms } = useTerms();
  const create = useCreate();
  const update = useUpdate();
  const del = useDelete();
  const [modal, setModal] = useState<{ open: boolean; edit?: Invoice }>({ open: false });
  const [form, setForm] = useState<Partial<Invoice>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Invoice) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as any, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form as any, { onSuccess: () => setModal({ open: false }) });
  };

  const statusCls = (s: string) => s === 'paid' ? 'bg-success-light text-success' : s === 'partial' ? 'bg-warning-light text-warning' : 'bg-alert-error-bg text-alert-error-text';
  const studentMap = new Map(students?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<Wallet className="w-8 h-8 text-primary" />}
        title="Finance"
        description="Invoices and fee tracking"
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Invoice</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No invoices found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Total</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Paid</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Balance</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Status</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Issue Date</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((s: Invoice) => {
              const st = studentMap.get(s.student_id);
              const t = termMap.get(s.term_id);
              return (
              <tr key={s.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm">{st ? `${st.first_name} ${st.last_name}` : s.student_id}</td>
                <td className="p-3 text-sm">{t?.term_name ?? s.term_id}</td>
                <td className="p-3 text-sm text-right">{Number(s.total_amount).toLocaleString()}</td>
                <td className="p-3 text-sm text-right">{Number(s.amount_paid).toLocaleString()}</td>
                <td className="p-3 text-sm text-right font-semibold">{Number(s.balance).toLocaleString()}</td>
                <td className="p-3 text-sm"><span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${statusCls(s.status)}`}>{s.status}</span></td>
                <td className="p-3 text-sm">{formatDate(s.issue_date)}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(s)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(s.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Invoice' : 'Add Invoice'} subtitle="Create a fee invoice for a student" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Billing Info" icon={Wallet} description="Student, term, and dates">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Student" icon={User} required>
                  <select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select>
                </IconField>
                <IconField label="Term" icon={BookOpen} required>
                  <select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select>
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Total Amount" icon={DollarSign} required>
                  <input type="number" step="0.01" value={form.total_amount || ''} onChange={setF('total_amount')} required className={inputClass} />
                </IconField>
                <IconField label="Amount Paid" icon={DollarSign}>
                  <input type="number" step="0.01" value={form.amount_paid ?? 0} onChange={setF('amount_paid')} className={inputClass} />
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Issue Date" icon={CalendarDays} required>
                  <input type="date" value={form.issue_date || ''} onChange={setF('issue_date')} required className={inputClass} />
                </IconField>
                <IconField label="Status" icon={Wallet}>
                  <select value={form.status || 'unpaid'} onChange={setF('status')} className={selectClass}>{statuses.map(s => <option key={s} value={s}>{s}</option>)}</select>
                </IconField>
              </div>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Invoice?" message="This cannot be undone." />
    </div>
  );
}
