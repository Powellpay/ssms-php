import { useState, useEffect } from 'react';
import { Check, X, Clock, Sunrise, Loader2, Users } from 'lucide-react';
import { useAttendanceRegister, useBulkMarkAttendance, type AttendanceRegisterRecord } from '../../../shared/api/attendance/attendanceQueries';
import SSMSLoader from '../../../shared/components/SSMSLoader';
import { cn } from '../../../shared/utils/cn';

const STATUS_OPTIONS = [
  { value: 'Present', label: 'P', color: 'bg-success text-white', hoverColor: 'hover:bg-success/80', icon: Check },
  { value: 'Absent', label: 'A', color: 'bg-alert-error-text text-white', hoverColor: 'hover:bg-alert-error-text/80', icon: X },
  { value: 'Late', label: 'L', color: 'bg-warning text-white', hoverColor: 'hover:bg-warning/80', icon: Clock },
  { value: 'Excused', label: 'E', color: 'bg-primary text-white', hoverColor: 'hover:bg-primary-dark', icon: Sunrise },
] as const;

interface AttendanceRegisterProps {
  termId: number;
  attendanceDate: string;
  streamId: number;
}

export default function AttendanceRegister({ termId, attendanceDate, streamId }: AttendanceRegisterProps) {
  const { data: records, isLoading } = useAttendanceRegister(termId, attendanceDate, streamId);
  const bulkMark = useBulkMarkAttendance();
  const [localRecords, setLocalRecords] = useState<AttendanceRegisterRecord[]>([]);

  useEffect(() => {
    if (records) {
      setLocalRecords(records);
    }
  }, [records]);

  const handleStatusChange = (studentId: number, status: AttendanceRegisterRecord['status']) => {
    setLocalRecords(prev => prev.map(r =>
      r.student_id === studentId ? { ...r, status } : r
    ));
  };

  const handleMarkAllPresent = () => {
    setLocalRecords(prev => prev.map(r => ({ ...r, status: 'Present' as const })));
  };

  const getStatusCount = (status: string) =>
    localRecords.filter(r => r.status === status).length;

  const handleSave = () => {
    const recordsToSave = localRecords
      .filter(r => r.status)
      .map(r => ({ student_id: r.student_id, status: r.status }));

    if (recordsToSave.length === 0) return;

    bulkMark.mutate({
      term_id: termId,
      attendance_date: attendanceDate,
      stream_id: streamId,
      records: recordsToSave,
    });
  };

  if (isLoading) return <SSMSLoader message="Loading register..." />;

  if (!localRecords.length) {
    return (
      <div className="rounded-xl border border-border bg-white p-8 text-center">
        <Users className="w-10 h-10 text-muted mx-auto mb-3" />
        <p className="text-sm text-muted">No enrolled students found for this stream.</p>
      </div>
    );
  }

  const markedCount = localRecords.filter(r => r.status).length;
  const allMarked = markedCount === localRecords.length;

  return (
    <div className="rounded-xl border border-border bg-white overflow-hidden">
      <div className="flex items-center justify-between px-4 py-3 border-b border-border bg-gray-50">
        <div className="flex items-center gap-4 text-sm text-gray-600">
          <span className="font-medium">{localRecords.length} students</span>
          <span className="text-success">{getStatusCount('Present')} Present</span>
          <span className="text-alert-error-text">{getStatusCount('Absent')} Absent</span>
          <span className="text-warning">{getStatusCount('Late')} Late</span>
          <span className="text-primary">{getStatusCount('Excused')} Excused</span>
        </div>
        <div className="flex items-center gap-2">
          {!allMarked && (
            <button
              onClick={handleMarkAllPresent}
              className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-border text-xs font-medium text-gray-600 hover:bg-gray-100 cursor-pointer"
            >
              <Check className="w-3.5 h-3.5 text-success" />
              Mark All Present
            </button>
          )}
          <button
            onClick={handleSave}
            disabled={bulkMark.isPending || markedCount === 0}
            className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-primary text-white text-xs font-medium hover:bg-primary-dark disabled:opacity-50 cursor-pointer"
          >
            {bulkMark.isPending ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : <Check className="w-3.5 h-3.5" />}
            {bulkMark.isPending ? 'Saving...' : `Save (${markedCount}/${localRecords.length})`}
          </button>
        </div>
      </div>

      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="bg-gray-50 border-b border-border">
              <th className="text-left p-3 text-sm font-semibold text-gray-600">#</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Student Name</th>
              <th className="text-left p-3 text-sm font-semibold text-gray-600">Admission No</th>
              <th className="text-center p-3 text-sm font-semibold text-gray-600" colSpan={4}>Status</th>
            </tr>
          </thead>
          <tbody>
            {localRecords.map((record, idx) => (
              <tr key={record.student_id} className="border-t border-border hover:bg-gray-50/50">
                <td className="p-3 text-sm text-muted">{idx + 1}</td>
                <td className="p-3 text-sm font-medium text-gray-900">
                  {record.first_name} {record.last_name}
                </td>
                <td className="p-3 text-sm text-gray-600">{record.admission_no}</td>
                <td className="p-3" colSpan={4}>
                  <div className="flex items-center justify-center gap-2">
                    {STATUS_OPTIONS.map(opt => {
                      const isActive = record.status === opt.value;
                      const Icon = opt.icon;
                      return (
                        <button
                          key={opt.value}
                          onClick={() => handleStatusChange(record.student_id, opt.value)}
                          className={cn(
                            'inline-flex items-center justify-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium transition-all border border-border cursor-pointer min-w-[60px]',
                            isActive ? opt.color : 'bg-white text-gray-500 hover:bg-gray-100'
                          )}
                        >
                          <Icon className={cn('w-3.5 h-3.5', isActive ? 'text-white' : 'text-gray-400')} />
                          {opt.label}
                        </button>
                      );
                    })}
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {bulkMark.isSuccess && (
        <div className="px-4 py-2 bg-success-light text-success text-sm font-medium border-t border-border">
          Attendance saved successfully!
        </div>
      )}

      {bulkMark.isError && (
        <div className="px-4 py-2 bg-alert-error-bg text-alert-error-text text-sm font-medium border-t border-border">
          Failed to save attendance. Please try again.
        </div>
      )}
    </div>
  );
}
