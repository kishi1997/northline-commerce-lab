# Security Policy

## Scope

Supported code is the current `main` branch and the latest tagged release. The project is a no-sales technical demo and must not receive real customer, identity, payment, or fulfillment data.

## Reporting

Do not open a public Issue for a suspected vulnerability. Use the repository's private GitHub security advisory workflow. Include the affected version, reproduction, impact, and suggested mitigation when available.

## Security guarantees and limits

- Product-editor changes require a WordPress nonce and mapped edit capability.
- Input is allow-listed/sanitized and output is contextually escaped.
- Destination restrictions are checked on the server for Classic and Store API checkout paths.
- Applied rules are snapshotted through WooCommerce order-item CRUD APIs.
- HPOS compatibility is declared.
- The project does not provide legal compliance, identity verification, payment security, email delivery, or production hosting.

No bounty or paid disclosure program is offered.

