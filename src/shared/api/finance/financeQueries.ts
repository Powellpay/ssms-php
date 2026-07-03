import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '../axiosConfig';
import { ENDPOINTS } from '../endpoints';
import type { FeeStructure, Invoice, Payment } from '../../types';

export const finKeys = {
  feeStructures: { list: () => ['fee-structures', 'list'] as const },
  invoices: { list: () => ['invoices', 'list'] as const },
  payments: { list: () => ['payments', 'list'] as const },
};

export function useFeeStructures() { return useQuery<FeeStructure[]>({ queryKey: finKeys.feeStructures.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.FEE_STRUCTURES); return data.data ?? data; } }); }
export function useCreateFeeStructure() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<FeeStructure>) => { const { data } = await api.post(ENDPOINTS.FEE_STRUCTURES, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.feeStructures.list() }) }); }
export function useUpdateFeeStructure() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<FeeStructure> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.FEE_STRUCTURES}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.feeStructures.list() }) }); }
export function useDeleteFeeStructure() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.FEE_STRUCTURES}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.feeStructures.list() }) }); }

export function useInvoices() { return useQuery<Invoice[]>({ queryKey: finKeys.invoices.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.INVOICES); return data.data ?? data; } }); }
export function useCreateInvoice() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Invoice>) => { const { data } = await api.post(ENDPOINTS.INVOICES, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.invoices.list() }) }); }
export function useUpdateInvoice() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Invoice> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.INVOICES}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.invoices.list() }) }); }
export function useDeleteInvoice() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.INVOICES}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.invoices.list() }) }); }

export function usePayments() { return useQuery<Payment[]>({ queryKey: finKeys.payments.list(), queryFn: async () => { const { data } = await api.get(ENDPOINTS.PAYMENTS); return data.data ?? data; } }); }
export function useCreatePayment() { const qc = useQueryClient(); return useMutation({ mutationFn: async (p: Partial<Payment>) => { const { data } = await api.post(ENDPOINTS.PAYMENTS, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.payments.list() }) }); }
export function useUpdatePayment() { const qc = useQueryClient(); return useMutation({ mutationFn: async ({ id, ...p }: Partial<Payment> & { id: number }) => { const { data } = await api.put(`${ENDPOINTS.PAYMENTS}/${id}`, p); return data.data ?? data; }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.payments.list() }) }); }
export function useDeletePayment() { const qc = useQueryClient(); return useMutation({ mutationFn: async (id: number) => { await api.delete(`${ENDPOINTS.PAYMENTS}/${id}`); }, onSuccess: () => qc.invalidateQueries({ queryKey: finKeys.payments.list() }) }); }
