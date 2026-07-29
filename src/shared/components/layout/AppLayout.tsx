import { useEffect, useMemo, useState } from 'react';
import { Outlet, Link, NavLink, useNavigate, useLocation } from 'react-router-dom';
import { useAppSelector, useAppDispatch } from '../../../app/store/hooks';
import { logout } from '../../../app/store/slices/authSlice';
import { ROUTES } from '../../../app/routes/constants';
import { useLogout } from '../../api/auth/authQueries';
import { baseNavGroups } from './sidebarNavGroups';
import type { NavGroup } from './sidebarNavGroups';
import type { ModuleSlug } from '../../types';
import { Menu, ChevronDown, ChevronRight, LogOut } from 'lucide-react';
import { cn } from '../../utils/cn';
import { APP_VERSION } from '../../config/version';
import LogoImage from '../LogoImage';

const moduleLabelMap: Record<string, ModuleSlug> = {
  Dashboard: 'dashboard',
  Academic: 'academic',
  Staff: 'staff',
  Students: 'students',
  Curriculum: 'curriculum',
  Assessment: 'assessment',
  Reports: 'reports',
  Attendance: 'attendance',
  Timetable: 'timetable',
  Finance: 'finance',
  Discipline: 'discipline',
  Library: 'library',
  Announcements: 'announcements',
};

function isSubItemActive(subTo: string, pathname: string): boolean {
  if (subTo === ROUTES.DASHBOARD) return pathname === subTo;
  return pathname.startsWith(subTo);
}

function groupHasActiveItem(group: NavGroup, pathname: string): boolean {
  return group.subItems.some((s) => isSubItemActive(s.to, pathname));
}

export default function AppLayout() {
  const [sidebarOpen, setSidebarOpen] = useState(() => {
    if (typeof window !== 'undefined') return window.innerWidth >= 1024;
    return true;
  });
  const [openGroup, setOpenGroup] = useState<number | null>(null);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const location = useLocation();
  const user = useAppSelector((s) => s.auth.user);
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const logoutMutation = useLogout();

  const groups = useMemo(() => {
    const allowedModules = user?.is_school_admin || !user?.modules
      ? Object.values(moduleLabelMap)
      : user.modules;
    const moduleSet = new Set(allowedModules);
    return baseNavGroups.filter((g) => {
      if (g.label === 'Account' || g.label === 'Administration') return true;
      const m = moduleLabelMap[g.label];
      return m ? moduleSet.has(m) : false;
    });
  }, [user]);

  useEffect(() => {
    const idx = groups.findIndex((g) => groupHasActiveItem(g, location.pathname));
    if (idx >= 0) {
      setOpenGroup(idx);
    }
  }, [location.pathname, groups]);

  const toggleGroup = (idx: number) => {
    setOpenGroup(openGroup === idx ? null : idx);
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
      {sidebarOpen && (
        <div
          className="fixed inset-0 bg-black/50 z-20 lg:hidden"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <aside
        className={cn(
          'fixed lg:static inset-y-0 left-0 z-30 bg-white border-r border-border flex flex-col transition-all duration-200',
          sidebarOpen ? 'w-64' : 'w-16 -translate-x-full lg:translate-x-0',
        )}
      >
        <div className={cn('flex items-center h-16 px-4 border-b border-border shrink-0', !sidebarOpen && 'justify-center')}>
          <div className="flex items-center gap-2.5 min-w-0">
            <LogoImage size="sm" />
            {sidebarOpen && <span className="text-lg font-bold text-primary">SSMS</span>}
          </div>
          {sidebarOpen && (
            <span className="ml-auto text-[10px] font-bold font-mono tracking-tight">V{APP_VERSION}</span>
          )}
        </div>

        <nav className="flex-1 overflow-y-auto py-2 px-2 space-y-0.5">
          {groups.map((group, idx) => {
            const isSingle = group.subItems.length === 1;
            const isOpen = openGroup === idx;
            const singleItem = group.subItems[0];

            if (isSingle && singleItem) {
              return (
                <NavLink
                  key={singleItem.to}
                  to={singleItem.to}
                  end={singleItem.to === ROUTES.DASHBOARD}
                  className={({ isActive }) =>
                    cn(
                      'flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition-colors',
                      isActive
                        ? 'bg-primary-light text-primary font-semibold'
                        : 'text-gray-600 hover:bg-primary-light hover:text-primary',
                      !sidebarOpen && 'justify-center px-2',
                    )
                  }
                  title={sidebarOpen ? undefined : group.label}
                >
                  <group.icon className="w-5 h-5 shrink-0" />
                  {sidebarOpen && <span>{group.label}</span>}
                </NavLink>
              );
            }

            return (
              <div key={group.label}>
                <button
                  onClick={() => toggleGroup(idx)}
                  className={cn(
                    'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm transition-colors text-left',
                    isOpen || groupHasActiveItem(group, location.pathname)
                      ? 'bg-primary-light text-primary font-semibold'
                      : 'text-gray-600 hover:bg-primary-light hover:text-primary',
                    !sidebarOpen && 'justify-center px-2',
                  )}
                  title={sidebarOpen ? undefined : group.label}
                >
                  <group.icon className="w-5 h-5 shrink-0" />
                  {sidebarOpen && (
                    <>
                      <span className="flex-1">{group.label}</span>
                      {isOpen ? <ChevronDown className="w-4 h-4" /> : <ChevronRight className="w-4 h-4" />}
                    </>
                  )}
                </button>

                {sidebarOpen && isOpen && (
                  <div className="ml-3 pl-3 border-l-2 border-primary/20 space-y-0.5 mt-0.5">
                    {group.subItems.map((sub) => (
                      <NavLink
                        key={sub.to}
                        to={sub.to}
                        end={sub.to === ROUTES.DASHBOARD}
                        className={({ isActive }) =>
                          cn(
                            'flex items-center gap-2 px-3 py-2 rounded-lg text-sm transition-colors',
                            isActive
                              ? 'bg-primary-light text-primary font-semibold'
                              : 'text-gray-500 hover:bg-primary-light hover:text-primary',
                          )
                        }
                      >
                        <sub.icon className="w-4 h-4 shrink-0" />
                        <span>{sub.label}</span>
                      </NavLink>
                    ))}
                  </div>
                )}
              </div>
            );
          })}
        </nav>

        <div className="border-t border-border p-3 shrink-0">
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

      <div className="flex-1 flex flex-col min-w-0">
        <header className="sticky top-0 z-10 h-16 bg-white border-b border-border flex items-center justify-between px-4 lg:px-6">
          <button
            onClick={() => setSidebarOpen(!sidebarOpen)}
            className="p-2 rounded-lg text-gray-500 hover:bg-gray-100 cursor-pointer shrink-0"
          >
            <Menu className="w-5 h-5" />
          </button>

          <span className="text-sm font-semibold text-gray-800 truncate max-w-[200px] sm:max-w-[300px] lg:max-w-[400px] mx-2">
            {user?.school_name || 'SSMS'}
          </span>

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
                    <Link
                      to={ROUTES.ACCOUNT.PROFILE}
                      onClick={() => setUserMenuOpen(false)}
                      className="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-50"
                    >
                      Account Settings
                    </Link>
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

        <main className="flex-1 overflow-auto p-4 sm:p-6 lg:p-8">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
