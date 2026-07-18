import { readFile, readdir } from 'node:fs/promises';
import { extname, join, relative } from 'node:path';

const root = new URL('../', import.meta.url).pathname;
const workflowRoot = join(root, '.github', 'workflows');
const scanRoots = [
  workflowRoot,
  join(root, 'playground'),
  join(root, 'wp-content'),
  join(root, 'package.json'),
  join(root, '.wp-env.json'),
];
const forbidden = [
  /OPENAI_API_KEY/i,
  /STRIPE_(?:SECRET|PUBLISHABLE)_KEY/i,
  /PAYPAL_(?:CLIENT|SECRET)/i,
  /SENDGRID_API_KEY/i,
  /MAILGUN_API_KEY/i,
  /AWS_(?:ACCESS|SECRET)/i,
  /wrangler\s+deploy/i,
  /vercel\s+(?:deploy|--prod)/i,
  /netlify\s+deploy/i,
];
const violations = [];

async function filesAt(target) {
  const entry = await import('node:fs/promises').then(({ stat }) => stat(target));
  if (entry.isFile()) return [target];

  const files = [];
  for (const child of await readdir(target, { withFileTypes: true })) {
    if (child.name === 'dist') continue;
    files.push(...await filesAt(join(target, child.name)));
  }
  return files;
}

for (const scanRoot of scanRoots) {
  for (const file of await filesAt(scanRoot)) {
    const extension = extname(file);
    if (!['', '.json', '.yml', '.yaml', '.php', '.js', '.mjs', '.html'].includes(extension)) continue;
    const source = await readFile(file, 'utf8');
    for (const pattern of forbidden) {
      if (pattern.test(source)) violations.push(`${relative(root, file)} matches ${pattern}`);
    }
  }
}

for (const file of await filesAt(workflowRoot)) {
  const source = await readFile(file, 'utf8');
  for (const line of source.split('\n')) {
    const runner = line.match(/^\s*runs-on:\s*(.+)\s*$/);
    if (runner && runner[1].trim() !== 'ubuntu-latest') {
      violations.push(`${relative(root, file)} uses a non-standard runner: ${runner[1].trim()}`);
    }
  }
}

if (violations.length) {
  console.error('Zero-cost policy violations:\n' + violations.join('\n'));
  process.exit(1);
}

console.log('zero-cost policy check passed');

