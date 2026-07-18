# Requirements / 要件定義

## Portfolio objective

Demonstrate the practical skills expected from a basic-to-intermediate ecommerce website developer: WordPress/WooCommerce use, HTML/CSS implementation, product and content operations, safe extension development, reproducibility, testing, and clear technical communication.

## Users

- **Reviewer/recruiter:** opens a disposable demo and understands ownership within two minutes.
- **Shopper/tester:** discovers a product, selects a variation, uses cart/coupon/shipping, and completes a no-payment order.
- **Store manager:** updates products, imagery, banners, inventory, coupons, orders, and refunds without editing code.
- **Developer:** rebuilds the environment, verifies rules, and produces a versioned Playground bundle.

## Functional acceptance

- Twelve fictional products cover simple, variable, sale, low-stock, out-of-stock, category, SKU, featured, attribute, and gallery workflows.
- CAD, fictional tax behavior, flat-rate/free shipping/local pickup, coupon, Checkout Block, order status, cancellation, and manual refund workflows are represented.
- Restricted items carry notices and allowed-region metadata.
- Disallowed regions fail server validation; BC/AB/ON succeed for seeded restricted products.
- Purchase-time rules appear in order administration, customer order details, and WooCommerce email templates through public item metadata.
- A fresh environment is created without a committed database or real personal information.

## Non-functional acceptance

- Keyboard and focus behavior, labels, headings, contrast, zoom resilience, responsive layout, and axe checks pass on primary flows.
- `WP_DEBUG` remains free of project warnings/notices during normal tested use.
- Code passes PHP syntax, WPCS/PHPCS, Stylelint, JSON validation, PHPUnit, Playwright, and zero-cost checks.
- No paid service, real transaction, email delivery, identity claim, or production-sales claim exists.

## Out of scope

Real products, legal advice, regulatory certification, identity verification, payment capture, real email, production hosting, custom domain, fulfillment, analytics, and the independent WordPress.org theme submission.

## 日本語要約

採用要件を、WooCommerce店舗運用・独自拡張・再現可能性・テスト・説明責任へ変換した。12商品、配送、クーポン、Checkout Block、注文、返金、在庫更新を扱う一方、実販売・本人確認・法令適合・課金サービスは対象外とする。

