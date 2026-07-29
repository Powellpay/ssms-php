import { useState, useMemo, useEffect } from 'react';
import type { ReactNode } from 'react';
import LoadingSpinner from './LoadingSpinner';
import EmptyState from './EmptyState';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '../../utils/cn';

export interface Column<T> {
  key: string;
  label: string;
  render?: (item: T) => ReactNode;
  className?: string;
  headClassName?: string;
}

interface DataTableProps<T> {
  columns: Column<T>[];
  data: T[] | undefined;
  isLoading: boolean;
  emptyMessage?: string;
  emptyIcon?: ReactNode;
  keyExtractor: (item: T) => string | number;
  actions?: (item: T) => ReactNode;
  pageSize?: number;
}

export default function DataTable<T>({ columns, data, isLoading, emptyMessage, emptyIcon, keyExtractor, actions, pageSize = 25 }: DataTableProps<T>) {
  const [page, setPage] = useState(1);

  const totalPages = useMemo(() => data ? Math.ceil(data.length / pageSize) : 0, [data, pageSize]);
  const paginatedData = useMemo(() => {
    if (!data) return [];
    const start = (page - 1) * pageSize;
    return data.slice(start, start + pageSize);
  }, [data, page, pageSize]);

  useEffect(() => {
    if (totalPages > 0 && page > totalPages) {
      setPage(1);
    }
  }, [data]);

  if (isLoading) {
    return <div className="rounded-xl border border-border bg-white overflow-hidden"><LoadingSpinner /></div>;
  }

  if (!data?.length) {
    return <div className="rounded-xl border border-border bg-white overflow-hidden"><EmptyState message={emptyMessage || 'No records found.'} icon={emptyIcon} /></div>;
  }

  return (
    <div className="rounded-xl border border-border bg-white overflow-hidden">
      <div className="overflow-x-auto">
        <table className="w-full">
          <thead>
            <tr className="bg-gray-50">
              {columns.map(col => (
                <th key={col.key} className={cn('text-left p-3 text-sm font-semibold text-gray-600', col.headClassName)}>{col.label}</th>
              ))}
              {actions && <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>}
            </tr>
          </thead>
          <tbody>
            {paginatedData.map(item => (
              <tr key={keyExtractor(item)} className="border-t border-border hover:bg-gray-50/50">
                {columns.map(col => (
                  <td key={col.key} className={cn('p-3 text-sm', col.className || 'text-gray-600')}>
                    {col.render ? col.render(item) : String((item as Record<string, unknown>)[col.key] ?? '-')}
                  </td>
                ))}
                {actions && <td className="p-3 text-right">{actions(item)}</td>}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {totalPages > 1 && (
        <div className="flex items-center justify-between px-4 py-3 border-t border-border bg-gray-50/50">
          <span className="text-sm text-muted">
            Showing {((page - 1) * pageSize) + 1}–{Math.min(page * pageSize, data.length)} of {data.length}
          </span>
          <div className="flex items-center gap-1">
            <button
              onClick={() => setPage(p => Math.max(1, p - 1))}
              disabled={page <= 1}
              className="p-1.5 rounded text-gray-500 hover:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
            >
              <ChevronLeft className="w-4 h-4" />
            </button>
            {Array.from({ length: Math.min(totalPages, 5) }, (_, i) => {
              let pageNum: number;
              if (totalPages <= 5) {
                pageNum = i + 1;
              } else if (page <= 3) {
                pageNum = i + 1;
              } else if (page >= totalPages - 2) {
                pageNum = totalPages - 4 + i;
              } else {
                pageNum = page - 2 + i;
              }
              return (
                <button
                  key={pageNum}
                  onClick={() => setPage(pageNum)}
                  className={cn(
                    'w-8 h-8 rounded text-sm font-medium cursor-pointer',
                    page === pageNum ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-200'
                  )}
                >
                  {pageNum}
                </button>
              );
            })}
            <button
              onClick={() => setPage(p => Math.min(totalPages, p + 1))}
              disabled={page >= totalPages}
              className="p-1.5 rounded text-gray-500 hover:bg-gray-200 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
            >
              <ChevronRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
