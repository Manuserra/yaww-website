#!/usr/bin/env python3
"""Build the premium/ copy of the site from the official pages.

Local comparison draft. Reads every real page at the repo root (and
journal/), swaps the two-row header for the premium one, links the shared
premium skin plus the page family's skin, and writes the result into
premium/. The official pages are never modified.

premium/ reuses the official assets in place (no copies, no symlinks, so
it also works on GitHub Pages): every asset path in the copied markup gets
one more "../", and premium-paths.js does the same for the paths held in
the data/*.js globals. Re-run after changing an
official page or a skin:   python3 premium/_build.py
"""
import os, re, glob, shutil

HERE = os.path.dirname(os.path.abspath(__file__))
ROOT = os.path.dirname(HERE)
VER = "202611192300"

LINKS = ["images", "css", "js", "data", "retreats", "hero-home.mp4", "hero-lassie.mp4"]  # removed if present

ASSET_RE = re.compile(r'(?<=["\'(=\s,;])((?:\.\./)*)(images|retreats|css|js|data)/')
MEDIA_RE = re.compile(r'(?<=["\'(=\s,;])((?:\.\./)*)(hero-home\.mp4|hero-lassie\.mp4)')
DATA_TAG_RE = re.compile(r'<script src="[^"]*data/[^"]+"></script>')

def relocate(html, prefix):
    """Point asset paths one folder up and fix data paths after load."""
    html = ASSET_RE.sub(lambda m: "../" + m.group(1) + m.group(2) + "/", html)
    html = MEDIA_RE.sub(lambda m: "../" + m.group(1) + m.group(2), html)
    tags = list(DATA_TAG_RE.finditer(html))
    if tags:
        end = tags[-1].end()
        html = html[:end] + f'\n<script src="{prefix}premium-paths.js?v={VER}"></script>' + html[end:]
    return html

# page -> family skin; families whose first block is a full-bleed photo
# get the transparent header (p-over-hero).
def family(name):
    if name.startswith("retreatpage-"): return "retreat"
    if name.startswith("locationpage-"): return "location"
    if name.startswith(("teacher-", "team-")): return "person"
    if name.startswith("journal/"): return "journal-post"
    return {
        "about.html": "about", "contact.html": "contact", "journal.html": "journal",
        "locations.html": "locations", "retreats.html": "retreats",
        "teachers.html": "people", "team.html": "people",
    }.get(name)

OVER_HERO = {"retreat", "location"}

CURRENT = {  # which primary nav item a family lights up
    "retreats": "retreats.html", "retreat": "retreats.html",
    "locations": "locations.html", "location": "locations.html",
    "about": "about.html", "people": "about.html", "person": "about.html",
    "journal": "journal.html", "journal-post": "journal.html",
    "contact": "contact.html",
}

def header_html(prefix, current):
    def a(href, label):
        cur = ' aria-current="page"' if href == current else ''
        return f'<a href="{prefix}{href}"{cur}>{label}</a>'
    return f"""<header class="p-header" id="pHeader">
  <div class="p-header__row">
    <nav class="p-header__nav p-header__nav--left" aria-label="Primary">
      {a("retreats.html", "Retreats")}
      {a("locations.html", "Locations")}
      {a("about.html", "About")}
    </nav>
    <button class="p-menu-btn" type="button" aria-expanded="false" aria-controls="pMenu"><span class="p-menu-btn__bars" aria-hidden="true"></span>Menu</button>
    <a href="{prefix}index.html" class="p-header__logo" aria-label="Yoga Adventures Worldwide — home">
      <svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg>
    </a>
    <nav class="p-header__nav p-header__nav--right" aria-label="Secondary">
      {a("journal.html", "Journal")}
      {a("contact.html", "Contact")}
      <a class="p-header__cta" href="{prefix}retreats.html">Find a retreat</a>
    </nav>
  </div>
</header>
<div class="p-menu" id="pMenu" aria-hidden="true">
  <div class="p-menu__top">
    <svg role="img" aria-hidden="true" width="364" height="41" viewBox="0 0 364 41"><use href="#site-logo-symbol"></use></svg>
    <button class="p-menu-btn" type="button" data-close>Close</button>
  </div>
  <nav class="p-menu__list" aria-label="Menu">
    <a href="{prefix}retreats.html">Retreats</a>
    <a href="{prefix}locations.html">Locations</a>
    <a href="{prefix}about.html">About</a>
    <a href="{prefix}teachers.html">Teachers</a>
    <a href="{prefix}team.html">Team</a>
    <a href="{prefix}journal.html">Journal</a>
    <a href="{prefix}contact.html">Contact</a>
  </nav>
  <div class="p-menu__foot"><span>Since 2008</span><span>Yoga Adventures Worldwide</span></div>
</div>"""

HEADER_RE = re.compile(r'<header class="site-header">.*?</header>.*?<nav class="site-header__nav".*?</nav>', re.S)

def build_page(rel):
    src = os.path.join(ROOT, rel)
    fam = family(rel)
    if not fam:
        return None
    html = open(src, encoding="utf-8").read()
    prefix = "../" * rel.count("/")
    html, n = HEADER_RE.subn(lambda m: header_html(prefix, CURRENT.get(fam, "")), html, count=1)
    if n != 1:
        raise SystemExit(f"header not found in {rel}")
    links = (f'\n<link rel="stylesheet" href="{prefix}premium.css?v={VER}">'
             f'\n<link rel="stylesheet" href="{prefix}skin-{fam}.css?v={VER}">\n')
    html = html.replace("</head>", links + "</head>", 1)
    classes = "p-page p-fam-" + fam + (" p-over-hero" if fam in OVER_HERO else "")
    html, n = re.subn(r"<body([^>]*)>", lambda m: _body(m.group(1), classes), html, count=1)
    html = html.replace("</body>", f'<script src="{prefix}premium.js?v={VER}"></script>\n</body>', 1)
    html = html.replace("<title>", "<title>[Premium] ", 1)
    html = relocate(html, prefix)
    out = os.path.join(HERE, rel)
    os.makedirs(os.path.dirname(out), exist_ok=True)
    open(out, "w", encoding="utf-8").write(html)
    return rel

def _body(attrs, classes):
    if 'class="' in attrs:
        return "<body" + attrs.replace('class="', f'class="{classes} ', 1) + ">"
    return f'<body class="{classes}"{attrs}>'

def main():
    for name in LINKS:
        dst = os.path.join(HERE, name)
        if os.path.islink(dst):
            os.remove(dst)
    pages = sorted(os.path.basename(p) for p in glob.glob(os.path.join(ROOT, "*.html")))
    pages += sorted("journal/" + os.path.basename(p) for p in glob.glob(os.path.join(ROOT, "journal", "*.html")))
    built = [r for r in (build_page(p) for p in pages) if r]
    for fam in sorted({family(p) for p in built}):
        skin = os.path.join(HERE, f"skin-{fam}.css")
        if not os.path.exists(skin):
            open(skin, "w").write(f"/* Premium skin · {fam} family. Loads after premium.css. */\n")
    # The premium home is index-premium.html itself.
    home = open(os.path.join(ROOT, "index-premium.html"), encoding="utf-8").read()
    open(os.path.join(HERE, "index.html"), "w", encoding="utf-8").write(relocate(home, ""))
    print(f"built {len(built)} pages + index.html into premium/")

if __name__ == "__main__":
    main()
