"""Page chrome every official page shares and no builder should forget.

    apply(html) -> html
        * the skip link, first thing in <body>, pointing at <main id="main">
        * one cache-buster for every css/*.css and js/*.js reference
        * the site-wide layers loaded last, after type.css (and rooms.css):
          css/cards.css (card hovers), css/nav.css + js/nav.js (menu),
          css/photo-focus.css (per-photo framing), js/attribution.js (form source)

build_page.py and build_location.py call apply() just before seo.apply(),
so a rebuild keeps both. The hand-written pages (home, about, teachers,
facilitators, journal, contact...) are not generated; run

    python3 retreats/_tools/chrome.py

to rewrite every official page in place (idempotent). Draft and variant
files (index-*, proto-*, premium/, proposals/, retreats-full.html,
yaww-home-wireframe.html) and the team-* redirects are left alone.

Bump ASSET_VERSION whenever a file in css/ or js/ changes.
"""
import glob
import os
import re
import sys

ASSET_VERSION = "202611230600"

REPO = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
SKIP = '<a class="skip-link" href="#main">Skip to content</a>'
DRAFT = re.compile(r"^(index-.+|proto-.*|retreats-full|yaww-home-wireframe|team.*)\.html$")


LAYERS_CSS = ["cards", "nav", "photo-focus"]
LAYERS_JS = ["nav", "attribution"]


def _layers(doc):
    """Make sure the shared layers are linked once, after the last of
    type.css / rooms.css, with the same relative prefix (journal/ uses ../)."""
    anchors = list(re.finditer(r'<link rel="stylesheet" href="((?:\.\./)?)css/(?:type|rooms)\.css[^"]*">', doc))
    if not anchors:
        return doc
    last = anchors[-1]
    pre = last.group(1)
    add = ""
    for name in LAYERS_CSS:
        if f"css/{name}.css" not in doc:
            add += f'\n<link rel="stylesheet" href="{pre}css/{name}.css?v={ASSET_VERSION}">'
    for name in LAYERS_JS:
        if f"js/{name}.js" not in doc:
            add += f'\n<script src="{pre}js/{name}.js?v={ASSET_VERSION}" defer></script>'
    return doc[:last.end()] + add + doc[last.end():] if add else doc


def apply(doc):
    doc = _layers(doc)
    doc = re.sub(r"((?<![\w-])(?:css|js)/[\w-]+\.(?:css|js))\?v=\w+", r"\1?v=" + ASSET_VERSION, doc)
    if SKIP not in doc:
        doc = re.sub(r"(<body[^>]*>\n?)", lambda m: m.group(1) + SKIP + "\n", doc, count=1)
    doc = re.sub(r"<main(?![^>]*\bid=)", '<main id="main"', doc, count=1)
    return doc


def official_pages():
    pages = sorted(glob.glob(os.path.join(REPO, "*.html")) + glob.glob(os.path.join(REPO, "journal", "*.html")))
    return [p for p in pages if not DRAFT.match(os.path.basename(p))]


if __name__ == "__main__":
    # retreatpage-elcuyo.html is the template build_page.py reads: kept in step.
    changed = 0
    for p in official_pages():
        src = open(p, encoding="utf-8").read()
        out = apply(src)
        if out != src:
            open(p, "w", encoding="utf-8").write(out)
            changed += 1
    print(f"{changed} pages updated, assets at ?v={ASSET_VERSION}")
