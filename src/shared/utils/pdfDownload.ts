import api from '../api/axiosConfig';

export async function downloadPdf(url: string, filename: string) {
  const { data } = await api.get(url, { responseType: 'blob' });
  const blob = new Blob([data], { type: 'application/pdf' });
  const blobUrl = window.URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = blobUrl;
  link.setAttribute('download', filename);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  window.URL.revokeObjectURL(blobUrl);
}
