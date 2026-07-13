import type { ReactNode } from 'react';
import LoadingSpinner from './LoadingSpinner';
import EmptyState from './EmptyState';

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
}

export default function DataTable<T extends Record<string, unknown>>({ columns, data, isLoading, emptyMessage, emptyIcon, keyExtractor, actions }: DataTableProps<T>) {
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
                <th key={col.key} className={`text-left p-3 text-sm font-semibold text-gray-600 ${col.headClassName || ''}`}>{col.label}</th>
              ))}
              {actions && <th className="text-right p-3 text-sm font-semibold text-gray-600">Actions</th>}
            </tr>
          </thead>
          <tbody>
            {data.map(item => (
              <tr key={keyExtractor(item)} className="border-t border-border hover:bg-gray-50/50">
                {columns.map(col => (
                  <td key={col.key} className={`p-3 text-sm ${col.className || 'text-gray-600'}`}>
                    {col.render ? col.render(item) : String(item[col.key] ?? '-')}
                  </td>
                ))}
                {actions && <td className="p-3 text-right">{actions(item)}</td>}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
