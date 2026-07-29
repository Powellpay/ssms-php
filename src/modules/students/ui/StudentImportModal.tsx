import { useState, useRef } from 'react';
import { Upload, Download, FileSpreadsheet, AlertCircle, CheckCircle2, Loader2, Users, GitBranch, CalendarDays, XCircle } from 'lucide-react';
import { useImportStudents, useDownloadTemplate } from '../../../shared/api/students/studentQueries';
import { useStreams } from '../../../shared/api/academic/academicQueries';
import { useAcademicYears } from '../../../shared/api/academic/academicQueries';
import Modal from '../../../shared/components/ui/Modal';
import FormSection from '../../../shared/components/ui/FormSection';
import IconField, { selectClass } from '../../../shared/components/ui/IconField';

interface ImportResult {
  imported: number;
  total: number;
  errors: string[];
}

interface StudentImportModalProps {
  open: boolean;
  onClose: () => void;
  onSuccess: () => void;
}

export default function StudentImportModal({ open, onClose, onSuccess }: StudentImportModalProps) {
  const fileRef = useRef<HTMLInputElement>(null);
  const [file, setFile] = useState<File | null>(null);
  const [streamId, setStreamId] = useState<number>(0);
  const [academicYearId, setAcademicYearId] = useState<number>(0);
  const [result, setResult] = useState<ImportResult | null>(null);
  const [templateError, setTemplateError] = useState('');
  const [fileError, setFileError] = useState('');

  const { data: streams } = useStreams();
  const { data: academicYears } = useAcademicYears();
  const importMutation = useImportStudents();
  const downloadMutation = useDownloadTemplate();

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const f = e.target.files?.[0];
    if (f) {
      if (!f.name.endsWith('.csv')) {
        setFileError('Please select a CSV file.');
        return;
      }
      setFileError('');
      setFile(f);
      setResult(null);
    }
  };

  const handleDownload = () => {
    setTemplateError('');
    downloadMutation.mutate(undefined, {
      onSuccess: (data) => {
        const url = window.URL.createObjectURL(new Blob([data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'student-import-template.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
      },
      onError: () => setTemplateError('Failed to download template. Please try again.'),
    });
  };

  const handleImport = () => {
    if (!file || !streamId || !academicYearId) return;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('stream_id', String(streamId));
    formData.append('academic_year_id', String(academicYearId));

    importMutation.mutate(formData, {
      onSuccess: (data) => {
        setResult({ imported: data.imported ?? 0, total: data.total ?? data.imported ?? 0, errors: data.errors ?? [] });
        if ((data.errors ?? []).length === 0) {
          onSuccess();
          setTimeout(onClose, 1500);
        }
      },
      onError: (err: any) => {
        setResult({ imported: 0, total: 0, errors: [err?.response?.data?.message || 'Upload failed.'] });
      },
    });
  };

  const resetAndClose = () => {
    setFile(null);
    setStreamId(0);
    setAcademicYearId(0);
    setResult(null);
    onClose();
  };

  return (
    <Modal open={open} onClose={resetAndClose} title="Import Students" subtitle="Bulk upload students from a CSV file" maxWidth="lg">
      <div className="space-y-6 p-6">
        <FormSection title="Download Template" icon={Download} description="Use our CSV template to format your data correctly">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <FileSpreadsheet className="w-8 h-8 text-primary" />
              <div>
                <h3 className="font-medium text-gray-900">Template File</h3>
                <p className="text-sm text-muted">Contains the required columns and a sample row</p>
              </div>
            </div>
            <button
              onClick={handleDownload}
              disabled={downloadMutation.isPending}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-sm text-gray-600 hover:bg-gray-50 disabled:opacity-50 cursor-pointer"
            >
              {downloadMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Download className="w-4 h-4" />}
              Download
            </button>
          </div>
          {templateError && (
            <div className="flex items-center gap-2 mt-2 text-sm text-alert-error-text">
              <XCircle className="w-4 h-4 shrink-0" />
              <span>{templateError}</span>
            </div>
          )}
        </FormSection>

        <FormSection title="Upload CSV" icon={Upload} description="Select the completed CSV file to import">
          <div
            onClick={() => { setFileError(''); fileRef.current?.click(); }}
            className="border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-primary hover:bg-primary-light/20 cursor-pointer transition-colors"
          >
            <Upload className="w-10 h-10 text-muted mx-auto mb-3" />
            <p className="text-sm text-muted mb-1">{file ? file.name : 'Click to select a CSV file'}</p>
            <p className="text-xs text-muted/60">Required columns: first_name, last_name, gender</p>
            <input ref={fileRef} type="file" accept=".csv" onChange={handleFileChange} hidden />
          </div>
          {fileError && (
            <div className="flex items-center gap-2 text-sm text-alert-error-text mt-1">
              <XCircle className="w-4 h-4 shrink-0" />
              <span>{fileError}</span>
            </div>
          )}
        </FormSection>

        <FormSection title="Assignment" icon={Users} description="Assign imported students to a stream and academic year">
          <div className="grid grid-cols-2 gap-4">
            <IconField label="Stream" icon={GitBranch} required>
              <select value={streamId || ''} onChange={e => setStreamId(Number(e.target.value))} className={selectClass}>
                <option value="">Select stream</option>
                {streams?.map(s => <option key={s.id} value={s.id}>{s.stream_name}</option>)}
              </select>
            </IconField>
            <IconField label="Academic Year" icon={CalendarDays} required>
              <select value={academicYearId || ''} onChange={e => setAcademicYearId(Number(e.target.value))} className={selectClass}>
                <option value="">Select year</option>
                {academicYears?.map(y => <option key={y.id} value={y.id}>{y.year_name}</option>)}
              </select>
            </IconField>
          </div>
        </FormSection>

        {file && (
          <div className="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
            <span className="text-sm text-gray-700">{file.name} ({(file.size / 1024).toFixed(1)} KB)</span>
            <button
              onClick={handleImport}
              disabled={importMutation.isPending || !streamId || !academicYearId}
              className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer"
            >
              {importMutation.isPending ? <Loader2 className="w-4 h-4 animate-spin" /> : <Upload className="w-4 h-4" />}
              {importMutation.isPending ? 'Importing...' : 'Import'}
            </button>
          </div>
        )}

        {result && (
          <div className="bg-white rounded-xl border border-border p-6 space-y-3">
            <div className="flex items-center gap-3">
              {result.errors.length === 0
                ? <CheckCircle2 className="w-6 h-6 text-alert-success-text" />
                : <AlertCircle className="w-6 h-6 text-alert-error-text" />
              }
              <div>
                <h3 className="font-medium text-gray-900">
                  {result.imported > 0 ? `Imported ${result.imported} of ${result.total} student(s)` : 'Import completed with issues'}
                </h3>
                <p className="text-sm text-muted">{result.errors.length} error(s) found</p>
              </div>
            </div>
            {result.errors.length > 0 && (
              <div className="bg-alert-error-bg rounded-lg p-4 max-h-48 overflow-y-auto">
                {result.errors.map((err, i) => (
                  <p key={i} className="text-sm text-alert-error-text">{err}</p>
                ))}
              </div>
            )}
          </div>
        )}
      </div>
    </Modal>
  );
}
