import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../../../app/api/axiosConfig';
import { setCredentials } from '../../../app/store/slices/authSlice';
import { useAppDispatch } from '../../../app/store/hooks';

export default function AuthModule() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const dispatch = useAppDispatch();
  const navigate = useNavigate();

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    try {
      const { data } = await api.post('/auth/login', { email, password });
      dispatch(setCredentials({ user: data.user, token: data.token }));
      navigate('/dashboard');
    } catch {
      setError('Invalid credentials');
    }
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gray-50">
      <div className="w-full max-w-md rounded-lg bg-white p-8 shadow-card">
        <h1 className="mb-6 text-h2 text-neutral-black">SSMS Login</h1>
        {error && <p className="mb-4 text-critical">{error}</p>}
        <form onSubmit={handleLogin} className="space-y-4">
          <div>
            <label className="mb-1 block text-body-sm text-neutral-gray-dark">Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full rounded-md border border-neutral-gray-light p-2 text-body"
              required
            />
          </div>
          <div>
            <label className="mb-1 block text-body-sm text-neutral-gray-dark">Password</label>
            <input
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full rounded-md border border-neutral-gray-light p-2 text-body"
              required
            />
          </div>
          <button
            type="submit"
            className="w-full rounded-md bg-primary px-4 py-2 text-body text-white hover:bg-primary-hover"
          >
            Sign In
          </button>
        </form>
      </div>
    </div>
  );
}
