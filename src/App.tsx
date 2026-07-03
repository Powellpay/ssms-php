import { Routes, Route, Navigate } from 'react-router-dom';
import { ROUTES } from './app/routes/constants';
import LandingLayout from './modules/landing/LandingLayout';
import LandingPage from './modules/landing/LandingPage';
import LoginPage from './modules/auth/LoginPage';
import RegisterPage from './modules/auth/RegisterPage';
import ForgotPasswordPage from './modules/auth/ForgotPasswordPage';
import AppLayout from './shared/components/layout/AppLayout';
import DashboardPage from './modules/dashboard/DashboardPage';

function App() {
  return (
    <Routes>
      {/* Public landing pages */}
      <Route element={<LandingLayout />}>
        <Route path={ROUTES.HOME} element={<LandingPage />} />
      </Route>

      {/* Auth pages */}
      <Route path={ROUTES.AUTH.LOGIN} element={<LoginPage />} />
      <Route path={ROUTES.AUTH.REGISTER} element={<RegisterPage />} />
      <Route path={ROUTES.AUTH.FORGOT_PASSWORD} element={<ForgotPasswordPage />} />

      {/* Authenticated pages with sidebar layout */}
      <Route element={<AppLayout />}>
        <Route path={ROUTES.DASHBOARD} element={<DashboardPage />} />
        {/* Future: add more module routes here */}
      </Route>

      {/* Catch-all */}
      <Route path="*" element={<Navigate to={ROUTES.HOME} replace />} />
    </Routes>
  );
}

export default App;
