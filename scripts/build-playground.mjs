import archiver from 'archiver';
import { createWriteStream } from 'node:fs';
import { mkdir, readFile, rm, writeFile } from 'node:fs/promises';
import { basename, dirname, join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const dist = join(projectRoot, 'playground', 'dist');
const releaseTag = process.env.NORTHLINE_RELEASE_TAG ?? 'v1.0.0';
const repository = process.env.GITHUB_REPOSITORY ?? 'kishi1997/northline-commerce-lab';
const releaseBase = `https://github.com/${repository}/releases/download/${releaseTag}`;

await rm(dist, { recursive: true, force: true });
await mkdir(dist, { recursive: true });

async function zipDirectory(source, destination) {
  await new Promise((resolvePromise, rejectPromise) => {
    const output = createWriteStream(destination);
    const archive = archiver('zip', { zlib: { level: 9 } });
    output.on('close', resolvePromise);
    output.on('error', rejectPromise);
    archive.on('warning', rejectPromise);
    archive.on('error', rejectPromise);
    archive.pipe(output);
    archive.directory(source, basename(source));
    archive.finalize();
  });
}

await zipDirectory(
  join(projectRoot, 'wp-content', 'themes', 'northline-storefront'),
  join(dist, 'northline-storefront.zip'),
);
await zipDirectory(
  join(projectRoot, 'wp-content', 'plugins', 'northline-commerce-rules'),
  join(dist, 'northline-commerce-rules.zip'),
);

const template = JSON.parse(await readFile(join(projectRoot, 'playground', 'blueprint.template.json'), 'utf8'));
const serialized = JSON.stringify(template)
  .replaceAll('{{RELEASE_BASE}}', releaseBase)
  .replaceAll('{{WOOCOMMERCE_VERSION}}', '10.9.4');
const storefront = JSON.parse(serialized);
const admin = structuredClone(storefront);
admin.landingPage = '/wp-admin/';
admin.steps.splice(1, 0, { step: 'login', username: 'admin', password: 'password' });

await writeFile(join(dist, 'storefront.json'), `${JSON.stringify(storefront, null, 2)}\n`);
await writeFile(join(dist, 'admin.json'), `${JSON.stringify(admin, null, 2)}\n`);

console.log(`Built ${dist}`);
console.log(`Release source: ${releaseBase}`);
