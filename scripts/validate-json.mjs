import { readFile, readdir } from 'node:fs/promises';
import { extname, join, relative } from 'node:path';

const root = new URL('../', import.meta.url).pathname;
const ignored = new Set([
  'node_modules',
  'vendor',
  '.git',
  '.wp-env',
  '.phpunit.cache',
  'playground/dist',
  'test-results',
  'playwright-report',
]);
const errors = [];

async function walk(directory) {
  for (const entry of await readdir(directory, { withFileTypes: true })) {
    const absolute = join(directory, entry.name);
    const path = relative(root, absolute);

    if (entry.isDirectory()) {
      if (!ignored.has(entry.name) && !ignored.has(path)) {
        await walk(absolute);
      }
      continue;
    }

    if (extname(entry.name) !== '.json') {
      continue;
    }

    try {
      JSON.parse(await readFile(absolute, 'utf8'));
      console.log(`valid ${path}`);
    } catch (error) {
      errors.push(`${path}: ${error.message}`);
    }
  }
}

await walk(root);

if (errors.length > 0) {
  console.error(errors.join('\n'));
  process.exit(1);
}
