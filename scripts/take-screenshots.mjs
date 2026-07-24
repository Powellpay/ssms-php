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
      cwd: ROOT, stdio: ['ignore', 'pipe', 'pipe'], shell: true,
    });
    const timeout = setTimeout(() => rej(new Error('Vite timeout')), 30000);
    const check = (d) => {
      if (d.toString().includes('Local:') || d.toString().includes('localhost')) {
        clearTimeout(timeout); res({ proc });
      }
    };
    proc.stdout.on('data', check); proc.stderr.on('data', check);
  });
}

async function shot(browser, url, name, opts = {}) {
  const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });

  await page.addInitScript(() => {
    localStorage.setItem('token', 'mock-token-for-screenshot');
    localStorage.setItem('user', JSON.stringify({
      id: 1, name: 'Admin User', email: 'admin@school.com',
      role_id: 1, status: 'active', username: 'admin',
    }));
  });

  await page.goto(url, { waitUntil: 'networkidle', timeout: 15000 }).catch(() => {});
  await page.waitForTimeout(2000);

  if (opts.clickButton) {
    try {
      const btn = page.getByRole('button', { name: opts.clickButton });
      if (await btn.isVisible()) { await btn.click(); await page.waitForTimeout(800); }
    } catch {}
  }

  await page.screenshot({ path: resolve(OUT, `${name}.png`), fullPage: true });
  console.log(`  ✅ ${name}.png`);
  await page.close();
}

const SHOTS = [
  { name: 'fe-1-dashboard', url: '/dashboard', desc: 'Dashboard — main landing after login' },
  { name: 'fe-1-academic-years', url: '/academic/years', desc: 'Academic Years — PageHeader + DataTable' },
  { name: 'fe-1-staff-list', url: '/staff', desc: 'Staff — refactored with shared components' },
  { name: 'fe-1-modal-open', url: '/academic/years', desc: 'Academic Years — Add modal open',
    clickButton: 'Add Year' },
  { name: 'fe-1-subjects', url: '/curriculum/subjects', desc: 'Subjects — DataTable + Modal' },
  { name: 'fe-10-students', url: '/students', desc: 'Students list page' },
];

async function main() {
  mkdirSync(OUT, { recursive: true });
  console.log(' Starting Vite...');
  await startDev().catch(() => console.log(' (using existing Vite)'));
  console.log(' Dev server ready');

  const browser = await chromium.launch({ headless: true });

  for (const s of SHOTS) {
    console.log(`\n📸 ${s.name} — ${s.desc}`);
    try {
      await shot(browser, `${BASE}${s.url}`, s.name, s);
    } catch (err) {
      console.log(`  ❌ ${err.message}`);
    }
  }

  await browser.close();
  console.log('\n✅ Done — screenshots in screenshots/');
}

main().catch((e) => { console.error(e); process.exit(1); });
