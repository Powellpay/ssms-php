import type { LucideIcon } from 'lucide-react';

interface Props {
  title: string;
  icon: LucideIcon;
  description?: string;
  features?: string[];
}

export default function ModulePlaceholder({ title, icon: Icon, description, features }: Props) {
  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <div className="p-3 rounded-xl bg-primary-light">
          <Icon className="w-8 h-8 text-primary" />
        </div>
        <div>
          <h1 className="text-2xl font-bold text-gray-900">{title}</h1>
          {description && <p className="text-muted text-sm mt-1">{description}</p>}
        </div>
      </div>

      <div className="rounded-xl border border-border bg-white p-8 text-center">
        <Icon className="w-16 h-16 text-muted mx-auto mb-4 opacity-40" />
        <h2 className="text-lg font-semibold text-gray-700 mb-2">Coming Soon</h2>
        <p className="text-muted text-sm max-w-md mx-auto">
          {description || `The ${title} module is being built. Full CRUD functionality will be available here.`}
        </p>
        {features && features.length > 0 && (
          <div className="mt-6 max-w-sm mx-auto">
            <h3 className="text-sm font-semibold text-gray-600 mb-2">Planned Features:</h3>
            <ul className="text-sm text-muted space-y-1.5">
              {features.map((f) => (
                <li key={f} className="flex items-center gap-2">
                  <span className="w-1.5 h-1.5 rounded-full bg-primary shrink-0" />
                  {f}
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>
    </div>
  );
}
