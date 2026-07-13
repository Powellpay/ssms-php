import { Loader2 } from 'lucide-react';

interface LoadingSpinnerProps {
  message?: string;
}

export default function LoadingSpinner({ message = 'Loading...' }: LoadingSpinnerProps) {
  return (
    <div className="flex flex-col items-center justify-center py-8 text-muted gap-2">
      <Loader2 className="w-6 h-6 animate-spin" />
      <span className="text-sm">{message}</span>
    </div>
  );
}
