import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAppDispatch } from '../../app/store/hooks';
import { setVerificationContext } from '../../app/store/slices/authSlice';
import { useRegister } from '../../shared/api/auth/authQueries';
import { ROUTES } from '../../app/routes/constants';
import { Mail, Lock, User, Phone, Eye, EyeOff, Building2, UserPlus } from 'lucide-react';
import AuthLayout from './AuthLayout';

export default function RegisterPage() {
  const navigate = useNavigate();
  const dispatch = useAppDispatch();
  const registerMutation = useRegister();
  const [form, setForm] = useState({
    first_name: '', last_name: '', school_name: '',
    email: '', phone: '', password: '', password_confirmation: '',
  });
  const [showPassword, setShowPassword] = useState(false);
  const [showConfirm, setShowConfirm] = useState(false);

  const handleChange = (field: string) => (e: React.ChangeEvent<HTMLInputElement>) => {
    setForm((prev) => ({ ...prev, [field]: e.target.value }));
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (form.password !== form.password_confirmation) return;
    registerMutation.mutate({
      name: `${form.first_name} ${form.last_name}`,
      email: form.email,
      password: form.password,
      username: form.email.split('@')[0],
      role_id: 1,
    }, {
      onSuccess: (data) => {
        if (data.user?.id) {
          dispatch(setVerificationContext({
            type: 'email',
            flow: 'registration',
            userId: data.user.id,
            email: form.email,
          }));
        }
        navigate(ROUTES.AUTH.VERIFY_EMAIL);
      },
    });
  };

  const error = registerMutation.error?.response?.data?.message || registerMutation.error?.message;
  const inputCls = "w-full pl-11 pr-4 py-3.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-colors text-sm";
  const passwordsMatch = !form.password_confirmation || form.password === form.password_confirmation;

  return (
    <AuthLayout title="Register Your School" subtitle="Free to use. Aligned to the Uganda NCDC Curriculum.">
      <form onSubmit={handleSubmit} className="space-y-4">
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div className="relative">
            <User className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
            <input placeholder="First name" value={form.first_name} onChange={handleChange('first_name')} required className={inputCls} />
          </div>
          <div className="relative">
            <User className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
            <input placeholder="Last name" value={form.last_name} onChange={handleChange('last_name')} required className={inputCls} />
          </div>
        </div>
        <div className="relative">
          <Building2 className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input placeholder="School name" value={form.school_name} onChange={handleChange('school_name')} required className={inputCls} />
        </div>
        <div className="relative">
          <Mail className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input type="email" placeholder="Email address" value={form.email} onChange={handleChange('email')} required className={inputCls} />
        </div>
        <div className="relative">
          <Phone className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input type="tel" placeholder="Phone number" value={form.phone} onChange={(e) => setForm(p => ({ ...p, phone: e.target.value.replace(/[^\d\s\-()+]/g, '') }))} className={inputCls} />
        </div>
        <div className="relative">
          <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input type={showPassword ? 'text' : 'password'} placeholder="Password (min 6 chars)" value={form.password} onChange={handleChange('password')} required className={`${inputCls} pr-12`} />
          <button type="button" onClick={() => setShowPassword(!showPassword)} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
            {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
          </button>
        </div>
        <div className="relative">
          <Lock className="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5 pointer-events-none" />
          <input type={showConfirm ? 'text' : 'password'} placeholder="Confirm password" value={form.password_confirmation} onChange={handleChange('password_confirmation')} required className={`${inputCls} pr-12`} />
          <button type="button" onClick={() => setShowConfirm(!showConfirm)} className="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
            {showConfirm ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
          </button>
        </div>
        {form.password_confirmation && !passwordsMatch && (
          <p className="text-xs text-alert-error-text -mt-1">Passwords do not match</p>
        )}
        {error && (
          <p className="text-sm text-alert-error-text bg-alert-error-bg border border-alert-error-border rounded-lg px-4 py-3">{error}</p>
        )}
        <button type="submit" disabled={registerMutation.isPending || (form.password_confirmation.length > 0 && !passwordsMatch)} className="w-full inline-flex items-center justify-center px-8 py-3.5 rounded-lg bg-primary text-white hover:bg-primary-dark transition-colors font-medium text-sm disabled:opacity-50 cursor-pointer">
          {registerMutation.isPending ? (
            <span className="flex items-center gap-2"><span className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" /> Registering...</span>
          ) : (
            <span className="flex items-center gap-2"><UserPlus className="w-4 h-4" /> Register Your School</span>
          )}
        </button>
        <p className="text-center text-sm text-muted pt-1">
          Already have an account?{' '}
          <Link to={ROUTES.AUTH.LOGIN} className="text-primary hover:underline font-medium">Sign in</Link>
        </p>
      </form>
    </AuthLayout>
  );
}
