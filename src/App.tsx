import { Routes, Route, Navigate } from 'react-router-dom';
import { ROUTES } from './app/routes/constants';
import LandingLayout from './modules/landing/LandingLayout';
import LandingPage from './modules/landing/LandingPage';
import LoginPage from './modules/auth/LoginPage';
import RegisterPage from './modules/auth/RegisterPage';
import ForgotPasswordPage from './modules/auth/ForgotPasswordPage';
import DashboardModule from './modules/dashboard/ui/DashboardModule';

function App() {
  return (
    <Routes>
      {/* Public landing pages */}
      <Route element={<LandingLayout />}>
        <Route path={ROUTES.HOME} element={<LandingPage />} />
      </Route>

      {/* Auth pages (standalone) */}
      <Route path={ROUTES.AUTH.LOGIN} element={<LoginPage />} />
      <Route path={ROUTES.AUTH.REGISTER} element={<RegisterPage />} />
      <Route path={ROUTES.AUTH.FORGOT_PASSWORD} element={<ForgotPasswordPage />} />

      {/* Authenticated pages */}
      <Route path={ROUTES.DASHBOARD} element={<DashboardModule />} />

      {/* Catch-all */}
      <Route path="*" element={<Navigate to={ROUTES.HOME} replace />} />
    </Routes>
  );
}

export default App;
