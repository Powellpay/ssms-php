import type { LucideIcon } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

interface StatCardProps {
  icon: LucideIcon;
  label: string;
  value: number | string;
  badge?: string;
  color: 'primary' | 'blue' | 'amber' | 'purple' | 'rose';
  path?: string;
}

const palettes = {
  primary: { border: 'border-primary', shadow: 'hover:shadow-primary/20', iconBg: 'bg-primary-light', iconColor: 'text-primary', badge: 'bg-primary-light text-primary', glow: 'bg-primary/10' },
  blue: { border: 'border-blue-500', shadow: 'hover:shadow-blue-500/20', iconBg: 'bg-blue-100', iconColor: 'text-blue-600', badge: 'bg-blue-100 text-blue-700', glow: 'bg-blue-500/10' },
  amber: { border: 'border-amber-500', shadow: 'hover:shadow-amber-500/20', iconBg: 'bg-amber-100', iconColor: 'text-amber-600', badge: 'bg-amber-100 text-amber-700', glow: 'bg-amber-500/10' },
  purple: { border: 'border-purple-500', shadow: 'hover:shadow-purple-500/20', iconBg: 'bg-purple-100', iconColor: 'text-purple-600', badge: 'bg-purple-100 text-purple-700', glow: 'bg-purple-500/10' },
  rose: { border: 'border-rose-500', shadow: 'hover:shadow-rose-500/20', iconBg: 'bg-rose-100', iconColor: 'text-rose-600', badge: 'bg-rose-100 text-rose-700', glow: 'bg-rose-500/10' },
};

export default function StatCard({ icon: Icon, label, value, badge, color, path }: StatCardProps) {
  const navigate = useNavigate();
  const s = palettes[color];

  return (
    <div
      onClick={() => path && navigate(path)}
      className={`relative overflow-hidden rounded-xl p-5 transition-all duration-300 border-2 bg-white ${s.border} ${s.shadow} hover:-translate-y-0.5 group cursor-pointer min-h-[120px] flex flex-col justify-center`}
    >
      <div className={`absolute -top-8 -right-8 w-24 h-24 rounded-full blur-2xl ${s.glow}`} />
      <div className="flex items-center justify-between mb-3 relative">
        <div className={`p-3 rounded-xl transition-all duration-300 ${s.iconBg} group-hover:scale-110`}>
          <Icon className={`w-5 h-5 ${s.iconColor}`} />
        </div>
        {badge && (
          <span className={`text-xs font-medium px-2.5 py-1 rounded-full ${s.badge}`}>{badge}</span>
        )}
      </div>
      <p className="text-2xl font-bold text-gray-900 relative">{value}</p>
      <p className="text-sm font-medium text-gray-500 relative">{label}</p>
    </div>
  );
}