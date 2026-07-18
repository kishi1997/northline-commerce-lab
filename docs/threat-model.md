# Threat Model / 脅威モデル

## Protected assets

- Store configuration and product metadata
- Checkout integrity and order audit trail
- Administrator capabilities
- Customer-entered addresses in disposable test orders
- Repository and release integrity

## Main threats and controls

| Threat | Control | Verification |
| --- | --- | --- |
| CSRF changes product rules | Product-specific nonce plus capability check | PHPUnit/admin integration test |
| Unauthorized metadata edits | `edit_product`/`manage_woocommerce` capability enforcement | Negative test |
| Stored XSS in notices | length limits, `sanitize_textarea_field`, context-aware escaping | Unit test and manual payload test |
| Region-rule bypass in UI | server-side validation for classic and Store API checkout | Playwright request/UI tests |
| Rule changes after purchase | immutable snapshot in order-item metadata via CRUD APIs | Order/email assertions |
| Secrets or personal data in Git | ignored env/database/upload files, secret scanning, fictional fixtures | CI and repository review |
| Dependency compromise | lockfiles, Dependabot, minimal plugins, release checksums | dependency audit |

## Out of scope

This demo does not perform identity verification, payment processing, email delivery, real fulfillment, or legal compliance certification. The extension exposes an integration filter for a future identity provider but returns no verified state by default.

