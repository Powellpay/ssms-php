import { cn } from '../utils/cn';

const SIZE_MAP = {
  sm: 'h-8 w-auto',
  md: 'h-10 w-auto',
  lg: 'h-18 w-auto',
} as const;

interface LogoImageProps {
  className?: string;
  size?: 'sm' | 'md' | 'lg';
}

export default function LogoImage({ className, size = 'md' }: LogoImageProps) {
  return (
    <img
      src="/logo.png"
      alt="SSMS"
      className={cn('rounded-lg', SIZE_MAP[size], className)}
    />
  );
}
