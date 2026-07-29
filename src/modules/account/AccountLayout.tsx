import { Outlet, NavLink } from 'react-router-dom';
import { ROUTES } from '../../app/routes/constants';
import { User, Shield } from 'lucide-react';

const tabs = [
  { to: ROUTES.ACCOUNT.PROFILE, label: 'Profile', icon: User },
  { to: ROUTES.ACCOUNT.SECURITY, label: 'Security', icon: Shield },
];

export default function AccountLayout() {
  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold text-gray-900">Account Settings</h1>
        <p className="text-muted text-sm mt-1">Manage your profile, password, and security</p>
      </div>
      <div className="flex gap-1 border-b border-border">
        {tabs.map(tab => (
          <NavLink
            key={tab.to}
            to={tab.to}
            end
            className={({ isActive }) =>
              `inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors ${
                isActive ? 'border-primary text-primary' : 'border-transparent text-muted hover:text-gray-900'
              }`
            }
          >
            <tab.icon className="w-4 h-4" />
            {tab.label}
          </NavLink>
        ))}
      </div>
      <Outlet />
    </div>
  );
}
