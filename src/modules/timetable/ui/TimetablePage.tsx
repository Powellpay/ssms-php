import { useState } from 'react';
import { useTimetableList, useCreateTimetable, useUpdateTimetable, useDeleteTimetable } from '../../../shared/api/timetable/timetableQueries';
import { useStreams, useAcademicYears } from '../../../shared/api/academic/academicQueries';
import { useSubjects } from '../../../shared/api/curriculum/curriculumQueries';
import { useStaffList } from '../../../shared/api/staff/staffQueries';
import { Timer, Plus, Pencil, Trash2, X } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { Timetable } from '../../../shared/types';

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'] as const;
const df: Partial<Timetable> = { stream_id: 0, subject_id: 0, staff_id: 0, academic_year_id: 0, day_of_week: 'Monday', period_no: 1, start_time: '', end_time: '' };

export default function TimetablePage() {
  const { data: list, isLoading } = useTimetableList();
  const { data: streams } = useStreams();
  const { data: subjects } = useSubjects();
  const { data: staff } = useStaffList();
  const { data: academicYears } = useAcademicYears();
  const create = useCreateTimetable();
  const update = useUpdateTimetable();
  const del = useDeleteTimetable();
  const [modal, setModal] = useState<{ open: boolean; edit?: Timetable }>({ open: false });
  const [form, setForm] = useState<Partial<Timetable>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: Timetable) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<Timetable> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  const handleDelete = (id: number) => del.mutate(id, { onSuccess: () => setDeleteId(null) });

  const streamMap = new Map(streams?.map(s => [s.id, s]));
  const subjectMap = new Map(subjects?.map(s => [s.id, s]));
  const staffMap = new Map(staff?.map(s => [s.id, s]));
  const yearMap = new Map(academicYears?.map(y => [y.id, y]));

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-4">
          <div className="p-3 rounded-xl bg-primary-light"><Timer className="w-8 h-8 text-primary" /></div>
          <div><h1 className="text-2xl font-bold text-gray-900">Timetable</h1><p className="text-muted text-sm mt-1">Build and view weekly timetables per stream with period slots.</p></div>
        </div>
        <button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Entry</button>
      </div>
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No timetable entries found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Stream</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Subject</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Staff</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Year</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Day</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Period</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Start</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">End</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: Timetable) => {
              const st = streamMap.get(item.stream_id);
              const sub = subjectMap.get(item.subject_id);
              const sf = staffMap.get(item.staff_id);
              const y = yearMap.get(item.academic_year_id);
              return (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm text-gray-600">{st?.stream_name ?? item.stream_id}</td>
                <td className="p-3 text-sm text-gray-600">{sub?.subject_name ?? item.subject_id}</td>
                <td className="p-3 text-sm text-gray-600">{sf ? `${sf.first_name} ${sf.last_name}` : item.staff_id}</td>
                <td className="p-3 text-sm text-gray-600">{y?.year_name ?? item.academic_year_id}</td>
                <td className="p-3 text-sm text-gray-600">{item.day_of_week}</td>
                <td className="p-3 text-sm text-gray-600">{item.period_no}</td>
                <td className="p-3 text-sm text-gray-600">{item.start_time}</td>
                <td className="p-3 text-sm text-gray-600">{item.end_time}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      {modal.open && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40" onClick={() => setModal({ open: false })}>
          <div className="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto" onClick={e => e.stopPropagation()}>
            <div className="flex items-center justify-between px-6 py-4 border-b border-border">
              <h2 className="text-lg font-bold">{modal.edit ? 'Edit Timetable Entry' : 'Add Timetable Entry'}</h2>
              <button onClick={() => setModal({ open: false })} className="p-1 text-muted hover:text-gray-900 cursor-pointer"><X className="w-5 h-5" /></button>
            </div>
            <form onSubmit={handleSubmit} className="p-6 space-y-4">
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Stream *</label><select value={form.stream_id || ''} onChange={(e) => setForm(p => ({ ...p, stream_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select stream</option>{streams?.map((s) => <option key={s.id} value={s.id}>{s.stream_name}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Subject *</label><select value={form.subject_id || ''} onChange={(e) => setForm(p => ({ ...p, subject_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select subject</option>{subjects?.map((s) => <option key={s.id} value={s.id}>{s.subject_name}</option>)}</select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Staff *</label><select value={form.staff_id || ''} onChange={(e) => setForm(p => ({ ...p, staff_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select staff</option>{staff?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Academic Year *</label><select value={form.academic_year_id || ''} onChange={(e) => setForm(p => ({ ...p, academic_year_id: Number(e.target.value) }))} required className="w-full px-3 py-2 border border-border rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary"><option value="">Select year</option>{academicYears?.map((y) => <option key={y.id} value={y.id}>{y.year_name}</option>)}</select></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Day *</label><select value={form.day_of_week || 'Monday'} onChange={setF('day_of_week')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none">{days.map(d => <option key={d} value={d}>{d}</option>)}</select></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Period No *</label><input type="number" value={form.period_no || 1} onChange={setF('period_no')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <div><label className="block text-sm font-medium text-gray-700 mb-1">Start Time *</label><input type="time" value={form.start_time || ''} onChange={setF('start_time')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
                <div><label className="block text-sm font-medium text-gray-700 mb-1">End Time *</label><input type="time" value={form.end_time || ''} onChange={setF('end_time')} required className="w-full px-3 py-2 border border-border rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" /></div>
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
            <h3 className="text-lg font-bold text-gray-900 mb-2">Delete Timetable Entry?</h3>
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
