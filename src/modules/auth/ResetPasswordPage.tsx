import { useState } from 'react';
import { Link, useSearchParams } from 'react-router-dom';
import type { AxiosError } from 'axios';
import { useResetPassword } from '../../shared/api/auth/authQueries';
import type { ApiError } from '../../shared/api/auth/authTypes';
import { ROUTES } from '../../app/routes/constants';
import { Lock, Eye, EyeOff, KeyRound, CheckCircle } from 'lucide-react';
import AuthLayout from './AuthLayout';

export default function ResetPasswordPage() {
  const [searchParams] = useSearchParams();
  const resetMutation = useResetPassword();

  const token = searchParams.get('token') || searchParams.get('code') || '';
  const emailParam = searchParams.get('email') || '';

  const [email, setEmail] = useState(emailParam);
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

  const isSuccess = resetMutation.isSuccess;
  const axiosError = resetMutation.error as AxiosError<ApiError> | undefined;
  const error = axiosError?.response?.data?.message || axiosError?.message;

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (password !== passwordConfirmation) return;
    resetMutation.mutate({
      email,
      token,
      password,
      password_confirmation: passwordConfirmation,
    });
  };

  const passwordsMatch = !passwordConfirmation || password === passwordConfirmation;
  const inputCls = "w-full pl-11 pr-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors text-sm";

  if (isSuccess) {
    return (
      <AuthLayout title="Password Reset" subtitle="Your password has been changed successfully." heroImage="/images/class_discussion.jpg">
        <div className="text-center space-y-4 py-4">
          <div className="mx-auto w-16 h-16 rounded-full bg-success-light flex items-center justify-center">
            <CheckCircle className="w-8 h-8 text-success" />
          </div>
          <p className="text-sm text-gray-600">You can now sign in with your new password.</p>
          <Link
            to={ROUTES.AUTH.LOGIN}
            className="inline-block px-8 py-3 rounded-lg bg-primary text-white hover:bg-primary-dark transition-colors font-medium text-sm cursor-pointer"
          >
            Sign In
          </Link>
        </div>
      </AuthLayout>
    );
  }

  return (
    <AuthLayout title="Reset Password" subtitle="Enter your new password below." heroImage="/images/class_discussion.jpg">
      <form onSubmit={handleSubmit} className="space-y-4">
        <div className="relative">
          <KeyRound className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            required
            placeholder="Email address"
            className={inputCls}
          />
        </div>
        <div className="relative">
          <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input
            type={showPassword ? 'text' : 'password'}
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            minLength={8}
            placeholder="New password (min 8 chars)"
            className={`${inputCls} pr-12`}
          />
          <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
            {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
          </button>
        </div>
        <div className="relative">
          <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input
            type={showConfirm ? 'text' : 'password'}
            value={passwordConfirmation}
            onChange={(e) => setPasswordConfirmation(e.target.value)}
            required
            placeholder="Confirm new password"
            className={`${inputCls} pr-12`}
          />
          <button type="button" onClick={() => setShowConfirm(!showConfirm)} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
            {showConfirm ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
          </button>
        </div>
        {passwordConfirmation && !passwordsMatch && (
          <p className="text-xs text-alert-error-text -mt-1">Passwords do not match</p>
        )}
        {error && (
          <p className="text-sm text-alert-error-text bg-alert-error-bg border border-alert-error-border rounded-lg px-4 py-3">{error}</p>
        )}
        <button
          type="submit"
          disabled={resetMutation.isPending || (passwordConfirmation.length > 0 && !passwordsMatch)}
          className="w-full inline-flex items-center justify-center px-8 py-3.5 rounded-lg bg-primary text-white hover:bg-primary-dark transition-colors font-medium text-sm disabled:opacity-50 cursor-pointer"
        >
          {resetMutation.isPending ? (
            <span className="flex items-center gap-2"><span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" /> Resetting...</span>
          ) : (
            <span className="flex items-center gap-2"><KeyRound className="w-4 h-4" /> Reset Password</span>
          )}
        </button>
        <p className="text-center text-sm text-muted pt-1">
          <Link to={ROUTES.AUTH.LOGIN} className="text-primary hover:underline font-medium">Back to sign in</Link>
        </p>
      </form>
    </AuthLayout>
  );
}
