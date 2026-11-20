"""Page chrome every official page shares and no builder should forget.

    apply(html) -> html
        * the skip link, first thing in <body>, pointing at <main id="main">
        * one cache-buster for every css/*.css and js/*.js reference

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

ASSET_VERSION = "202611210600"

REPO = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
SKIP = '<a class="skip-link" href="#main">Skip to content</a>'
DRAFT = re.compile(r"^(index-.+|proto-.*|retreats-full|yaww-home-wireframe|team.*)\.html$")


def apply(doc):
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
