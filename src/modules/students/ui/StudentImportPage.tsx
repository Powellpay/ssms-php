import { useState, useRef } from 'react';
import { useNavigate } from 'react-router-dom';
import { Upload, Download, FileSpreadsheet, AlertCircle, CheckCircle2, ArrowLeft, Loader2 } from 'lucide-react';
import api from '../../../shared/api/axiosConfig';
import { ENDPOINTS } from '../../../shared/api/endpoints';
import { ROUTES } from '../../../app/routes/constants';
import PageHeader from '../../../shared/components/ui/PageHeader';

interface ImportResult {
  imported: number;
  errors: string[];
}

export default function StudentImportPage() {
  const navigate = useNavigate();
  const fileRef = useRef<HTMLInputElement>(null);
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [result, setResult] = useState<ImportResult | null>(null);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const f = e.target.files?.[0];
    if (f) {
      if (!f.name.endsWith('.csv')) {
        alert('Please select a CSV file.');
        return;
      }
      setFile(f);
      setResult(null);
    }
  };

  const handleUpload = async () => {
    if (!file) return;
    setUploading(true);
    setResult(null);

    const formData = new FormData();
    formData.append('file', file);

    try {
      const { data } = await api.post(ENDPOINTS.STUDENTS_IMPORT, formData);
      setResult({ imported: data.imported ?? 0, errors: data.errors ?? [] });
    } catch (err: any) {
      setResult({ imported: 0, errors: [err?.response?.data?.message || 'Upload failed.'] });
    } finally {
      setUploading(false);
    }
  };

  const downloadTemplate = () => {
    const baseUrl = import.meta.env.VITE_API_BASE_URL?.replace(/\/api$/, '') || '';
    window.open(`${baseUrl}${ENDPOINTS.STUDENTS_IMPORT_TEMPLATE}`, '_blank');
  };

  return (
    <div className="space-y-6">
      <PageHeader
        icon={<ArrowLeft className="w-8 h-8 text-primary cursor-pointer" onClick={() => navigate(ROUTES.STUDENTS.LIST)} />}
        title="Import Students"
        description="Bulk upload students from a CSV file"
      />

      <div className="max-w-2xl mx-auto space-y-6">
        {/* Template download */}
        <div className="bg-white rounded-xl border border-border p-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
              <FileSpreadsheet className="w-8 h-8 text-primary" />
              <div>
                <h3 className="font-medium text-gray-900">Download Template</h3>
                <p className="text-sm text-muted">Use our CSV template to format your data correctly</p>
              </div>
            </div>
            <button onClick={downloadTemplate} className="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-border text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">
              <Download className="w-4 h-4" /> Template
            </button>
          </div>
        </div>

        {/* File upload */}
        <div className="bg-white rounded-xl border border-border p-6">
          <h3 className="font-medium text-gray-900 mb-4">Upload CSV</h3>
          <div
            onClick={() => fileRef.current?.click()}
            className="border-2 border-dashed border-border rounded-xl p-8 text-center hover:border-primary hover:bg-primary-light/20 cursor-pointer transition-colors"
          >
            <Upload className="w-10 h-10 text-muted mx-auto mb-3" />
            <p className="text-sm text-muted mb-1">{file ? file.name : 'Click to select a CSV file'}</p>
            <p className="text-xs text-muted/60">Required columns: first_name, last_name, gender</p>
            <input ref={fileRef} type="file" accept=".csv" onChange={handleFileChange} hidden />
          </div>

          {file && (
            <div className="mt-4 flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3">
              <span className="text-sm text-gray-700">{file.name} ({(file.size / 1024).toFixed(1)} KB)</span>
              <button
                onClick={handleUpload}
                disabled={uploading}
                className="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer"
              >
                {uploading ? <Loader2 className="w-4 h-4 animate-spin" /> : <Upload className="w-4 h-4" />}
                {uploading ? 'Importing...' : 'Import'}
              </button>
            </div>
          )}
        </div>

        {/* Results */}
        {result && (
          <div className="bg-white rounded-xl border border-border p-6 space-y-3">
            <div className="flex items-center gap-3">
              {result.errors.length === 0
                ? <CheckCircle2 className="w-6 h-6 text-success" />
                : <AlertCircle className="w-6 h-6 text-warning" />
              }
              <div>
                <h3 className="font-medium text-gray-900">
                  {result.imported > 0 ? `Imported ${result.imported} student(s)` : 'Import completed with issues'}
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

        {/* Back button */}
        <div className="text-center">
          <button onClick={() => navigate(ROUTES.STUDENTS.LIST)} className="text-sm text-primary hover:text-primary-dark cursor-pointer">
            &larr; Back to Students
          </button>
        </div>
      </div>
    </div>
  );
}
