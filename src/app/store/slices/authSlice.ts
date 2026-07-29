import { createSlice, type PayloadAction } from '@reduxjs/toolkit';
import type { User } from '../../../shared/types';

export interface VerificationContext {
  type: 'email' | 'mfa';
  flow: 'registration' | 'login';
  userId: number;
  email: string;
}

interface AuthState {
  user: User | null;
  token: string | null;
  verification: VerificationContext | null;
}

function loadUser(): User | null {
  try {
    const raw = localStorage.getItem('user');
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

function loadVerification(): VerificationContext | null {
  try {
    const raw = localStorage.getItem('authVerification');
    return raw ? JSON.parse(raw) : null;
  } catch {
    return null;
  }
}

const initialState: AuthState = {
  user: loadUser(),
  token: localStorage.getItem('token'),
  verification: loadVerification(),
};

const authSlice = createSlice({
  name: 'auth',
  initialState,
  reducers: {
    setCredentials(state, action: PayloadAction<{ user: User; token: string }>) {
      state.user = action.payload.user;
      state.token = action.payload.token;
      state.verification = null;
      localStorage.setItem('token', action.payload.token);
      localStorage.setItem('user', JSON.stringify(action.payload.user));
      localStorage.removeItem('authVerification');
    },
    setVerificationContext(state, action: PayloadAction<VerificationContext>) {
      state.verification = action.payload;
      localStorage.setItem('authVerification', JSON.stringify(action.payload));
    },
    clearVerificationContext(state) {
      state.verification = null;
      localStorage.removeItem('authVerification');
    },
    logout(state) {
      state.user = null;
      state.token = null;
      state.verification = null;
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      localStorage.removeItem('authVerification');
    },
  },
});

export const { setCredentials, setVerificationContext, clearVerificationContext, logout } = authSlice.actions;
export default authSlice.reducer;
