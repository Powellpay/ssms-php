import { useAppSelector } from '../../../app/store/hooks';
import { ROUTES } from '../../../app/routes/constants';
import { Link } from 'react-router-dom';

const modules = [
  { name: 'Dashboard', path: ROUTES.DASHBOARD, icon: '📊' },
  { name: 'Academic', path: ROUTES.ACADEMIC.YEARS, icon: '📚' },
  { name: 'Staff', path: ROUTES.STAFF.LIST, icon: '👥' },
  { name: 'Students', path: ROUTES.STUDENTS.LIST, icon: '👨‍🎓' },
  { name: 'Curriculum', path: ROUTES.CURRICULUM.SUBJECTS, icon: '📖' },
  { name: 'Assessment', path: ROUTES.ASSESSMENT.RECORDS, icon: '📝' },
  { name: 'Reports', path: ROUTES.REPORTS.LIST, icon: '📄' },
  { name: 'Attendance', path: ROUTES.ATTENDANCE.REGISTER, icon: '✅' },
  { name: 'Timetable', path: ROUTES.TIMETABLE.VIEW, icon: '📅' },
  { name: 'Finance', path: ROUTES.FINANCE.INVOICES, icon: '💰' },
  { name: 'Discipline', path: ROUTES.DISCIPLINE.LIST, icon: '⚖️' },
  { name: 'Library', path: ROUTES.LIBRARY.BOOKS, icon: '📖' },
  { name: 'Announcements', path: ROUTES.ANNOUNCEMENTS.LIST, icon: '📢' },
];

export default function DashboardModule() {
  const user = useAppSelector((s) => s.auth.user);

  return (
    <div className="p-8">
      <h1 className="mb-2 text-h1 text-neutral-black">
        Welcome, {user?.name || 'User'}
      </h1>
      <p className="mb-8 text-body text-neutral-gray-medium">School Management System</p>

      <div className="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        {modules.map((m) => (
          <Link
            key={m.path}
            to={m.path}
            className="flex items-center gap-3 rounded-lg border border-neutral-gray-light p-4 shadow-card transition-shadow hover:shadow-card-hover"
          >
            <span className="text-2xl">{m.icon}</span>
            <span className="text-body font-medium text-neutral-black">{m.name}</span>
          </Link>
        ))}
      </div>
    </div>
  );
}
