function parseApiDate(value: string): Date {
  if (/^\d{4}-\d{2}-\d{2}$/.test(value.trim())) {
    const [y, m, d] = value.trim().split('-').map(Number);
    return new Date(y, m - 1, d);
  }
  return new Date(value);
}

export function formatDate(iso: string | null | undefined): string {
  if (!iso) return '\u2014';
  const d = parseApiDate(iso);
  if (Number.isNaN(d.getTime())) return '\u2014';
  return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

export function formatDateTime(iso: string | null | undefined): string {
  if (!iso) return '\u2014';
  const d = parseApiDate(iso);
  if (Number.isNaN(d.getTime())) return '\u2014';
  const datePart = d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
  const hasTime = /T\d{2}:\d{2}/.test(iso);
  if (!hasTime) return datePart;
  return `${datePart}, ${d.toLocaleTimeString('en-GB', { hour: 'numeric', minute: '2-digit', hour12: true })}`;
}
