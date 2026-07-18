# ADR 0002: Commerce rules live in a plugin

- Status: Accepted
- Date: 2026-07-17

## Context

Restricted-product notices and checkout validation must survive a theme change and must not be mistaken for visual-only behavior.

## Decision

Implement metadata, validation, order snapshots, HPOS declaration, and the future verification filter in `Northline Commerce Rules`. Keep presentation and responsive composition in the block theme.

## Consequences

- Disabling/changing the theme does not silently disable server validation.
- Order data uses WooCommerce CRUD APIs and remains compatible with HPOS.
- Phase 2 can extract a WordPress.org theme without prohibited plugin-domain functionality.
- The plugin explicitly states that no identity or legal-compliance verification occurs.

