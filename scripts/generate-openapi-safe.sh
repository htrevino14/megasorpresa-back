#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

mkdir -p storage/api-docs

# Try l5-swagger first (for compatibility with existing tooling).
if ./vendor/bin/sail artisan l5-swagger:generate >/dev/null 2>&1; then
  if [[ -s storage/api-docs/api-docs.json ]] && grep -q '"paths"' storage/api-docs/api-docs.json; then
    echo "Swagger docs generated with l5-swagger."
    exit 0
  fi
fi

# Fallback to swagger-php scanner, which is stable in this project.
./vendor/bin/sail run ./vendor/bin/openapi --format json app > storage/api-docs/api-docs.json
./vendor/bin/sail run ./vendor/bin/openapi --format yaml app > storage/api-docs/api-docs.yaml
cp storage/api-docs/api-docs.yaml storage/api-docs/api-spec.yaml

echo "Swagger docs generated using openapi fallback."
