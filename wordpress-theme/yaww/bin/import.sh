#!/usr/bin/env bash
# Full content import into the Local site: export the static site's content, then load it.
#   bash wordpress-theme/yaww/bin/import.sh [only=media,locations,people,retreats,journal,site]
# Safe to re-run: posts are matched on _yaww_source_id and media on _yaww_source.
set -euo pipefail
BIN="$(cd "$(dirname "$0")" && pwd)"
python3 "$BIN/export-content.py"
bash "$BIN/wp.sh" eval-file "$BIN/import.php" "$@"
