import { chromium } from 'playwright';
import { spawn, execSync } from 'child_process';
import { readFileSync, mkdirSync, writeFileSync } from 'fs';
import { resolve, dirname } from 'path';
import { fileURLToPath } from 'url';

const __dirname = dirname(fileURLToPath(import.meta.url));
const ROOT = resolve(__dirname, '..');
const OUT = resolve(ROOT, 'screenshots');
const PORT = 5199;
const BASE = `http://localhost:${PORT}`;

try { execSync(`kill $(lsof -ti:${PORT}) 2>/dev/null || true`, { stdio: 'ignore' }); } catch {}

async function startDev() {
  return new Promise((res, rej) => {
    const proc = spawn('npx', ['vite', '--port', String(PORT), '--host'], {
      cwd: ROOT,
      stdio: ['ignore', 'pipe', 'pipe'],
      shell: true,
    });
    const timeout = setTimeout(() => rej(new Error('Vite did not start within 30s')), 30000);
    const handler = (d) => {
      const text = d.toString();
      if (text.includes('Local:') || text.includes('ready') || text.includes('localhost')) {
        clearTimeout(timeout);
        res({ proc, url: BASE });
      }
    };
    proc.stdout.on('data', handler);
    proc.stderr.on('data', handler);
  });
}

function codeToHtml(filePath, title) {
  const code = readFileSync(filePath, 'utf8');
  const escaped = code
    .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  return `<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>${title}</title>
<style>
  *{margin:0;padding:0;box-sizing:border-box}
  body{background:#1e1e1e;padding:24px}
  .hdr{color:#888;font-size:13px;margin-bottom:12px;font-family:sans-serif}
  pre{background:#252526;border-radius:8px;padding:20px;overflow-x:auto;border:1px solid #333}
  code{color:#d4d4d4;font-size:13px;line-height:1.6;font-family:'Cascadia Code','Fira Code','Consolas',monospace;white-space:pre}
</style></head><body>
<div class="hdr">${title}</div>
<pre><code>${escaped}</code></pre>
</body></html>`;
}

async function shot(browser, url, name) {
  const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
  await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 });
  await page.screenshot({ path: resolve(OUT, `${name}.png`), fullPage: true });
  console.log(`  ✅ ${name}.png`);
  await page.close();
}

async function shotCode(browser, fp, title, name) {
  const abs = resolve(ROOT, fp);
  const html = codeToHtml(abs, title);
  const tmp = resolve(OUT, '_tmp.html');
  writeFileSync(tmp, html);
  await shot(browser, `file://${tmp}`, name);
}

const ISSUES = [
  ['fe-1-modal', 'code', 'src/shared/components/ui/Modal.tsx', 'FE-1: Modal component'],
  ['fe-1-refactor', 'code', 'src/modules/staff/ui/StaffListPage.tsx', 'FE-1: StaffListPage using shared components'],
  ['fe-3-student-detail', 'code', 'src/modules/students/ui/StudentsListPage.tsx', 'FE-3: Student list page (pre-detail)'],
  ['fe-5-subjects', 'code', 'src/modules/curriculum/ui/SubjectsPage.tsx', 'FE-5: SubjectsPage using shared components'],
  ['fe-10-loading', 'code', 'src/shared/components/ui/LoadingSpinner.tsx', 'FE-10: LoadingSpinner component'],
  ['fe-10-empty', 'code', 'src/shared/components/ui/EmptyState.tsx', 'FE-10: EmptyState component'],
  ['fe-10-pageheader', 'code', 'src/shared/components/ui/PageHeader.tsx', 'FE-10: PageHeader component'],
  ['fe-10-datatable', 'code', 'src/shared/components/ui/DataTable.tsx', 'FE-10: DataTable component'],
  ['fe-10-confirm', 'code', 'src/shared/components/ui/ConfirmDialog.tsx', 'FE-10: ConfirmDialog component'],
  ['be-3-stream-test', 'code', '../backend/tests/Feature/Api/StreamTest.php', 'BE-3: Stream test coverage'],
];

async function main() {
  mkdirSync(OUT, { recursive: true });
  console.log(' Starting Vite...');
  const { proc } = await startDev().catch(() => {
    console.log(' (using already-running Vite at port ' + PORT + ')');
    return { proc: null, url: BASE };
  });
  console.log(` Dev server ready`);

  const browser = await chromium.launch({ headless: true });

  for (const [name, type, path, desc] of ISSUES) {
    console.log(`\n📸 ${name} — ${desc}`);
    try {
      if (type === 'page') {
        await shot(browser, `${BASE}${path}`, name);
      } else {
        await shotCode(browser, path, desc, name);
      }
    } catch (err) {
      console.log(`  ❌ ${err.message}`);
    }
  }

  await browser.close();
  if (proc) proc.kill();
  console.log('\n✅ Done — screenshots saved to screenshots/');
}

main().catch((e) => { console.error(e); process.exit(1); });
