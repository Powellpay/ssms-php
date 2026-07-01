import { execSync } from 'child_process';

const root = new URL('..', import.meta.url).pathname;

execSync('node scripts/vera-fast.mjs', { cwd: root, stdio: 'inherit', shell: true });

console.log(' Vera extended: done.');
