import { cn } from '../utils/cn';
import LogoImage from './LogoImage';

interface SSMSLoaderProps {
  message?: string;
  className?: string;
  fullPage?: boolean;
}

export default function SSMSLoader({ message, className, fullPage = false }: SSMSLoaderProps) {
  return (
    <div
      className={cn(
        'flex flex-col items-center justify-center',
        fullPage ? 'fixed inset-0 z-50 bg-white' : 'py-24',
        className,
      )}
      role="status"
      aria-label="Loading SSMS"
    >
      <div className="relative flex items-center justify-center">
        <svg
          className="h-28 w-28 sm:h-32 sm:w-32"
          viewBox="0 0 128 128"
          fill="none"
          aria-hidden
        >
          <defs>
            <linearGradient id="loader-arc-1" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stopColor="#1f6f43" />
              <stop offset="100%" stopColor="#154d2e" />
            </linearGradient>
            <linearGradient id="loader-arc-2" x1="0" y1="1" x2="1" y2="0">
              <stop offset="0%" stopColor="#2f7d32" />
              <stop offset="100%" stopColor="#1f6f43" />
            </linearGradient>
          </defs>

          <circle
            cx="64"
            cy="64"
            r="52"
            stroke="url(#loader-arc-1)"
            strokeWidth="2.5"
            strokeDasharray="6 10"
            strokeLinecap="round"
            className="origin-center animate-ssms-loader-spin"
          />

          <circle
            cx="64"
            cy="64"
            r="58"
            stroke="url(#loader-arc-2)"
            strokeWidth="2"
            strokeDasharray="4 14"
            strokeLinecap="round"
            className="origin-center animate-ssms-loader-spin-reverse"
          />

          <circle
            cx="64"
            cy="64"
            r="46"
            stroke="url(#loader-arc-1)"
            strokeWidth="1.5"
            strokeDasharray="2 8"
            strokeLinecap="round"
            className="origin-center animate-ssms-loader-spin animate-ssms-loader-fade"
          />
        </svg>

        <div className="absolute inset-0 flex items-center justify-center">
          <LogoImage size="md" />
        </div>
      </div>

      {message && (
        <p className="mt-6 text-sm text-muted animate-pulse">{message}</p>
      )}
    </div>
  );
}
