import { useState, useEffect, useRef } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAppDispatch, useAppSelector } from '../../app/store/hooks';
import { setCredentials } from '../../app/store/slices/authSlice';
import type { AxiosError } from 'axios';
import type { ApiError } from '../../shared/api/auth/authTypes';
import { useVerifyEmail, useResendVerification } from '../../shared/api/auth/authQueries';
import { ROUTES } from '../../app/routes/constants';
import { CheckCircle, AlertCircle, RefreshCw } from 'lucide-react';
import AuthLayout from './AuthLayout';

const RESEND_COOLDOWN = 60;

export default function VerifyEmailPage() {
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const verification = useAppSelector((s) => s.auth.verification);
  const verifyMutation = useVerifyEmail();
  const resendMutation = useResendVerification();

  const [code, setCode] = useState(['', '', '', '', '', '']);
  const [cooldown, setCooldown] = useState(0);
  const [resendMsg, setResendMsg] = useState('');
  const inputRefs = useRef<(HTMLInputElement | null)[]>([]);

  useEffect(() => {
    if (!verification) {
      navigate(ROUTES.AUTH.LOGIN, { replace: true });
    }
  }, [verification, navigate]);

  useEffect(() => {
    if (cooldown > 0) {
      const t = setTimeout(() => setCooldown((c) => c - 1), 1000);
      return () => clearTimeout(t);
    }
  }, [cooldown]);

  const handleChange = (index: number, value: string) => {
    if (!/^\d?$/.test(value)) return;
    const newCode = [...code];
    newCode[index] = value;
    setCode(newCode);

    if (value && index < 5) {
      inputRefs.current[index + 1]?.focus();
    }

    if (newCode.every((d) => d !== '') && verification) {
      verifyMutation.mutate(
        { user_id: verification.userId, code: newCode.join('') },
        {
          onSuccess: (data) => {
            if (data.user && data.token) {
              dispatch(setCredentials({ user: data.user, token: data.token }));
            }
            setTimeout(() => {
              if (verification.flow === 'registration') {
                navigate(ROUTES.DASHBOARD, { replace: true });
              } else {
                navigate(ROUTES.DASHBOARD, { replace: true });
              }
            }, 1500);
          },
        },
      );
    }
  };

  const handleKeyDown = (index: number, e: React.KeyboardEvent) => {
    if (e.key === 'Backspace' && !code[index] && index > 0) {
      inputRefs.current[index - 1]?.focus();
    }
  };

  const handleResend = () => {
    if (cooldown > 0 || !verification) return;
    resendMutation.mutate(
      { email: verification.email },
      {
        onSuccess: () => {
          setCooldown(RESEND_COOLDOWN);
          setCode(['', '', '', '', '', '']);
          setResendMsg('');
          inputRefs.current[0]?.focus();
        },
        onError: (err) => {
          const axiosErr = err as AxiosError<ApiError>;
          setResendMsg(axiosErr.response?.data?.message || 'Failed to resend code.');
        },
      },
    );
  };

  if (!verification) return null;

  const isVerified = verifyMutation.isSuccess && verifyMutation.data?.success;
  const verifyError = verifyMutation.error as AxiosError<ApiError> | undefined;
  const error = verifyError?.response?.data?.message;

  return (
    <AuthLayout
      title="Verify Your Email"
      subtitle={`Enter the 6-digit code sent to ${verification.email}`}
    >
      <div className="space-y-6">
        {isVerified ? (
          <div className="text-center space-y-4 py-4">
            <div className="mx-auto w-16 h-16 rounded-full bg-success-light flex items-center justify-center">
              <CheckCircle className="w-8 h-8 text-success" />
            </div>
            <p className="text-sm font-medium text-gray-900">Email Verified!</p>
            <p className="text-xs text-muted">Redirecting to dashboard...</p>
          </div>
        ) : (
          <>
            <div className="flex justify-center gap-2.5">
              {code.map((digit, i) => (
                <input
                  key={i}
                  ref={(el) => { inputRefs.current[i] = el; }}
                  type="text"
                  inputMode="numeric"
                  maxLength={1}
                  value={digit}
                  onChange={(e) => handleChange(i, e.target.value)}
                  onKeyDown={(e) => handleKeyDown(i, e)}
                  className="w-12 h-14 text-center text-xl font-bold border-2 border-gray-300 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary outline-none transition-colors"
                  autoFocus={i === 0}
                />
              ))}
            </div>

            {verifyMutation.isPending && (
              <p className="text-center text-sm text-muted">Verifying code...</p>
            )}

            {error && (
              <p className="text-sm text-alert-error-text bg-alert-error-bg border border-alert-error-border rounded-lg px-4 py-3 flex items-center gap-2">
                <AlertCircle className="w-4 h-4 shrink-0" />
                {error}
              </p>
            )}

            {resendMsg && (
              <p className="text-sm text-primary text-center">{resendMsg}</p>
            )}

            <div className="text-center">
              <button
                type="button"
                onClick={handleResend}
                disabled={cooldown > 0 || resendMutation.isPending}
                className="group inline-flex items-center gap-1.5 text-sm text-primary hover:underline font-medium disabled:text-gray-400 disabled:no-underline cursor-pointer"
              >
                <RefreshCw className={`w-3.5 h-3.5 transition-transform duration-500 ${resendMutation.isPending ? 'animate-spin' : ''} ${cooldown === 0 && !resendMutation.isPending ? 'group-hover:rotate-180' : ''}`} />
                {cooldown > 0
                  ? `Resend code in ${cooldown}s`
                  : 'Resend code'}
              </button>
            </div>
          </>
        )}

        <div className="text-center border-t border-border pt-4">
          <Link to={ROUTES.AUTH.LOGIN} className="text-xs text-muted hover:text-primary">
            Back to sign in
          </Link>
        </div>
      </div>
    </AuthLayout>
  );
}
