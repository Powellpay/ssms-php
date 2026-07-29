import type { LucideIcon } from 'lucide-react';

interface FormSectionProps {
  title: string;
  icon: LucideIcon;
  description?: string;
  children: React.ReactNode;
  className?: string;
}

export default function FormSection({ title, icon: Icon, description, children, className }: FormSectionProps) {
  return (
    <div className={`overflow-hidden rounded-xl border border-border bg-white shadow-sm ${className ?? ''}`}>
      <div className="border-b border-border bg-primary-light/40 px-4 py-3">
        <div className="flex items-center gap-2">
          <Icon className="h-4 w-4 text-primary" />
          <h3 className="text-sm font-semibold text-gray-800">{title}</h3>
        </div>
        {description && <p className="mt-1 pl-6 text-xs text-muted">{description}</p>}
      </div>
      <div className="space-y-4 p-4">{children}</div>
    </div>
  );
}
