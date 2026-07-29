import type { LucideIcon } from 'lucide-react';

interface ModalHeroProps {
  icon: LucideIcon;
  title: string;
  description: string;
}

export default function ModalHero({ icon: Icon, title, description }: ModalHeroProps) {
  return (
    <div className="flex items-start gap-3 rounded-xl border border-primary/20 bg-gradient-to-r from-primary-light to-green-50 px-4 py-3.5">
      <div className="rounded-lg bg-primary p-2.5 shadow-sm">
        <Icon className="h-5 w-5 text-white" />
      </div>
      <div className="min-w-0 pt-0.5">
        <p className="text-sm font-semibold text-primary-dark">{title}</p>
        <p className="mt-0.5 text-xs text-muted">{description}</p>
      </div>
    </div>
  );
}
