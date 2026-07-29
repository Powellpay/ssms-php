import SSMSLoader from '../SSMSLoader';

interface LoadingSpinnerProps {
  message?: string;
  fullPage?: boolean;
}

export default function LoadingSpinner({ message, fullPage }: LoadingSpinnerProps) {
  return <SSMSLoader message={message} fullPage={fullPage} />;
}
