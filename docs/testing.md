# Test Strategy / テスト方針

| Layer | Tool | Evidence |
| --- | --- | --- |
| Syntax and format | PHP `-l`, JSON parser, Stylelint | All project source parses cleanly |
| WordPress standards | PHPCS/WPCS + PHPCompatibilityWP | Theme/plugin pass the configured ruleset |
| Plugin directory checks | Official Plugin Check Action | Security, performance, general, and repository checks annotate PRs |
| Unit policy | PHPUnit | Region normalization and unknown-code rejection |
| Storefront E2E | Playwright | Home, catalog, stock, product notice at desktop/mobile |
| Accessibility | axe-playwright | Primary homepage has no detected violations in both viewports |
| Checkout security | Playwright + Store API | MB is denied in Block and Classic; BC completes a test order |
| Audit trail | Order-received assertion | Notice, allowed regions, and unverified status are snapshotted |
| Cost safety | `check-zero-cost.mjs` | Paid keys/deploy commands and non-standard runners are rejected |
| Packaging | Blueprint build | Theme/plugin ZIPs and storefront/admin JSON are deterministic |

## Manual checks

- Keyboard-only header, catalog, variation, cart, checkout, and error recovery.
- 200% zoom and narrow viewport without clipped controls or hidden errors.
- Color contrast across ink/mineral/copper surfaces.
- Product editor nonce/capability negative test.
- Classic Checkout visual and keyboard behavior; server rejection is automated.
- Order administration and WooCommerce email preview contain the same item metadata.
- No project warning/notice/deprecation in `WP_DEBUG_LOG` after tested flows.

## Current automated result

- PHPUnit: 3 tests / 3 assertions passed.
- Playwright: 12 desktop/mobile tests passed, including Block and Classic server validation.
- PHPCS: 9 source files passed.
- npm audit: 0 vulnerabilities at the recorded verification run.

These numbers are evidence from the local implementation run and must be refreshed before each release; they are not a permanent badge claim.
