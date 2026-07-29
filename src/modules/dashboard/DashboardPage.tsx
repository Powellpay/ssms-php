import { Link } from 'react-router-dom';
import { useAppSelector } from '../../app/store/hooks';
import { useClassLevels, useStreams } from '../../shared/api/academic/academicQueries';
import { useStudentList } from '../../shared/api/students/studentQueries';
import { useStaffList } from '../../shared/api/staff/staffQueries';
import { ROUTES } from '../../app/routes/constants';
import { Users, GraduationCap, School, Briefcase, CalendarDays, ArrowRight, BookOpen } from 'lucide-react';
import StatCard from '../../shared/components/ui/StatCard';
import LoadingSpinner from '../../shared/components/ui/LoadingSpinner';
import { GenderChart, StatusChart } from './DashboardCharts';
import { useActiveTerm } from './useDashboardQueries';

export default function DashboardPage() {
  const user = useAppSelector((s) => s.auth.user);
  const { data: students, isLoading: studentsLoading } = useStudentList();
  const { data: streams, isLoading: streamsLoading } = useStreams();
  const { data: classLevels, isLoading: classLoading } = useClassLevels();
  const { data: staff, isLoading: staffLoading } = useStaffList();
  const { term, year } = useActiveTerm();

  const loading = studentsLoading || streamsLoading || classLoading || staffLoading;

  const quickLinks = [
    { label: 'Manage Students', path: ROUTES.STUDENTS.LIST },
    { label: 'Record Attendance', path: ROUTES.ATTENDANCE.REGISTER },
    { label: 'Assessment Records', path: ROUTES.ASSESSMENT.RECORDS },
    { label: 'View Reports', path: ROUTES.REPORTS.LIST },
    { label: 'Fee Invoices', path: ROUTES.FINANCE.INVOICES },
    { label: 'Academic Setup', path: ROUTES.ACADEMIC.YEARS },
  ];

  if (loading) {
    return (
      <div className="space-y-6">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Welcome, {user?.name || 'User'}</h1>
          <p className="text-muted mt-1">School Management System — Uganda CBC</p>
        </div>
        <LoadingSpinner />
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">
          Welcome, {user?.name || 'User'}
        </h1>
        <p className="text-muted mt-1">School Management System — Uganda CBC</p>
      </div>

      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <StatCard
          icon={Users}
          label="Total Students"
          value={students?.length ?? 0}
          badge="Enrolled"
          color="primary"
          path={ROUTES.STUDENTS.LIST}
        />
        <StatCard
          icon={Briefcase}
          label="Staff"
          value={staff?.length ?? 0}
          badge="Employees"
          color="blue"
          path={ROUTES.STAFF.LIST}
        />
        <StatCard
          icon={School}
          label="Streams"
          value={streams?.length ?? 0}
          badge="Active"
          color="amber"
          path={ROUTES.ACADEMIC.STREAMS}
        />
        <StatCard
          icon={GraduationCap}
          label="Class Levels"
          value={classLevels?.length ?? 0}
          badge="S1–S4"
          color="purple"
          path={ROUTES.ACADEMIC.CLASSES}
        />
        <StatCard
          icon={CalendarDays}
          label="Current Term"
          value={term?.term_name ?? '—'}
          badge={year?.year_name ?? ''}
          color="rose"
          path={ROUTES.ACADEMIC.TERMS}
        />
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-5 gap-6">
        <div className="lg:col-span-3 space-y-6">
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <GenderChart />
            <StatusChart />
          </div>
        </div>

        <div className="lg:col-span-2 space-y-6">
          <div className="rounded-xl bg-white border border-border p-5">
            <h3 className="text-sm font-semibold text-gray-900 mb-4">Quick Links</h3>
            <div className="space-y-2">
              {quickLinks.map((link) => (
                <Link
                  key={link.label}
                  to={link.path}
                  className="flex items-center justify-between px-4 py-3 rounded-lg border border-border hover:bg-primary-light hover:border-primary/30 transition-colors group"
                >
                  <span className="text-sm font-medium text-gray-700">{link.label}</span>
                  <ArrowRight className="w-4 h-4 text-muted group-hover:text-primary transition-colors" />
                </Link>
              ))}
            </div>
          </div>

          <div className="rounded-xl bg-gradient-to-br from-primary to-primary-dark p-6 text-white">
            <div className="flex items-center gap-2 mb-2">
              <BookOpen className="w-5 h-5 text-white/80" />
              <h3 className="text-sm font-semibold text-white/90">Current Period</h3>
            </div>
            <p className="text-lg font-bold">
              {term?.term_name ? `${term.term_name}${year ? `, ${year.year_name}` : ''}` : 'No active term'}
            </p>
            {term?.next_term_begins && (
              <p className="text-white/60 text-xs mt-1">Next term begins: {term.next_term_begins}</p>
            )}
            <p className="text-white/50 text-xs mt-2">Uganda Lower Secondary Curriculum (Competency-Based)</p>
          </div>
        </div>
      </div>
    </div>
  );
}