import { School, GraduationCap, Layers, GitBranch } from 'lucide-react';
import { Link } from 'react-router-dom';
import { ROUTES } from '../../app/routes/constants';

const sections = [
  { icon: School, label: 'Academic Years', path: ROUTES.ACADEMIC.YEARS, desc: 'Manage school calendars and set current year' },
  { icon: GraduationCap, label: 'Terms', path: ROUTES.ACADEMIC.TERMS, desc: 'Configure term dates within each academic year' },
  { icon: Layers, label: 'Class Levels', path: ROUTES.ACADEMIC.CLASSES, desc: 'Set up S1–S4 class levels' },
  { icon: GitBranch, label: 'Streams', path: ROUTES.ACADEMIC.STREAMS, desc: 'Create and manage class streams with teachers' },
];

export default function AcademicModule() {
  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <div className="p-3 rounded-xl bg-primary-light">
          <School className="w-8 h-8 text-primary" />
        </div>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Academic Structure</h1>
          <p className="text-muted text-sm mt-1">Manage your school's academic calendar and class setup</p>
        </div>
      </div>
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
        {sections.map((s) => (
          <Link
            key={s.path}
            to={s.path}
            className="flex items-start gap-4 p-5 rounded-xl border border-border bg-white hover:shadow-md hover:border-primary/30 transition-all group"
          >
            <div className="p-2.5 rounded-lg bg-primary-light shrink-0">
              <s.icon className="w-5 h-5 text-primary" />
            </div>
            <div>
              <h3 className="font-semibold text-gray-900 group-hover:text-primary transition-colors">{s.label}</h3>
              <p className="text-sm text-muted mt-0.5">{s.desc}</p>
            </div>
          </Link>
        ))}
      </div>
    </div>
  );
}
