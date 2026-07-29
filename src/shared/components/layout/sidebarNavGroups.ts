import type { LucideIcon } from 'lucide-react';
import {
  LayoutDashboard, School, GraduationCap, Layers, GitBranch,
  UserCircle, Users, Upload, BookMarked, ClipboardCheck,
  BarChart3, CalendarCheck, Timer, Wallet, Scale,
  Library, Megaphone, Settings, User, Shield,
} from 'lucide-react';
import { ROUTES } from '../../../app/routes/constants';

interface SubItem {
  to: string;
  label: string;
  icon: LucideIcon;
}

export interface NavGroup {
  icon: LucideIcon;
  label: string;
  subItems: SubItem[];
}

export const baseNavGroups: NavGroup[] = [
  {
    icon: LayoutDashboard,
    label: 'Dashboard',
    subItems: [{ to: ROUTES.DASHBOARD, label: 'Overview', icon: LayoutDashboard }],
  },
  {
    icon: School,
    label: 'Academic',
    subItems: [
      { to: ROUTES.ACADEMIC.YEARS, label: 'Academic Years', icon: School },
      { to: ROUTES.ACADEMIC.TERMS, label: 'Terms', icon: GraduationCap },
      { to: ROUTES.ACADEMIC.CLASSES, label: 'Class Levels', icon: Layers },
      { to: ROUTES.ACADEMIC.STREAMS, label: 'Streams', icon: GitBranch },
    ],
  },
  {
    icon: UserCircle,
    label: 'Staff',
    subItems: [{ to: ROUTES.STAFF.LIST, label: 'All Staff', icon: UserCircle }],
  },
  {
    icon: Users,
    label: 'Students',
    subItems: [
      { to: ROUTES.STUDENTS.LIST, label: 'All Students', icon: Users },
      { to: ROUTES.STUDENTS.IMPORT, label: 'Import', icon: Upload },
    ],
  },
  {
    icon: BookMarked,
    label: 'Curriculum',
    subItems: [{ to: ROUTES.CURRICULUM.SUBJECTS, label: 'Subjects', icon: BookMarked }],
  },
  {
    icon: ClipboardCheck,
    label: 'Assessment',
    subItems: [{ to: ROUTES.ASSESSMENT.RECORDS, label: 'Records', icon: ClipboardCheck }],
  },
  {
    icon: BarChart3,
    label: 'Reports',
    subItems: [{ to: ROUTES.REPORTS.LIST, label: 'Report Cards', icon: BarChart3 }],
  },
  {
    icon: CalendarCheck,
    label: 'Attendance',
    subItems: [{ to: ROUTES.ATTENDANCE.REGISTER, label: 'Register', icon: CalendarCheck }],
  },
  {
    icon: Timer,
    label: 'Timetable',
    subItems: [{ to: ROUTES.TIMETABLE.VIEW, label: 'Schedule', icon: Timer }],
  },
  {
    icon: Wallet,
    label: 'Finance',
    subItems: [{ to: ROUTES.FINANCE.INVOICES, label: 'Invoices', icon: Wallet }],
  },
  {
    icon: Scale,
    label: 'Discipline',
    subItems: [{ to: ROUTES.DISCIPLINE.LIST, label: 'Records', icon: Scale }],
  },
  {
    icon: Library,
    label: 'Library',
    subItems: [{ to: ROUTES.LIBRARY.BOOKS, label: 'Books', icon: Library }],
  },
  {
    icon: Megaphone,
    label: 'Announcements',
    subItems: [{ to: ROUTES.ANNOUNCEMENTS.LIST, label: 'Notices', icon: Megaphone }],
  },
  {
    icon: Settings,
    label: 'Account',
    subItems: [
      { to: ROUTES.ACCOUNT.PROFILE, label: 'Profile', icon: User },
      { to: ROUTES.ACCOUNT.SECURITY, label: 'Security', icon: Shield },
    ],
  },
  {
    icon: Shield,
    label: 'Administration',
    subItems: [{ to: ROUTES.ROLES.LIST, label: 'Roles', icon: Shield }],
  },
];
