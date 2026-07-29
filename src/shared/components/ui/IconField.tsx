import type { LucideIcon } from 'lucide-react';

interface IconFieldProps {
  label: string;
  icon: LucideIcon;
  required?: boolean;
  hint?: string;
  children: React.ReactNode;
}

export const inputClass = 'w-full rounded-lg border border-border bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
export const selectClass = 'w-full appearance-none rounded-lg border border-border bg-white py-2.5 pl-10 pr-8 text-sm text-gray-900 shadow-sm transition-colors focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
export const labelClass = 'block text-sm font-medium text-gray-700 mb-1.5';
export const textareaClass = 'min-h-[80px] w-full rounded-lg border border-border bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm transition-colors placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';

export default function IconField({ label, icon: Icon, required, hint, children }: IconFieldProps) {
  return (
    <div>
      <label className={labelClass}>
        {label}
        {required && <span className="ml-0.5 text-red-500">*</span>}
      </label>
      <div className="relative">
        <Icon className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
        {children}
      </div>
      {hint && <p className="mt-1 text-xs text-muted">{hint}</p>}
    </div>
  );
}
