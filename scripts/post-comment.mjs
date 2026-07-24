import https from 'https';

const TOKEN = process.env.GH_TOKEN || '';
const body = `## 📸 Screenshots — FE-1: Shared UI Components

The screenshots are available locally in the \`screenshots/\` folder. Drag & drop them into this comment box to attach.

| File | Description |
|------|-------------|
| fe-1-dashboard.png | Dashboard — main landing after login |
| fe-1-academic-years.png | Academic Years — PageHeader + DataTable in action |
| fe-1-staff-list.png | Staff list — refactored with shared components |
| fe-1-modal-open.png | Academic Years — Modal component open with form |
| fe-1-subjects.png | Subjects — DataTable + PageHeader |

### Quick attach:
You can drag any of these .png files from \`frontend/screenshots/\` directly onto this comment to upload them.`;

const data = JSON.stringify({ body });
const req = https.request({
  hostname: 'api.github.com',
  path: '/repos/Powellpay/ssms-php/issues/1/comments',
  method: 'POST',
  headers: {
    Authorization: `Bearer ${TOKEN}`,
    'Content-Type': 'application/json',
    'User-Agent': 'screenshot-script',
    'Content-Length': Buffer.byteLength(data),
  },
}, (res) => {
  let d = '';
  res.on('data', c => d += c);
  res.on('end', () => {
    try { console.log(JSON.parse(d).html_url || d); } catch { console.log(d); }
  });
});
req.write(data);
req.end();
