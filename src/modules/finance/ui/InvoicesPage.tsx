import { useState } from 'react';
import { useInvoices as useList, useCreateInvoice as useCreate, useUpdateInvoice as useUpdate, useDeleteInvoice as useDelete } from '../../../shared/api/finance/financeQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useTerms } from '../../../shared/api/academic/academicQueries';
import { Plus, Pencil, Trash2, X, Wallet } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { Invoice } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';

const df: Partial<Invoice> = { student_id: undefined, term_id: undefined, total_amount: undefined, amount_paid: 0, issue_date: '', status: 'unpaid' };

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
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><Wallet className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Finance</h1><p className="text-muted text-sm mt-1">Invoices and fee tracking</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Invoice</button>
      </div>
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
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Invoice' : 'Add Invoice'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Student *</label><select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Term *</label><select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Total Amount *</label><input type="number" step="0.01" value={form.total_amount || ''} onChange={setF('total_amount')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Amount Paid</label><input type="number" step="0.01" value={form.amount_paid ?? 0} onChange={setF('amount_paid')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Issue Date *</label><input type="date" value={form.issue_date || ''} onChange={setF('issue_date')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Status</label><select value={form.status || 'unpaid'} onChange={setF('status')} className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="unpaid">Unpaid</option><option value="partial">Partial</option><option value="paid">Paid</option></select></div>
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
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Invoice?</h3>
            <p className="text-sm text-muted mb-6">This cannot be undone.</p>
            <div className="flex justify-end gap-3">
              <button onClick={() => setDeleteId(null)} className="px-4 py-2 border border-border rounded-lg text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Cancel</button>
              <button onClick={() => del.mutate(deleteId)} className="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700 cursor-pointer">Delete</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}
