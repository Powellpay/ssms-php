import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import type { AxiosError } from 'axios';
import type { ApiError } from '../../shared/api/auth/authTypes';
import { useForgotPassword } from '../../shared/api/auth/authQueries';
import { ROUTES } from '../../app/routes/constants';
import { Mail, ArrowLeft, Send, RefreshCw } from 'lucide-react';
import AuthLayout from './AuthLayout';

const RESEND_COOLDOWN = 60;

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState('');
  const [sent, setSent] = useState(false);
  const [cooldown, setCooldown] = useState(0);
  const forgotMutation = useForgotPassword();

  useEffect(() => {
    if (cooldown > 0) {
      const t = setTimeout(() => setCooldown((c) => c - 1), 1000);
      return () => clearTimeout(t);
    }
  }, [cooldown]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    forgotMutation.mutate({ email }, {
      onSuccess: () => setSent(true),
    });
  };

  const handleResend = () => {
    if (cooldown > 0 || forgotMutation.isPending) return;
    forgotMutation.mutate({ email }, {
      onSuccess: () => {
        setCooldown(RESEND_COOLDOWN);
      },
    });
  };

  const axiosError = forgotMutation.error as AxiosError<ApiError> | undefined;
  const error = axiosError?.response?.data?.message || axiosError?.message;

  const inputCls = "w-full pl-11 pr-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors text-sm";

  return (
    <AuthLayout title="Reset Password" subtitle="We'll send you a reset link">
      {sent ? (
        <div className="text-center space-y-4">
          <div className="w-16 h-16 rounded-full bg-success-light flex items-center justify-center mx-auto">
            <Send className="w-8 h-8 text-success" />
          </div>
          <p className="text-sm text-gray-600">
            If an account exists for <strong>{email}</strong>, you'll receive a password reset link shortly.
          </p>

          {forgotMutation.error && (
            <p className="text-sm text-alert-error-text bg-alert-error-bg border border-alert-error-border rounded-lg px-4 py-3">
              {(forgotMutation.error as AxiosError<ApiError>)?.response?.data?.message || (forgotMutation.error as AxiosError<ApiError>)?.message}
            </p>
          )}

          <button
            type="button"
            onClick={handleResend}
            disabled={cooldown > 0 || forgotMutation.isPending}
            className="group inline-flex items-center gap-1.5 text-sm text-primary hover:underline font-medium disabled:text-gray-400 disabled:no-underline cursor-pointer"
          >
            <RefreshCw className={`w-3.5 h-3.5 transition-transform duration-500 ${forgotMutation.isPending ? 'animate-spin' : ''} ${cooldown === 0 && !forgotMutation.isPending ? 'group-hover:rotate-180' : ''}`} />
            {cooldown > 0
              ? `Resend link in ${cooldown}s`
              : 'Resend link'}
          </button>

          <Link to={ROUTES.AUTH.LOGIN} className="inline-flex items-center gap-2 text-sm text-primary hover:underline font-medium">
            <ArrowLeft className="w-4 h-4" /> Back to sign in
          </Link>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="relative">
            <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              placeholder="Email address"
              className={inputCls}
            />
          </div>

          {error && (
            <p className="text-sm text-alert-error-text bg-alert-error-bg border border-alert-error-border rounded-lg px-4 py-3">
              {error}
            </p>
          )}

          <button
            type="submit"
            disabled={forgotMutation.isPending}
            className="w-full inline-flex items-center justify-center px-8 py-3.5 rounded-lg bg-primary text-white hover:bg-primary-dark transition-colors font-medium text-sm disabled:opacity-50 cursor-pointer"
          >
            {forgotMutation.isPending ? (
              <span className="flex items-center gap-2">
                <span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                Sending...
              </span>
            ) : (
              <span className="flex items-center gap-2"><Send className="w-4 h-4" /> Send Reset Link</span>
            )}
          </button>

          <p className="text-center text-sm text-muted">
            <Link to={ROUTES.AUTH.LOGIN} className="inline-flex items-center gap-1 text-primary hover:underline font-medium">
              <ArrowLeft className="w-4 h-4" /> Back to sign in
            </Link>
          </p>
        </form>
      )}
    </AuthLayout>
  );
}
