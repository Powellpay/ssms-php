import { Inbox } from 'lucide-react';
import type { ReactNode } from 'react';

interface EmptyStateProps {
  message: string;
  icon?: ReactNode;
}

export default function EmptyState({ message, icon }: EmptyStateProps) {
  return (
    <div className="flex flex-col items-center justify-center py-8 text-muted gap-2">
      <div className="text-muted/50">{icon || <Inbox className="w-10 h-10" />}</div>
      <p className="text-sm">{message}</p>
    </div>
  );
}
