#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

if [[ ! -x "./vendor/bin/pint" ]]; then
  echo "Pint not found. Run: composer install"
  exit 1
fi

./vendor/bin/pint "$@"
