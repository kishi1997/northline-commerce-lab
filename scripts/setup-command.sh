#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WP_ENV_BIN="${PROJECT_ROOT}/node_modules/.bin/wp-env"

if [[ ! -x "${WP_ENV_BIN}" ]]; then
  echo "Missing wp-env. Run: npm install" >&2
  exit 1
fi

cd "${PROJECT_ROOT}"
node "${PROJECT_ROOT}/scripts/build-demo-images.mjs"
"${WP_ENV_BIN}" start
if ! "${WP_ENV_BIN}" run cli wp plugin is-active woocommerce; then
  "${WP_ENV_BIN}" run cli wp plugin activate woocommerce
fi
if ! "${WP_ENV_BIN}" run cli wp plugin is-active northline-commerce-rules; then
  "${WP_ENV_BIN}" run cli wp plugin activate northline-commerce-rules
fi
if ! "${WP_ENV_BIN}" run cli wp theme is-active northline-storefront; then
  "${WP_ENV_BIN}" run cli wp theme activate northline-storefront
fi
"${WP_ENV_BIN}" run cli wp northline seed --yes
"${WP_ENV_BIN}" run cli wp rewrite flush --hard

echo "Northline is ready at http://localhost:8888"
