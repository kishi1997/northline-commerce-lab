# Architecture / アーキテクチャ

## Boundaries

- **Theme:** presentation, templates, block styles, patterns, and storefront layout.
- **Northline Commerce Rules plugin:** restricted-product metadata, validation, order snapshots, and integration hooks.
- **Seed command:** deterministic demo content and WooCommerce configuration through public WordPress/WooCommerce APIs.
- **Playground bundle:** disposable reviewer environment assembled from the same theme, plugin, and seed command.

Business rules stay outside the theme so switching themes cannot silently remove checkout validation. Database dumps are excluded because they obscure provenance, are fragile across URLs, and can leak personal data.

## Request flow

```text
Editor saves product
  -> nonce + capability check
  -> sanitize and allow-list region data
  -> product metadata

Customer checks out
  -> WooCommerce customer destination
  -> Northline server-side region validation
  -> allowed: create order through WooCommerce
  -> denied: structured checkout error; no order created
  -> snapshot notice and region rule to each order item
```

## Compatibility policy

The baseline is WordPress 7.0.1, WooCommerce 10.9.4, and PHP 8.3. CI runs the pinned baseline. WooCommerce 11.0 is added as a compatibility lane after its stable release; the baseline is not silently upgraded.

