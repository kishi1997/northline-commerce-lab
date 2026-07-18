# ADR 0001: wp-env as the canonical local environment

- Status: Accepted
- Date: 2026-07-17

## Context

The portfolio must prove reproducibility, WP-CLI operations, database-backed WooCommerce behavior, and parity with WooCommerce contributor tooling without paid hosting.

## Decision

Use `wp-env` with a local Docker-compatible engine, MySQL, PHP 8.3, pinned WordPress/WooCommerce downloads, and a WP-CLI seed command. WordPress Studio is not part of the MVP.

## Consequences

- Fresh setup and tests are scriptable and reviewable.
- The repository contains no database dump.
- Docker Desktop use must stay within its free licensing boundary; otherwise use another eligible free local engine.
- Initial dependency downloads require network access, but day-to-day state is local.

