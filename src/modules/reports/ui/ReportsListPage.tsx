import { useState } from 'react';
import { useReportCards as useList, useDeleteReportCard as useDelete, useGenerateReportCard } from '../../../shared/api/reports/reportQueries';
import { useStudentList } from '../../../shared/api/students/studentQueries';
import { useTerms, useStreams } from '../../../shared/api/academic/academicQueries';
import { BarChart3, User, BookOpen, FileDown, Sparkles, Trash2 } from 'lucide-react';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import type { ReportCard } from '../../../shared/types';
import { formatDate } from '../../../shared/utils/formatDate';
import { downloadPdf } from '../../../shared/utils/pdfDownload';
import Modal from '../../../shared/components/ui/Modal';
import ConfirmDialog from '../../../shared/components/ui/ConfirmDialog';
import IconField, { selectClass } from '../../../shared/components/ui/IconField';
import ModalFooter from '../../../shared/components/ui/ModalFooter';
import PageHeader from '../../../shared/components/ui/PageHeader';


export default function ReportsListPage() {
  const { data: list, isLoading } = useList();
  const { data: students } = useStudentList();
  const { data: terms } = useTerms();
  const { data: streams } = useStreams();
  const del = useDelete();
  const generate = useGenerateReportCard();
  const [deleteId, setDeleteId] = useState<number | null>(null);
  const [genModal, setGenModal] = useState(false);
  const [genStudentId, setGenStudentId] = useState<number>(0);
  const [genTermId, setGenTermId] = useState<number>(0);

  const studentMap = new Map(students?.map(s => [s.id, s]));
  const termMap = new Map(terms?.map(t => [t.id, t]));
  const streamMap = new Map(streams?.map(s => [s.id, s]));

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<BarChart3 className="w-8 h-8 text-primary" />}
        title="Report Cards"
        description="Generate and manage learner report cards"
        action={<div className="flex items-center gap-2">
          <button onClick={() => setGenModal(true)} className="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark cursor-pointer"><Sparkles className="w-4 h-4" /> Generate</button>
        </div>}
      />
      <div className="rounded-xl border border-border bg-white overflow-hidden">
        {isLoading ? <SSMSLoader />
        : !list?.length ? <div className="p-8 text-center text-muted">No report cards found.</div>
        : <div className="overflow-x-auto"><table className="w-full">
            <thead><tr className="bg-gray-50">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Term</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Stream</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Present</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600">Absent</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Date Issued</th>
              <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>
            </tr></thead>
            <tbody>{list?.map((r: ReportCard) => {
              const st = studentMap.get(r.student_id);
              const t = termMap.get(r.term_id);
              const str = streamMap.get(r.stream_id);
              return (
              <tr key={r.id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm">{st ? `${st.first_name} ${st.last_name}` : r.student_id}</td>
                <td className="p-3 text-sm">{t?.term_name ?? r.term_id}</td>
                <td className="p-3 text-sm">{str?.stream_name ?? r.stream_id}</td>
                <td className="p-3 text-sm text-center">{r.days_present}</td>
                <td className="p-3 text-sm text-center">{r.days_absent}</td>
                <td className="p-3 text-sm">{formatDate(r.date_issued)}</td>
                <td className="p-3 text-right">
                  <button onClick={() => downloadPdf(`/report-cards/${r.id}/pdf`, `report-card-${r.id}.pdf`)} className="p-1.5 text-muted hover:text-primary rounded cursor-pointer" title="Download PDF"><FileDown className="w-4 h-4" /></button>
                  <button onClick={() => setDeleteId(r.id)} className="p-1.5 text-muted hover:text-red-600 rounded cursor-pointer"><Trash2 className="w-4 h-4" /></button>
                </td>
              </tr>
              );
            })}</tbody></table></div>}
      </div>
      <Modal open={genModal} onClose={() => { setGenModal(false); setGenStudentId(0); setGenTermId(0); }} title="Generate Report Card" subtitle="Select student and term to generate a report card" maxWidth="sm">
        <div className="p-6 space-y-4">
          <IconField label="Student" icon={User} required>
            <select value={genStudentId || ''} onChange={e => setGenStudentId(Number(e.target.value))} className={selectClass}>
              <option value="">Select student</option>
              {students?.map(s => <option key={s.id} value={s.id}>{s.first_name} {s.last_name}</option>)}
            </select>
          </IconField>
          <IconField label="Term" icon={BookOpen} required>
            <select value={genTermId || ''} onChange={e => setGenTermId(Number(e.target.value))} className={selectClass}>
              <option value="">Select term</option>
              {terms?.map(t => <option key={t.id} value={t.id}>{t.term_name}</option>)}
            </select>
          </IconField>
        </div>
        <ModalFooter
          onCancel={() => { setGenModal(false); setGenStudentId(0); setGenTermId(0); }}
          submitLabel="Generate"
          submitting={generate.isPending}
          onSubmit={() => {
            if (genStudentId && genTermId) {
              generate.mutate({ student_id: genStudentId, term_id: genTermId }, {
                onSuccess: () => { setGenModal(false); setGenStudentId(0); setGenTermId(0); },
              });
            }
          }}
          submitType="button"
        />
      </Modal>

      <ConfirmDialog open={deleteId !== null} onClose={() => setDeleteId(null)} onConfirm={() => del.mutate(deleteId!, { onSuccess: () => setDeleteId(null) })} title="Delete Report?" message="This cannot be undone." />
    </div>
  );
}
