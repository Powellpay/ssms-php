import { Routes, Route, Navigate } from 'react-router-dom';
import DashboardModule from './modules/dashboard/ui/DashboardModule';
import AuthModule from './modules/auth/ui/AuthModule';
import { ROUTES } from './app/routes/constants';

function App() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Routes>
        <Route path={ROUTES.AUTH.LOGIN} element={<AuthModule />} />
        <Route path={ROUTES.DASHBOARD} element={<DashboardModule />} />
        <Route path="*" element={<Navigate to={ROUTES.DASHBOARD} replace />} />
      </Routes>
    </div>
  );
}

export default App;
