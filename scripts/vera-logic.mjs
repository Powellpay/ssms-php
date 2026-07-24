/**
 * Vera Logic — repo rules & contracts (not oxlint).
 * Runs on every Vera Fast handoff after lint.
 *
 * Usage: node scripts/vera-logic.mjs
 */
import { execSync } from 'child_process';
import fs from 'fs';
import path from 'path';

const ROOT = process.cwd();
const MAX_LINES = 500;

/** @typedef {{ id: string, ok: boolean, detail: string }} RuleResult */

function read(relPath) {
  const full = path.join(ROOT, relPath);
  if (!fs.existsSync(full)) return null;
  return fs.readFileSync(full, 'utf8');
}

function lineCount(relPath) {
  const text = read(relPath);
  if (text == null) return 0;
  return text.split(/\r?\n/).length;
}

function getChangedTsFiles() {
  const commands = [
    'git diff --name-only --diff-filter=ACMRTUXB HEAD',
    'git diff --cached --name-only --diff-filter=ACMRTUXB',
    'git ls-files --others --exclude-standard',
  ];
  const files = new Set();
  for (const cmd of commands) {
    try {
      const out = execSync(cmd, { encoding: 'utf8', stdio: ['pipe', 'pipe', 'ignore'] });
      for (const line of out.split('\n')) {
        const trimmed = line.trim().replace(/\\/g, '/');
        if (
          trimmed
          && (trimmed.endsWith('.ts') || trimmed.endsWith('.tsx'))
          && trimmed.startsWith('src/')
        ) {
          files.add(trimmed);
        }
      }
    } catch {
      // ignore
    }
  }
  return [...files];
}

/** Resolve a relative import specifier to an existing file under ROOT. */
function relativeImportExists(fromFile, specifier) {
  const clean = specifier.split('?')[0];
  if (!clean.startsWith('.')) return true;
  const fromDir = path.dirname(path.join(ROOT, fromFile));
  const base = path.resolve(fromDir, clean);
  const candidates = [
    base,
    `${base}.ts`,
    `${base}.tsx`,
    `${base}.js`,
    `${base}.jsx`,
    `${base}.css`,
    `${base}.json`,
    path.join(base, 'index.ts'),
    path.join(base, 'index.tsx'),
    path.join(base, 'index.js'),
  ];
  return candidates.some((candidate) => fs.existsSync(candidate));
}

/**
 * Catch broken relative imports (same class of error as Vite import-analysis).
 * @returns {RuleResult}
 */
function checkRelativeImports(changedFiles) {
  const importRe = /(?:from\s+|import\s*\(\s*)['"](\.[^'"]+)['"]/g;
  const broken = [];

  for (const file of changedFiles) {
    const text = read(file);
    if (text == null) continue;
    importRe.lastIndex = 0;
    let match;
    while ((match = importRe.exec(text)) !== null) {
      const spec = match[1];
      if (!spec) continue;
      if (!relativeImportExists(file, spec)) {
        broken.push(`${file} → ${spec}`);
      }
    }
  }

  if (broken.length) {
    return {
      id: 'relative-imports',
      ok: false,
      detail: `Unresolved relative import(s): ${broken.slice(0, 8).join('; ')}${broken.length > 8 ? ` (+${broken.length - 8} more)` : ''}`,
    };
  }

  return {
    id: 'relative-imports',
    ok: true,
    detail: changedFiles.length
      ? `Relative imports resolve for ${changedFiles.length} changed file(s)`
      : 'No changed TS/TSX under src/ — import check skipped',
  };
}

/** @returns {RuleResult[]} */
function checkFileSizeLimit(changedFiles) {
  const results = [];
  const targets = changedFiles.length > 0
    ? changedFiles
    : [];

  for (const file of targets) {
    const lines = lineCount(file);
    if (lines > MAX_LINES) {
      results.push({
        id: 'file-size-500',
        ok: false,
        detail: `${file} has ${lines} lines (max ${MAX_LINES})`,
      });
    }
  }

  if (results.length === 0) {
    results.push({
      id: 'file-size-500',
      ok: true,
      detail: targets.length
        ? `Changed source files ≤ ${MAX_LINES} lines (${targets.length} checked)`
        : 'No changed TS/TSX under src/ — size check skipped',
    });
  }

  return results;
}

/** @returns {RuleResult} */
function checkRouteConstantsExist() {
  const routes = read('src/app/routes/constants.ts') ?? '';
  const hasRoutes = routes.length > 0 && routes.includes('export const ROUTES');
  return {
    id: 'route-constants',
    ok: hasRoutes,
    detail: hasRoutes
      ? 'Route constants defined in src/app/routes/constants.ts'
      : 'Missing or empty src/app/routes/constants.ts',
  };
}

/** @returns {RuleResult} */
function checkEndpointsExist() {
  const endpoints = read('src/shared/api/endpoints.ts') ?? '';
  const hasEndpoints = endpoints.length > 0 && endpoints.includes('export const ENDPOINTS');
  return {
    id: 'api-endpoints',
    ok: hasEndpoints,
    detail: hasEndpoints
      ? 'API endpoints defined in src/shared/api/endpoints.ts'
      : 'Missing or empty src/shared/api/endpoints.ts',
  };
}

const changed = getChangedTsFiles();
const results = [
  ...checkFileSizeLimit(changed),
  checkRelativeImports(changed),
  checkRouteConstantsExist(),
  checkEndpointsExist(),
];

const failed = results.filter((r) => !r.ok);

console.log(`🧪 Vera logic: ${results.length} rule(s)`);
for (const r of results) {
  console.log(`  ${r.ok ? '✅' : '❌'} [${r.id}] ${r.detail}`);
}

if (failed.length) {
  console.log(`❌ Vera logic: failed (${failed.length})`);
  process.exit(1);
}

console.log('✅ Vera logic: passed');
process.exit(0);
