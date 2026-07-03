import './axiosTypes';
import axios, { AxiosHeaders, type AxiosInstance, type AxiosError } from 'axios';
import { QueryClient } from '@tanstack/react-query';
import { API_BASE_URL, API_TIMEOUT } from './apiConfig';

const api: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT,
  headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token');
  if (token) {
    const headers = config.headers ? AxiosHeaders.from(config.headers) : new AxiosHeaders();
    headers.set('Authorization', `Bearer ${token}`);
    config.headers = headers;
  }
  return config;
});

let isHandling401 = false;

api.interceptors.response.use(
  (response) => response,
  (error: AxiosError) => {
    const isAuthUrl = error.config?.url?.includes('/auth/');
    const hasToken = !!localStorage.getItem('token');

    if (
      error.response?.status === 401 &&
      hasToken &&
      !isAuthUrl &&
      !isHandling401 &&
      !error.config?.skipAuthRedirect
    ) {
      isHandling401 = true;
      localStorage.removeItem('token');
      window.location.href = '/login';
      setTimeout(() => { isHandling401 = false; }, 3000);
    }
    return Promise.reject(error);
  },
);

export const queryClient = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 1000 * 60 * 2,
      gcTime: 1000 * 60 * 10,
      retry: 1,
      refetchOnWindowFocus: false,
    },
    mutations: { retry: 0 },
  },
});

export default api;
