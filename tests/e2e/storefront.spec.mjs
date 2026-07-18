import { expect, test } from '@playwright/test';
import { injectAxe, checkA11y } from 'axe-playwright';

test('homepage communicates demo status and exposes the catalogue', async ({ page }) => {
  await page.goto('/');

  await expect(page.getByText('Technical demo — no real sales')).toBeVisible();
  await expect(page.getByRole('heading', { name: /equipment, clearly considered/i })).toBeVisible();
  await expect(page.getByRole('link', { name: /explore the catalogue/i }).first()).toBeVisible();

  await injectAxe(page);
  await checkA11y(page, undefined, {
    detailedReport: true,
    detailedReportOptions: { html: true },
  });
});

test('catalogue includes seeded products and operational stock states', async ({ page }) => {
  await page.goto('/shop/');

  await expect(page.getByText('NL Core 2.0 Precision Device')).toBeVisible();
  await expect(page.getByText('Pocket Control 04')).toBeVisible();
  await expect(page.getByText(/out of stock/i)).toBeVisible();
});

test('restricted product explains the technical rule boundary', async ({ page }) => {
  await page.goto('/product/nl-core-2-0-precision-device/');

  await expect(page.getByRole('heading', { name: 'NL Core 2.0 Precision Device' })).toBeVisible();
  await expect(page.getByText('Technical restriction demo')).toBeVisible();
  await expect(page.getByText(/no age or identity verification is performed/i)).toBeVisible();
});

async function openRestrictedCheckout(page) {
  await page.goto('/product/nl-core-2-0-precision-device/');
  await page.getByRole('button', { name: 'Add to cart', exact: true }).click();
  await page.goto('/checkout/');
  await expect(page.getByRole('button', { name: 'Place Order' })).toBeVisible();
}

async function fillCheckoutAddress(page, province) {
  await page.locator('#shipping-country').selectOption('CA');
  await page.getByLabel('Email address').fill('checkout-test@example.com');
  await page.getByLabel('First name').fill('Northline');
  await page.getByLabel('Last name').fill('Tester');
  await page.getByLabel('Address', { exact: true }).fill('100 Demo Street');
  await page.getByLabel('City').fill('Vancouver');
  await page.locator('#shipping-state').selectOption(province);
  await page.getByLabel('Postal code').fill('V6B 1A1');
}

test('Checkout Block rejects a restricted destination on the server', async ({ page }) => {
  await openRestrictedCheckout(page);
  await fillCheckoutAddress(page, 'MB');
  await page.waitForTimeout(1_000);
  await page.getByRole('button', { name: 'Place Order' }).click();

  await expect(page.getByText(/cannot be ordered for the selected province or territory/i).first()).toBeVisible();
  await expect(page).toHaveURL(/\/checkout\/?$/);
});

test('Classic Checkout rejects the same restricted destination on the server', async ({ page }) => {
  await page.goto('/product/nl-core-2-0-precision-device/');
  await page.getByRole('button', { name: 'Add to cart', exact: true }).click();
  await page.goto('/classic-checkout-test/');

  await page.locator('#billing_first_name').fill('Northline');
  await page.locator('#billing_last_name').fill('Tester');
  await page.locator('#billing_country').selectOption('CA');
  await page.locator('#billing_address_1').fill('100 Demo Street');
  await page.locator('#billing_city').fill('Winnipeg');
  await page.locator('#billing_state').selectOption('MB');
  await page.locator('#billing_postcode').fill('R3C 0V8');
  await page.locator('#billing_email').fill('checkout-test@example.com');
  await page.locator('#billing_postcode').press('Tab');
  await page.waitForTimeout(1_000);
  await page.locator('#place_order').click();

  await expect(page.getByText(/cannot be ordered for the selected province or territory/i).first()).toBeVisible();
  await expect(page).toHaveURL(/\/classic-checkout-test\/?$/);
});

test('allowed destination completes a test order and displays the rule snapshot', async ({ page }) => {
  await openRestrictedCheckout(page);
  await fillCheckoutAddress(page, 'BC');

  await page.getByRole('radio', { name: /standard demo shipping/i }).check();
  await page.getByRole('radio', { name: /test order/i }).check();
  await page.getByLabel('Postal code').press('Tab');
  await page.waitForTimeout(1_000);

  await page.getByRole('button', { name: 'Place Order' }).click();

  await expect(page).toHaveURL(/order-received/, { timeout: 15_000 });
  await expect(page.getByText(/Purchase notice:/)).toBeVisible();
  await expect(page.getByText(/Allowed regions at purchase:/)).toBeVisible();
  await expect(page.getByText('Not verified — technical demo', { exact: true })).toBeVisible();
});
