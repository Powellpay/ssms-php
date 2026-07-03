import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { LibraryBook, BookLoan } from '../../types';

export const libKeys = {
  books: { list: () => ['library-books', 'list'] as const },
  loans: { list: () => ['book-loans', 'list'] as const },
};

export function useLibraryBooks() { return useQuery<LibraryBook[]>({ queryKey: libKeys.books.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.LIBRARY_BOOKS); return data.data ?? data; } }); }
export function useCreateLibraryBook() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<LibraryBook>) => { const { data } = await api.post(ENDPOINTS.LIBRARY_BOOKS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: libKeys.books.list() }) }); }
export function useUpdateLibraryBook() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<LibraryBook> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.LIBRARY_BOOKS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: libKeys.books.list() }) }); }
export function useDeleteLibraryBook() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.LIBRARY_BOOKS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: libKeys.books.list() }) }); }

export function useBookLoans() { return useQuery<BookLoan[]>({ queryKey: libKeys.loans.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.BOOK_LOANS); return data.data ?? data; } }); }
export function useCreateBookLoan() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<BookLoan>) => { const { data } = await api.post(ENDPOINTS.BOOK_LOANS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: libKeys.loans.list() }) }); }
export function useUpdateBookLoan() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<BookLoan> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.BOOK_LOANS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: libKeys.loans.list() }) }); }
export function useDeleteBookLoan() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.BOOK_LOANS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: libKeys.loans.list() }) }); }
