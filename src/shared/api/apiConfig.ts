import { API_VERSION } from '../config';

export const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://127.0.0.1:8000/api';
export const API_VERSIONED_URL = `${API_BASE_URL}/${API_VERSION}`;
export const API_TIMEOUT = Number(import.meta.env.VITE_API_TIMEOUT) || 30000;
