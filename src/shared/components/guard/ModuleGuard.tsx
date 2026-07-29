import { type ReactNode } from 'react';
import { Navigate } from 'react-router-dom';
import { useAppSelector } from '../../../app/store/hooks';
import type { ModuleSlug } from '../../types';

interface Props {
  module: ModuleSlug;
  children: ReactNode;
  fallback?: ReactNode;
}

export default function ModuleGuard({ module, children, fallback }: Props) {
  const user = useAppSelector((s) => s.auth.user);

  if (!user) return <Navigate to="/login" replace />;

  const hasAccess = user.is_school_admin || !user.modules || user.modules.includes(module);

  if (!hasAccess) {
    return fallback ? <>{fallback}</> : <Navigate to="/dashboard" replace />;
  }

  return <>{children}</>;
}
