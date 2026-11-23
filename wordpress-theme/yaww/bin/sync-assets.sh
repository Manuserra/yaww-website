#!/usr/bin/env bash
# Copies the static site's css/, js/ and the images they use into assets/, so the theme can be zipped
# on its own. Never writes outside the theme. Safe to re-run; run it again after any css/js change.
set -euo pipefail
THEME="$(cd "$(dirname "$0")/.." && pwd)"
SITE="$(cd "$THEME/../.." && pwd)"
A="$THEME/assets"
mkdir -p "$A/css" "$A/js" "$A/images" "$A/media"

cp "$SITE"/css/*.css "$A/css/"
cp "$SITE"/js/*.js   "$A/js/"

# Images: everything css/ and js/ point at, plus what the theme templates reference directly.
{
  grep -rhoE "images/[A-Za-z0-9_./@ %-]+\.(jpg|jpeg|png|webp|svg|gif)" "$SITE/css" "$SITE/js" || true
  printf '%s\n' images/lead-group-terrace.jpg images/hero-home-poster.jpg
  python3 -c "import json;[print(q['photo']) for q in json.load(open('$THEME/inc/testimonials.json')) if q['photo']]"
  # Template-level images the official static pages use (heroes, tier art, quotes, posters).
  # Content photos (retreat-photos, journal, people, team, retreat-cards, location-*) are NOT copied:
  # bin/import.php puts those in the Media Library under their original file names.
  ls "$SITE"/*.html | grep -vE '/(index-|proto-|yaww-home-wireframe|retreats-full)' | xargs grep -hoE "images/[A-Za-z0-9_./@%-]+\.(jpg|jpeg|png|webp|svg|gif|mp4)" \
    | grep -vE '^images/(retreat-photos|journal|people|team|retreat-cards|teachers)/' || true
} | sort -u | while IFS= read -r f; do
  if [ -f "$SITE/$f" ]; then mkdir -p "$A/$(dirname "$f")"; cp "$SITE/$f" "$A/$f"; else echo "missing: $f" >&2; fi
done

[ -f "$SITE/hero-home.mp4" ] && cp "$SITE/hero-home.mp4" "$A/media/hero-home.mp4"
echo "assets synced into $A"
