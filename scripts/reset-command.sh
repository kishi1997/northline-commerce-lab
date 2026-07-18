#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
WP_ENV_BIN="${PROJECT_ROOT}/node_modules/.bin/wp-env"

if [[ ! -x "${WP_ENV_BIN}" ]]; then
  echo "Missing wp-env. Run: npm install" >&2
  exit 1
fi

cd "${PROJECT_ROOT}"
"${WP_ENV_BIN}" clean all
"${PROJECT_ROOT}/scripts/setup-command.sh"

