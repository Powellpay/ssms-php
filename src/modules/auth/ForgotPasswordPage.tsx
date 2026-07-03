import { useState } from 'react';
import { Link } from 'react-router-dom';
import { ROUTES } from '../../app/routes/constants';
import { Mail, ArrowLeft, Send } from 'lucide-react';
import AuthLayout from './AuthLayout';

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState('');
  const [sent, setSent] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      // TODO: Implement password reset API call
      await new Promise((resolve) => setTimeout(resolve, 1000));
      setSent(true);
    } catch {
      setError('Failed to send reset link. Try again.');
    } finally {
      setLoading(false);
    }
  };

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
            disabled={loading}
            className="w-full inline-flex items-center justify-center px-8 py-3.5 rounded-lg bg-primary text-white hover:bg-primary-dark transition-colors font-medium text-sm disabled:opacity-50 cursor-pointer"
          >
            {loading ? (
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
