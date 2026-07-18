import { chromium } from 'playwright';
import { mkdir } from 'node:fs/promises';
import { join, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const projectRoot = resolve(fileURLToPath(new URL('../', import.meta.url)));
const outputDirectory = join(projectRoot, 'docs', 'screenshots');
const baseUrl = process.env.WP_BASE_URL ?? 'http://localhost:8888';

await mkdir(outputDirectory, { recursive: true });

const browser = await chromium.launch({ headless: true });

async function loadLazyAssets(page) {
  await page.evaluate(async () => {
    const step = Math.max(window.innerHeight * 0.75, 400);

    for (let position = 0; position < document.documentElement.scrollHeight; position += step) {
      window.scrollTo(0, position);
      await new Promise((resolveStep) => window.setTimeout(resolveStep, 80));
    }

    window.scrollTo(0, 0);
  });
  await page.waitForTimeout(400);
}

try {
  const desktop = await browser.newPage({ viewport: { width: 1440, height: 1000 }, deviceScaleFactor: 1 });
  await desktop.goto(baseUrl, { waitUntil: 'networkidle' });
  await loadLazyAssets(desktop);
  await desktop.screenshot({ path: join(outputDirectory, 'homepage-desktop.png'), fullPage: true });

  await desktop.goto(`${baseUrl}/product/nl-core-2-0-precision-device/`, { waitUntil: 'networkidle' });
  await loadLazyAssets(desktop);
  await desktop.screenshot({ path: join(outputDirectory, 'product-desktop.png'), fullPage: true });

  const mobile = await browser.newPage({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 1 });
  await mobile.goto(baseUrl, { waitUntil: 'networkidle' });
  await loadLazyAssets(mobile);
  await mobile.screenshot({ path: join(outputDirectory, 'homepage-mobile.png'), fullPage: true });
} finally {
  await browser.close();
}

console.log(`Screenshots saved to ${outputDirectory}`);
