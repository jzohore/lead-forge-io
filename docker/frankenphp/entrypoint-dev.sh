#!/usr/bin/env bash
set -euo pipefail

# If vendor is empty, run composer install (useful when volume mounted empty)
if [ ! -d "/srv/app/vendor" ] || [ -z "$(ls -A /srv/app/vendor 2>/dev/null || true)" ]; then
  echo "⤵ Vendor empty — running composer install (dev)..."
  composer install --no-interaction --prefer-dist
else
  echo "✅ Vendor already present"
fi

# Optionally toggle Xdebug via env var XDEBUG_MODE (common with PHP8/Xdebug3)
# If XDEBUG_MODE is "off", disable xdebug extension (works if xdebug is installed)
if [ "${XDEBUG_MODE:-off}" = "off" ]; then
  echo "XDEBUG mode is off"
  # Note: with install-php-extensions xdebug, extension file is present — we simple rely on XDEBUG_MODE env in runtime
fi

exec "$@"
