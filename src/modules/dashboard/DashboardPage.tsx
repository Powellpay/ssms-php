import { useAppSelector } from '../../app/store/hooks';
import { useClassLevels, useStreams } from '../../shared/api/academic/academicQueries';
import { useStudents } from '../../shared/api/students/studentQueries';
import { useNavigate } from 'react-router-dom';
import { ROUTES } from '../../app/routes/constants';
import { BookOpen, Users, School, GraduationCap, ArrowRight } from 'lucide-react';

export default function DashboardPage() {
  const user = useAppSelector((s) => s.auth.user);
  const navigate = useNavigate();
  const { data: students } = useStudents();
  const { data: streams } = useStreams();
  const { data: classLevels } = useClassLevels();

  const stats = [
    { icon: Users, label: 'Students', value: students?.length ?? 0, color: 'from-blue-500 to-blue-600', path: ROUTES.STUDENTS.LIST },
    { icon: School, label: 'Streams', value: streams?.length ?? 0, color: 'from-amber-500 to-amber-600', path: ROUTES.ACADEMIC.STREAMS },
    { icon: GraduationCap, label: 'Classes', value: classLevels?.length ?? 0, color: 'from-purple-500 to-purple-600', path: ROUTES.ACADEMIC.CLASSES },
    { icon: BookOpen, label: 'Active Term', value: 1, color: 'from-green-500 to-green-600', path: ROUTES.ACADEMIC.TERMS },
  ];

  const quickLinks = [
    { label: 'Manage Students', path: ROUTES.STUDENTS.LIST },
    { label: 'View Reports', path: ROUTES.REPORTS.LIST },
    { label: 'Record Attendance', path: ROUTES.ATTENDANCE.REGISTER },
    { label: 'Academic Setup', path: ROUTES.ACADEMIC.YEARS },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">
          Welcome, {user?.name || 'User'}
        </h1>
        <p className="text-muted mt-1">School Management System &mdash; Uganda CBC</p>
      </div>

      {/* Stats cards */}
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map((stat) => (
          <button
            key={stat.label}
            onClick={() => navigate(stat.path)}
            className="relative overflow-hidden p-5 rounded-xl bg-white border border-border hover:shadow-md transition-all text-left cursor-pointer group"
          >
            <div className={`inline-flex p-3 rounded-lg bg-gradient-to-br ${stat.color} shadow-sm mb-3`}>
              <stat.icon className="w-5 h-5 text-white" />
            </div>
            <div className="text-2xl font-bold text-gray-900">{stat.value}</div>
            <div className="text-sm text-muted">{stat.label}</div>
          </button>
        ))}
      </div>

      {/* Quick links */}
      <div className="rounded-xl bg-white border border-border p-5">
        <h2 className="text-lg font-bold text-gray-900 mb-4">Quick Links</h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          {quickLinks.map((link) => (
            <button
              key={link.label}
              onClick={() => navigate(link.path)}
              className="flex items-center justify-between px-4 py-3 rounded-lg border border-border hover:bg-primary-light hover:border-primary/30 transition-colors text-left cursor-pointer group"
            >
              <span className="text-sm font-medium text-gray-700">{link.label}</span>
              <ArrowRight className="w-4 h-4 text-muted group-hover:text-primary transition-colors" />
            </button>
          ))}
        </div>
      </div>

      {/* Current period info */}
      <div className="rounded-xl bg-gradient-to-br from-primary to-primary-dark p-6 text-white">
        <h2 className="text-lg font-semibold mb-1">Current Period</h2>
        <p className="text-white/80">Term 1, 2026 &mdash; S1 to S4</p>
        <p className="text-white/60 text-sm mt-2">Uganda Lower Secondary Curriculum (Competency-Based)</p>
      </div>
    </div>
  );
}
