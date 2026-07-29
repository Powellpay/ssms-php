import { useState } from 'react';
import { useAssessmentRecords, useCreateAssessmentRecord, useUpdateAssessmentRecord, useDeleteAssessmentRecord, useAssessmentTypes } from '../../../shared/api/assessment/assessmentQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useSubjects } from '../../../shared/api/curriculum/curriculumQueries';
import { useTerms } from '../../../shared/api/academic/academicQueries';
import { ClipboardCheck, Plus, Pencil, Trash2, User, BookOpen, FileText, Bookmark, CalendarDays, Award } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { AssessmentRecord } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { inputClass, selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';

const df: Partial<AssessmentRecord> = { student_id: 0, subject_id: 0, assessment_type_id: 0, term_id: 0, score: 0, max_score: 100, date_recorded: '' };

export default function AssessmentRecordsPage() {
  const { data: list, isLoading } = useAssessmentRecords();
  const { data: students } = useStudentList();
  const { data: subjects } = useSubjects();
  const { data: terms } = useTerms();
  const { data: assessmentTypes } = useAssessmentTypes();
  const create = useCreateAssessmentRecord();
  const update = useUpdateAssessmentRecord();
  const del = useDeleteAssessmentRecord();
  const [modal, setModal] = useState<{ open: boolean; edit?: AssessmentRecord }>({ open: false });
  const [form, setForm] = useState<Partial<AssessmentRecord>>(df);
  const [deleteId, setDeleteId] = useState<number | null>(null);

  const openAdd = () => { setForm(df); setModal({ open: true }); };
  const openEdit = (s: AssessmentRecord) => { setForm({ ...s }); setModal({ open: true, edit: s }); };
  const setF = (f: string) => (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => setForm(p => ({ ...p, [f]: e.target.value }));

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (modal.edit) update.mutate({ ...form, id: modal.edit.id } as Partial<AssessmentRecord> & { id: number }, { onSuccess: () => setModal({ open: false }) });
    else create.mutate(form, { onSuccess: () => setModal({ open: false }) });
  };

  const studentMap = new Map(students?.map(s => [s.id, s]));
  const subjectMap = new Map(subjects?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));
  const typeMap = new Map(assessmentTypes?.map(t => [t.id, t]));

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<ClipboardCheck className="w-8 h-8 text-primary" />}
        title="Assessment Records"
        description="Record CA and exam scores per learner per subject."
        action={<button onClick={openAdd} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Plus className="w-4 h-4" /> Add Record</button>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No assessment records found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Subject</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Assessment Type</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Score</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Max Score</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list.map((item: AssessmentRecord) => {
              const st = studentMap.get(item.student_id);
              const sub = subjectMap.get(item.subject_id);
              const t = termMap.get(item.term_id);
              const at = typeMap.get(item.assessment_type_id);
              return (
              <tr key={item.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm text-gray-600">{st ? `${st.first_name} ${st.last_name}` : item.student_id}</td>
                <td className="p-3 text-sm text-gray-600">{sub?.subject_name ?? item.subject_id}</td>
                <td className="p-3 text-sm text-gray-600">{at?.type_name ?? item.assessment_type_id}</td>
                <td className="p-3 text-sm text-gray-600">{t?.term_name ?? item.term_id}</td>
                <td className="p-3 text-sm text-gray-900 font-medium">{item.score ?? '-'}</td>
                <td className="p-3 text-sm text-gray-600">{item.max_score}</td>
                <td className="p-3 text-sm text-gray-600">{formatDate(item.date_recorded)}</td>
                <td className="p-3 text-right">
                  <button onClick={() => openEdit(item)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer"><Pencil className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(item.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      <Modal open={modal.open} onClose={() => setModal({ open: false })} title={modal.edit ? 'Edit Assessment Record' : 'Add Assessment Record'} subtitle="Record CA or exam scores for a learner" maxWidth="md">
        <form onSubmit={handleSubmit}>
          <div className="space-y-5 p-6">
            <FormSection title="Assessment Info" icon={ClipboardCheck} description="Student, subject, type, and term">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Student" icon={User} required>
                  <select value={form.student_id || ''} onChange={(e) => setForm(p => ({ ...p, student_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select student</option>{students?.map((s) => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}</select>
                </IconField>
                <IconField label="Subject" icon={BookOpen} required>
                  <select value={form.subject_id || ''} onChange={(e) => setForm(p => ({ ...p, subject_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select subject</option>{subjects?.map((s) => <option key={s.id} value={s.id}>{s.subject_name}</option>)}</select>
                </IconField>
              </div>
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Assessment Type" icon={FileText} required>
                  <select value={form.assessment_type_id || ''} onChange={(e) => setForm(p => ({ ...p, assessment_type_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select type</option>{assessmentTypes?.map((t) => <option key={t.id} value={t.id}>{t.type_name}</option>)}</select>
                </IconField>
                <IconField label="Term" icon={Bookmark} required>
                  <select value={form.term_id || ''} onChange={(e) => setForm(p => ({ ...p, term_id: Number(e.target.value) }))} required className={selectClass}><option value="">Select term</option>{terms?.map((t) => <option key={t.id} value={t.id}>{t.term_name}</option>)}</select>
                </IconField>
              </div>
            </FormSection>
            <FormSection title="Scoring" icon={Award} description="Score, max score, and recording date">
              <div className="grid grid-cols-2 gap-4">
                <IconField label="Score" icon={Award}>
                  <input type="number" value={form.score ?? 0} onChange={setF('score')} className={inputClass} />
                </IconField>
                <IconField label="Max Score" icon={Award} required>
                  <input type="number" value={form.max_score || 100} onChange={setF('max_score')} required className={inputClass} />
                </IconField>
              </div>
              <IconField label="Date Recorded" icon={CalendarDays} required>
                <input type="date" value={form.date_recorded || ''} onChange={setF('date_recorded')} required className={inputClass} />
              </IconField>
            </FormSection>
          </div>
          <ModalFooter onCancel={() => setModal({ open: false })} submitLabel={modal.edit ? 'Update' : 'Save'} submitting={create.isPending || update.isPending} />
        </form>
      </Modal>
      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Assessment Record?" message="This action cannot be undone." />
    </div>
  );
}
