import { useState } from 'react';
import { Outlet, Link, useNavigate, useLocation } from 'react-router-dom';
import { useAppSelector, useAppDispatch } from '../../../app/store/hooks';
import { logout } from '../../../app/store/slices/authSlice';
import { ROUTES } from '../../../app/routes/constants';
import { useLogout } from '../../api/auth/authQueries';
import type { ModuleSlug } from '../../types';
import {
  LayoutDashboard, Users, ClipboardCheck,
  BarChart3, CalendarCheck, Timer, Wallet, Library,
  Megaphone, Menu, X, ChevronDown, LogOut, School,
  UserCircle, BookMarked, Scale,
} from 'lucide-react';
import { cn } from '../../utils/cn';
import { APP_VERSION } from '../../config/version';
import LogoImage from '../LogoImage';

interface NavItem {
  icon: React.ComponentType<{ className?: string }>;
  label: string;
  path: string;
  module: ModuleSlug;
}

const ALL_NAV_ITEMS: NavItem[] = [
  { icon: LayoutDashboard, label: 'Dashboard', path: ROUTES.DASHBOARD, module: 'dashboard' },
  { icon: School, label: 'Academic', path: '/academic', module: 'academic' },
  { icon: UserCircle, label: 'Staff', path: ROUTES.STAFF.LIST, module: 'staff' },
  { icon: Users, label: 'Students', path: ROUTES.STUDENTS.LIST, module: 'students' },
  { icon: BookMarked, label: 'Curriculum', path: ROUTES.CURRICULUM.SUBJECTS, module: 'curriculum' },
  { icon: ClipboardCheck, label: 'Assessment', path: ROUTES.ASSESSMENT.RECORDS, module: 'assessment' },
  { icon: BarChart3, label: 'Reports', path: ROUTES.REPORTS.LIST, module: 'reports' },
  { icon: CalendarCheck, label: 'Attendance', path: ROUTES.ATTENDANCE.REGISTER, module: 'attendance' },
  { icon: Timer, label: 'Timetable', path: ROUTES.TIMETABLE.VIEW, module: 'timetable' },
  { icon: Wallet, label: 'Finance', path: ROUTES.FINANCE.INVOICES, module: 'finance' },
  { icon: Scale, label: 'Discipline', path: ROUTES.DISCIPLINE.LIST, module: 'discipline' },
  { icon: Library, label: 'Library', path: ROUTES.LIBRARY.BOOKS, module: 'library' },
  { icon: Megaphone, label: 'Announcements', path: ROUTES.ANNOUNCEMENTS.LIST, module: 'announcements' },
];

export default function AppLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(true);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const location = useLocation();
  const user = useAppSelector((s) => s.auth.user);
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const logoutMutation = useLogout();

  const allowedModules = user?.is_school_admin || !user?.modules
    ? ALL_NAV_ITEMS.map(i => i.module)
    : user.modules;

  const moduleSet = new Set(allowedModules);
  const navItems = ALL_NAV_ITEMS.filter(i => moduleSet.has(i.module));

  const isActive = (path: string) => {
    if (path === ROUTES.DASHBOARD) return location.pathname === path;
    return location.pathname.startsWith(path);
  };

  const handleLogout = () => {
    dispatch(logout());
    logoutMutation.mutate(undefined, {
      onSettled: () => {
        navigate(ROUTES.AUTH.LOGIN, { replace: true });
      },
    });
  };

  return (
    <div className="flex h-screen overflow-hidden bg-bg">
      {/* Mobile overlay */}
      {sidebarOpen && (
        <div
          className="fixed inset-0 bg-black/50 z-20 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      {/* Sidebar */}
      <aside
        className={cn(
          'fixed lg:static inset-y-0 left-0 z-30 w-64 bg-white border-r border-border flex flex-col transition-transform duration-200',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-16',
        )}
      >
        {/* Logo */}
        <div className={cn('flex items-center h-16 px-4 border-b border-border', !sidebarOpen && 'lg:justify-center')}>
          <LogoImage size="sm" />
          {sidebarOpen && (
            <div className="ml-2.5 flex items-baseline gap-1.5">
              <span className="text-lg font-bold text-primary">SSMS</span>
              <span className="text-[10px] text-muted/50 font-mono">v{APP_VERSION}</span>
            </div>
          )}
        </div>

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto py-2 px-2 space-y-0.5">
          {navItems.map((item) => (
            <Link
              key={item.path}
              to={item.path}
              className={cn(
                'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors',
                isActive(item.path)
                  ? 'bg-primary-light text-primary font-semibold'
                  : 'text-gray-600 hover:bg-primary-light hover:text-primary',
                !sidebarOpen && 'lg:justify-center lg:px-2',
              )}
              title={item.label}
            >
              <item.icon className="w-5 h-5 shrink-0" />
              {sidebarOpen && <span>{item.label}</span>}
            </Link>
          ))}
        </nav>

        {/* User section */}
        <div className="border-t border-border p-3">
          <div className="flex items-center gap-3">
            <div className="w-9 h-9 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold shrink-0">
              {user?.name?.charAt(0) || 'U'}
            </div>
            {sidebarOpen && (
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-gray-900 truncate">{user?.name || 'User'}</p>
                <p className="text-xs text-primary font-medium truncate">{user?.role?.role_name || ''}</p>
                <p className="text-xs text-muted truncate">{user?.email || ''}</p>
              </div>
            )}
          </div>
        </div>
      </aside>

      {/* Main area */}
      <div className="flex-1 flex flex-col min-w-0">
        {/* Top bar */}
        <header className="sticky top-0 z-10 h-16 bg-white border-b border-border flex items-center justify-between px-4 lg:px-6">
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="p-2 rounded-lg text-gray-500 hover:bg-gray-100 lg:hidden cursor-pointer"
          >
            {sidebarOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
          </button>
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="hidden lg:flex p-2 rounded-lg text-gray-500 hover:bg-gray-100 cursor-pointer"
          >
            <Menu className="w-5 h-5" />
          </button>

          <div className="flex items-center gap-4 ml-auto">
            <div className="relative">
              <button
                onClick={() => setUserMenuOpen(!userMenuOpen)}
                className="flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-gray-100 cursor-pointer"
              >
                <div className="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold">
                  {user?.name?.charAt(0) || 'U'}
                </div>
                <span className="text-sm font-medium text-gray-700 hidden sm:block">{user?.name || 'User'}</span>
                <ChevronDown className="w-4 h-4 text-gray-400" />
              </button>

              {userMenuOpen && (
                <>
                  <div className="fixed inset-0 z-10" onClick={() => setUserMenuOpen(false)} />
                  <div className="absolute right-0 top-full mt-1 w-48 bg-white border border-border rounded-lg shadow-lg z-20 py-1">
                    <div className="px-4 py-2 border-b border-border">
                      <p className="text-sm font-medium text-gray-900">{user?.name}</p>
                      <p className="text-xs text-muted">{user?.email}</p>
                    </div>
                    <button
                      onClick={handleLogout}
                      className="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 cursor-pointer"
                    >
                      <LogOut className="w-4 h-4" /> Sign Out
                    </button>
                  </div>
                </>
              )}
            </div>
          </div>
        </header>

        {/* Main content */}
        <main className="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
