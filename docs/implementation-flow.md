# Implementation Flow / 実装フロー

## Working rule

The project advances through explicit review gates. Code implementation starts only after the wireframe direction is accepted, and visual styling starts only after the information architecture is stable.

このプロジェクトは、確認ポイントを設けながら段階的に進める。ワイヤーフレームで情報設計を確定してからコード実装へ進み、構造が安定してから完成デザインを適用する。

## 1. Discovery and requirements

- Translate the job requirements into portfolio acceptance criteria.
- Audit the reference store without copying its brand, content, or assets.
- Define the fictional catalog, WooCommerce workflows, legal boundaries, and MVP exclusions.

**Review gate:** requirements, target audience, and success criteria are documented.

## 2. Information architecture and wireframes

- Map the path from home to catalog, product, cart, checkout, and order confirmation.
- Define content priority, editable areas, error states, compliance notices, and mobile actions.
- Review desktop and mobile wireframes before visual styling.

**Review gate:** page structure and purchase flow are accepted.

## 3. Visual design system

- Define color, typography, spacing, radii, borders, focus states, and responsive rules.
- Produce high-fidelity desktop and mobile designs using fictional, license-safe assets.
- Record reusable components and states rather than styling pages independently.

**Review gate:** the visual direction and component system are accepted.

## 4. Reproducible development environment

- Configure `wp-env`, Docker, MySQL, WordPress, WooCommerce, and WP-CLI.
- Add one-command setup and reset scripts.
- Pin the supported WordPress, WooCommerce, PHP, and Node versions.

**Review gate:** a fresh clone can start and seed the site without manual dashboard setup.

## 5. Storefront and content operations

- Implement the block theme, patterns, templates, responsive layout, and demo disclaimer.
- Seed products, variations, categories, stock states, coupons, shipping, pickup, tax samples, and policy pages.
- Document product, image, banner, inventory, order, and refund operations.

**Review gate:** the core customer and administrator workflows work end to end.

## 6. Northline Commerce Rules extension

- Add restricted-product metadata and regional availability controls.
- Display notices in product and cart contexts.
- Validate destinations on both classic and block checkout paths.
- Snapshot the applied rules into WooCommerce orders using HPOS-compatible CRUD APIs.

**Review gate:** allowed destinations succeed, restricted destinations fail safely, and order records remain auditable.

## 7. Playground delivery

- Package the theme, plugin, assets, and seed process as a versioned Blueprint bundle.
- Add storefront and administrator launch links to the public README.
- Confirm that every launch creates the same disposable demo without secrets or real customer data.

**Review gate:** a recruiter can open and operate the demo without installing WordPress.

## 8. Quality, security, and accessibility

- Run WordPress coding standards, PHPUnit, Playwright, axe, Theme Check, Plugin Check, and dependency audits.
- Review sanitization, validation, escaping, nonces, capabilities, personal data, and failure messages.
- Test mobile, tablet, and desktop purchase flows with `WP_DEBUG` enabled.

**Review gate:** all required checks pass and no PHP warnings or deprecated notices remain.

## 9. Portfolio and release

- Finish the bilingual README, case study, architecture decisions, threat model, test evidence, and operations guide.
- Preserve real Issue, branch, PR, CI, and release history through GitHub Flow.
- Publish the reproducible `v1.0.0` Playground bundle and screenshots.

**Completion:** the repository explains the problem, decisions, implementation, verification, and operational ownership—not only the final appearance.

## Phase 2

The independent WordPress.org theme adds Theme Check and directory-review gates. The MVP does not rely on the legacy, unpinned Theme Review Action; its theme remains a project-specific storefront.

After the MVP, extract the reusable visual system into a brand-neutral standalone block theme and prepare a separate WordPress.org submission. This is a follow-up project, not a WordPress child theme and not part of the MVP completion criteria.
