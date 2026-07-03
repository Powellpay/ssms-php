import { Outlet, NavLink } from 'react-router-dom';
import { ROUTES } from '../../app/routes/constants';
import { School, GraduationCap, Layers, GitBranch } from 'lucide-react';
import { cn } from '../../shared/utils/cn';

const tabs = [
  { icon: School, label: 'Academic Years', path: ROUTES.ACADEMIC.YEARS },
  { icon: GraduationCap, label: 'Terms', path: ROUTES.ACADEMIC.TERMS },
  { icon: Layers, label: 'Class Levels', path: ROUTES.ACADEMIC.CLASSES },
  { icon: GitBranch, label: 'Streams', path: ROUTES.ACADEMIC.STREAMS },
];

export default function AcademicLayout() {
  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <div className="p-3 rounded-xl bg-primary-light"><School className="w-8 h-8 text-primary" /></div>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Academic Structure</h1>
          <p className="text-muted text-sm mt-1">Manage your school's academic calendar and class setup</p>
        </div>
      </div>

      <div className="flex gap-1 rounded-xl bg-gray-100 p-1 overflow-x-auto">
        {tabs.map((tab) => (
          <NavLink
            key={tab.path}
            to={tab.path}
            className={({ isActive }) => cn(
              'flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap',
              isActive ? 'bg-white text-primary shadow-sm' : 'text-gray-600 hover:text-gray-900',
            )}
          >
            <tab.icon className="w-4 h-4" />
            {tab.label}
          </NavLink>
        ))}
      </div>

      <Outlet />
    </div>
  );
}
