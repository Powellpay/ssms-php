import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAppDispatch } from '../../app/store/hooks';
import { setCredentials } from '../../app/store/slices/authSlice';
import api from '../../app/api/axiosConfig';
import { ROUTES } from '../../app/routes/constants';
import { Mail, Lock, Eye, EyeOff, LogIn } from 'lucide-react';
import AuthLayout from './AuthLayout';

export default function LoginPage() {
  const dispatch = useAppDispatch();
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const { data } = await api.post('/auth/login', { email, password });
      dispatch(setCredentials({ user: data.user, token: data.token }));
      navigate(ROUTES.DASHBOARD);
    } catch (err: unknown) {
      if (err && typeof err === 'object' && 'response' in err) {
        const axiosErr = err as { response?: { data?: { message?: string } } };
        setError(axiosErr.response?.data?.message || 'Invalid credentials');
      } else {
        setError('Could not reach the server. Check your connection.');
      }
    } finally {
      setLoading(false);
    }
  };

  const inputCls = "w-full pl-11 pr-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors text-sm";

  return (
    <AuthLayout title="Sign In" subtitle="Welcome back to SSMS">
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

        <div className="relative">
          <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input
            type={showPassword ? 'text' : 'password'}
            value={password}
            onChange={(e) => setPassword(e.target.value)}
            required
            placeholder="Password"
            className={`${inputCls} pr-12`}
          />
          <button
            type="button"
            onClick={() => setShowPassword(!showPassword)}
            className="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer"
          >
            {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
          </button>
        </div>

        <div className="text-right -mt-3">
          <Link to={ROUTES.AUTH.FORGOT_PASSWORD} className="text-xs text-primary hover:underline font-medium">
            Forgot password?
          </Link>
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
              Signing in...
            </span>
          ) : (
            <span className="flex items-center gap-2"><LogIn className="w-4 h-4" /> Sign In</span>
          )}
        </button>

        <p className="text-center text-sm text-muted pt-2">
          Don't have an account?{' '}
          <Link to={ROUTES.AUTH.REGISTER} className="text-primary hover:underline font-medium">
            Register your school
          </Link>
        </p>
      </form>
    </AuthLayout>
  );
}
