#!/usr/bin/env bash
# WP-CLI against the LocalWP site "localwp.com" (http://localwpcom.local), from any shell.
#   bash wordpress-theme/yaww/bin/wp.sh <wp-cli args>      e.g.  bash .../wp.sh post list --post_type=retreat
# Local must be running with the site started (its MySQL listens on a socket in Local's run dir).
# Override with env: LOCAL_SITE_DIR, LOCAL_SITE_ID, LOCAL_PHP, WP_CLI_PHAR.
set -euo pipefail
SITE="${LOCAL_SITE_DIR:-$HOME/Local Sites/localwpcom}"
SITE_ID="${LOCAL_SITE_ID:-odhy6Z6xc}"
PHP="${LOCAL_PHP:-$HOME/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin/bin/php}"
PHAR="${WP_CLI_PHAR:-/Applications/Local.app/Contents/Resources/extraResources/bin/wp-cli/wp-cli.phar}"
SOCK="$HOME/Library/Application Support/Local/run/$SITE_ID/mysql/mysqld.sock"
[ -S "$SOCK" ] || { echo "wp.sh: MySQL socket not found at $SOCK - start the site in Local first." >&2; exit 1; }
ARGS=(-d memory_limit=1024M -d "mysqli.default_socket=$SOCK" -d "pdo_mysql.default_socket=$SOCK")
# Commands that re-launch PHP (rewrite flush, eval-file --skip..., etc.) inherit the same socket.
export WP_CLI_PHP="$PHP" WP_CLI_PHP_ARGS="-d memory_limit=1024M -d mysqli.default_socket='$SOCK'"
exec "$PHP" "${ARGS[@]}" "$PHAR" --path="$SITE/app/public" "$@"
