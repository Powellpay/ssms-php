import { Routes, Route, Navigate } from 'react-router-dom';
import { ROUTES } from './app/routes/constants';
import LandingLayout from './modules/landing/LandingLayout';
import LandingPage from './modules/landing/LandingPage';
import LoginPage from './modules/auth/LoginPage';
import RegisterPage from './modules/auth/RegisterPage';
import ForgotPasswordPage from './modules/auth/ForgotPasswordPage';
import VerifyEmailPage from './modules/auth/VerifyEmailPage';
import ResetPasswordPage from './modules/auth/ResetPasswordPage';
import AppLayout from './shared/components/layout/AppLayout';
import DashboardPage from './modules/dashboard/DashboardPage';
import AcademicLayout from './modules/academic/AcademicLayout';
import AcademicYearsPage from './modules/academic/ui/AcademicYearsPage';
import TermsPage from './modules/academic/ui/TermsPage';
import ClassLevelsPage from './modules/academic/ui/ClassLevelsPage';
import StreamsPage from './modules/academic/ui/StreamsPage';
import StaffListPage from './modules/staff/ui/StaffListPage';
import StudentsListPage from './modules/students/ui/StudentsListPage';
import StudentImportPage from './modules/students/ui/StudentImportPage';
import SubjectsPage from './modules/curriculum/ui/SubjectsPage';
import AssessmentRecordsPage from './modules/assessment/ui/AssessmentRecordsPage';
import ReportsListPage from './modules/reports/ui/ReportsListPage';
import AttendancePage from './modules/attendance/ui/AttendancePage';
import TimetablePage from './modules/timetable/ui/TimetablePage';
import InvoicesPage from './modules/finance/ui/InvoicesPage';
import DisciplineListPage from './modules/discipline/ui/DisciplineListPage';
import BooksPage from './modules/library/ui/BooksPage';
import AnnouncementsListPage from './modules/announcements/ui/AnnouncementsListPage';
import AccountLayout from './modules/account/AccountLayout';
import ProfileSettingsPage from './modules/account/ui/ProfileSettingsPage';
import SecurityPage from './modules/account/ui/SecurityPage';
import RolesListPage from './modules/roles/ui/RolesListPage';
import ModuleGuard from './shared/components/guard/ModuleGuard';

function App() {
  return (
    <Routes>
      <Route element={<LandingLayout />}>
        <Route path={ROUTES.HOME} element={<LandingPage />} />
      </Route>

      <Route path={ROUTES.AUTH.LOGIN} element={<LoginPage />} />
      <Route path={ROUTES.AUTH.REGISTER} element={<RegisterPage />} />
      <Route path={ROUTES.AUTH.FORGOT_PASSWORD} element={<ForgotPasswordPage />} />
      <Route path={ROUTES.AUTH.VERIFY_EMAIL} element={<VerifyEmailPage />} />
      <Route path={ROUTES.AUTH.RESET_PASSWORD} element={<ResetPasswordPage />} />

      <Route element={<AppLayout />}>
        <Route path={ROUTES.DASHBOARD} element={<ModuleGuard module="dashboard"><DashboardPage /></ModuleGuard>} />
        <Route path="/academic" element={<ModuleGuard module="academic"><AcademicLayout /></ModuleGuard>}>
          <Route index element={<AcademicYearsPage />} />
          <Route path={ROUTES.ACADEMIC.YEARS} element={<AcademicYearsPage />} />
          <Route path={ROUTES.ACADEMIC.TERMS} element={<TermsPage />} />
          <Route path={ROUTES.ACADEMIC.CLASSES} element={<ClassLevelsPage />} />
          <Route path={ROUTES.ACADEMIC.STREAMS} element={<StreamsPage />} />
        </Route>
        <Route path={ROUTES.STAFF.LIST} element={<ModuleGuard module="staff"><StaffListPage /></ModuleGuard>} />
        <Route path={ROUTES.STUDENTS.LIST} element={<ModuleGuard module="students"><StudentsListPage /></ModuleGuard>} />
        <Route path={ROUTES.STUDENTS.IMPORT} element={<ModuleGuard module="students"><StudentImportPage /></ModuleGuard>} />
        <Route path={ROUTES.CURRICULUM.SUBJECTS} element={<ModuleGuard module="curriculum"><SubjectsPage /></ModuleGuard>} />
        <Route path={ROUTES.ASSESSMENT.RECORDS} element={<ModuleGuard module="assessment"><AssessmentRecordsPage /></ModuleGuard>} />
        <Route path={ROUTES.REPORTS.LIST} element={<ModuleGuard module="reports"><ReportsListPage /></ModuleGuard>} />
        <Route path={ROUTES.ATTENDANCE.REGISTER} element={<ModuleGuard module="attendance"><AttendancePage /></ModuleGuard>} />
        <Route path={ROUTES.TIMETABLE.VIEW} element={<ModuleGuard module="timetable"><TimetablePage /></ModuleGuard>} />
        <Route path={ROUTES.FINANCE.INVOICES} element={<ModuleGuard module="finance"><InvoicesPage /></ModuleGuard>} />
        <Route path={ROUTES.DISCIPLINE.LIST} element={<ModuleGuard module="discipline"><DisciplineListPage /></ModuleGuard>} />
        <Route path={ROUTES.LIBRARY.BOOKS} element={<ModuleGuard module="library"><BooksPage /></ModuleGuard>} />
        <Route path={ROUTES.ANNOUNCEMENTS.LIST} element={<ModuleGuard module="announcements"><AnnouncementsListPage /></ModuleGuard>} />
        <Route path="/account" element={<AccountLayout />}>
          <Route index element={<Navigate to={ROUTES.ACCOUNT.PROFILE} replace />} />
          <Route path={ROUTES.ACCOUNT.PROFILE} element={<ProfileSettingsPage />} />
          <Route path={ROUTES.ACCOUNT.SECURITY} element={<SecurityPage />} />
        </Route>
        <Route path={ROUTES.ROLES.LIST} element={<RolesListPage />} />
      </Route>

      <Route path="*" element={<Navigate to={ROUTES.HOME} replace />} />
    </Routes>
  );
}

export default App;
