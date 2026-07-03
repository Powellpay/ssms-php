import { Routes, Route, Navigate } from 'react-router-dom';
import { ROUTES } from './app/routes/constants';
import LandingLayout from './modules/landing/LandingLayout';
import LandingPage from './modules/landing/LandingPage';
import LoginPage from './modules/auth/LoginPage';
import RegisterPage from './modules/auth/RegisterPage';
import ForgotPasswordPage from './modules/auth/ForgotPasswordPage';
import AppLayout from './shared/components/layout/AppLayout';
import DashboardPage from './modules/dashboard/DashboardPage';
import AcademicLayout from './modules/academic/AcademicLayout';
import AcademicYearsPage from './modules/academic/ui/AcademicYearsPage';
import TermsPage from './modules/academic/ui/TermsPage';
import ClassLevelsPage from './modules/academic/ui/ClassLevelsPage';
import StreamsPage from './modules/academic/ui/StreamsPage';
import StaffListPage from './modules/staff/ui/StaffListPage';
import StudentsListPage from './modules/students/ui/StudentsListPage';
import SubjectsPage from './modules/curriculum/ui/SubjectsPage';
import AssessmentRecordsPage from './modules/assessment/ui/AssessmentRecordsPage';
import ReportsListPage from './modules/reports/ui/ReportsListPage';
import AttendancePage from './modules/attendance/ui/AttendancePage';
import TimetablePage from './modules/timetable/ui/TimetablePage';
import InvoicesPage from './modules/finance/ui/InvoicesPage';
import DisciplineListPage from './modules/discipline/ui/DisciplineListPage';
import BooksPage from './modules/library/ui/BooksPage';
import AnnouncementsListPage from './modules/announcements/ui/AnnouncementsListPage';

function App() {
  return (
    <Routes>
      <Route element={<LandingLayout />}>
        <Route path={ROUTES.HOME} element={<LandingPage />} />
      </Route>

      <Route path={ROUTES.AUTH.LOGIN} element={<LoginPage />} />
      <Route path={ROUTES.AUTH.REGISTER} element={<RegisterPage />} />
      <Route path={ROUTES.AUTH.FORGOT_PASSWORD} element={<ForgotPasswordPage />} />

      <Route element={<AppLayout />}>
        <Route path={ROUTES.DASHBOARD} element={<DashboardPage />} />
        <Route path="/academic" element={<AcademicLayout />}>
          <Route index element={<AcademicYearsPage />} />
          <Route path={ROUTES.ACADEMIC.YEARS} element={<AcademicYearsPage />} />
          <Route path={ROUTES.ACADEMIC.TERMS} element={<TermsPage />} />
          <Route path={ROUTES.ACADEMIC.CLASSES} element={<ClassLevelsPage />} />
          <Route path={ROUTES.ACADEMIC.STREAMS} element={<StreamsPage />} />
        </Route>
        <Route path={ROUTES.STAFF.LIST} element={<StaffListPage />} />
        <Route path={ROUTES.STUDENTS.LIST} element={<StudentsListPage />} />
        <Route path={ROUTES.CURRICULUM.SUBJECTS} element={<SubjectsPage />} />
        <Route path={ROUTES.ASSESSMENT.RECORDS} element={<AssessmentRecordsPage />} />
        <Route path={ROUTES.REPORTS.LIST} element={<ReportsListPage />} />
        <Route path={ROUTES.ATTENDANCE.REGISTER} element={<AttendancePage />} />
        <Route path={ROUTES.TIMETABLE.VIEW} element={<TimetablePage />} />
        <Route path={ROUTES.FINANCE.INVOICES} element={<InvoicesPage />} />
        <Route path={ROUTES.DISCIPLINE.LIST} element={<DisciplineListPage />} />
        <Route path={ROUTES.LIBRARY.BOOKS} element={<BooksPage />} />
        <Route path={ROUTES.ANNOUNCEMENTS.LIST} element={<AnnouncementsListPage />} />
      </Route>

      <Route path="*" element={<Navigate to={ROUTES.HOME} replace />} />
    </Routes>
  );
}

export default App;
