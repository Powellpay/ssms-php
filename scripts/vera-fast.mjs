import { execSync } from 'child_process';

const root = new URL('..', import.meta.url).pathname;
const { stdout } = execSync('git diff --name-only --diff-filter=ACMRTUXB HEAD && git diff --cached --name-only --diff-filter=ACMRTUXB', { cwd: root });

const files = stdout.toString().split('\n').filter(Boolean).filter(f => f.endsWith('.ts') || f.endsWith('.tsx'));

if (files.length === 0) {
  console.log(' Vera fast: no changed TS/TSX files — skipped.');
} else {
  console.log(` Vera fast: oxlint —fix on ${files.length} file(s)`);

  try {
    execSync(`npx oxlint --fix ${files.join(' ')}`, { cwd: root, stdio: 'inherit' });
    console.log(' Vera fast: pass');
  } catch {
    console.log(' Vera fast: fail');
    process.exit(1);
  }
}

try {
  execSync('node scripts/vera-logic.mjs', { cwd: root, stdio: 'inherit', shell: true });
} catch {
  process.exit(1);
}
