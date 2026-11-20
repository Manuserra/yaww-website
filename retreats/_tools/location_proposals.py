"""The other two location-page layouts, for the review board.

    python3 retreats/_tools/build_location.py --proposals

Writes proposals/location-template/option-b-<id>.html and
option-c-<id>.html for one location with dates (Oaxaca) and one on the
waitlist (Iceland). Same content and the same parts as the live pages
(build_location.py); only the order and the layout change. Frozen: the
location CSS is inlined, so later edits to css/location.css do not move
them. Option A is the live page itself.
"""
import os
import build_location as B

OUT = os.path.join(B.REPO, "proposals", "location-template")
IDS = ["oaxaca", "iceland"]

# ---------------------------------------------------------------- option B --
# Editorial long-read: each pillar gets a full split, as the homepage
# pillars do, and Good to know becomes a quiet list beside the intro.
CSS_B = """
.pb-split { padding: 0 0 96px; }
.pb-split__row { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 48px; align-items: center; }
.pb-split__fig { grid-column: 1 / span 6; margin: 0; aspect-ratio: 672 / 560; overflow: hidden; background: var(--stone-300); }
.pb-split__fig img { width: 100%; height: 100%; object-fit: cover; display: block; }
.pb-split__copy { grid-column: 8 / span 4; display: flex; flex-direction: column; gap: 12px; }
.pb-split--rev .pb-split__fig { grid-column: 7 / span 6; grid-row: 1; }
.pb-split--rev .pb-split__copy { grid-column: 2 / span 4; grid-row: 1; }
.pb-intro { padding: 112px 0 96px; }
.pb-intro__row { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 48px; }
.pb-intro__copy { grid-column: 1 / span 7; display: flex; flex-direction: column; gap: 16px; }
.pb-intro__copy .loc-h2 { font-weight: 400; font-style: italic; font-size: 40px; line-height: 52px; margin-bottom: 8px; }
.pb-intro__know { grid-column: 9 / span 4; margin: 0; display: flex; flex-direction: column; gap: 20px; }
.pb-intro__know div { border-top: 1px solid var(--stone-400); padding-top: 12px; }
.pb-intro__know dt { font: 600 12px/20px var(--font-body); letter-spacing: .12em; text-transform: uppercase; color: var(--stone-600); margin-bottom: 4px; }
.pb-intro__know dd { margin: 0; font: 15px/24px var(--font-body); color: var(--sea-blue); }
@media (max-width: 767px) {
  .pb-split__row, .pb-intro__row { grid-template-columns: minmax(0, 1fr); gap: 24px; }
  .pb-split__fig, .pb-split__copy, .pb-split--rev .pb-split__fig, .pb-split--rev .pb-split__copy, .pb-intro__copy, .pb-intro__know { grid-column: auto; grid-row: auto; }
  .pb-intro { padding: 56px 0; }
  .pb-intro__copy .loc-h2 { font-size: 26px; line-height: 36px; }
  .pb-split { padding-bottom: 56px; }
}
"""


def option_b(L, c, dates, cat):
    know = "".join(f"<div><dt>{B.t(k)}</dt><dd>{B.missing(v)}</dd></div>" for k, v in c["know"])
    paras = "".join(f'<p class="loc-p">{B.t(p)}</p>' for p in c["intro"])
    intro = f"""
  <section class="pb-intro"><div class="loc-wrap pb-intro__row">
    <div class="pb-intro__copy"><p class="loc-kicker">Why we go</p><h2 class="loc-h2">{B.t(c['intro_title'])}</h2>{paras}</div>
    <dl class="pb-intro__know">{know}</dl>
  </div></section>"""
    splits = ""
    for i, g in enumerate(c["guide"]):
        splits += f"""
  <section class="pb-split{' pb-split--rev' if i % 2 else ''}"><div class="loc-wrap pb-split__row">
    {B.guide_fig(L, c, g, 'pb-split__fig')}
    <div class="pb-split__copy"><p class="loc-kicker">{B.t(g['kicker'])}</p><h2 class="loc-h2">{B.t(g['title'])}</h2><p class="loc-p">{B.t(g['text'])}</p></div>
  </div></section>"""
    band = B.dates_band(L, c, 0) if dates else B.waitlist_band(L, c)
    return B.hero(L, c, dates) + band + intro + splits + B.quote(c) + B.cta(L, c, dates)


# ---------------------------------------------------------------- option C --
# Compact, sales first: the dates sit inside the first screen's reach,
# the guide is a strip of four captioned photos, the practical facts
# share a row with the intro, no quote and no "More places".
CSS_C = """
.pc-glance { padding: 96px 0; }
.pc-glance__row { display: grid; grid-template-columns: repeat(12, minmax(0, 1fr)); gap: 48px; align-items: start; }
.pc-glance__copy { grid-column: 1 / span 6; display: flex; flex-direction: column; gap: 16px; }
.pc-glance__know { grid-column: 8 / span 5; margin: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 32px 32px; background: var(--stone-200); padding: 32px; }
.pc-glance__know dt { font: 600 12px/20px var(--font-body); letter-spacing: .12em; text-transform: uppercase; color: var(--stone-600); margin-bottom: 4px; }
.pc-glance__know dd { margin: 0; font: 14px/22px var(--font-body); color: var(--sea-blue); }
.pc-strip { padding: 0 0 96px; }
.pc-strip__grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 24px; list-style: none; margin: 24px 0 0; padding: 0; }
.pc-strip__fig { margin: 0; aspect-ratio: 1 / 1; overflow: hidden; position: relative; background: var(--stone-300); }
.pc-strip__fig img { width: 100%; height: 100%; object-fit: cover; display: block; }
.pc-strip__cap { margin-top: 12px; font: 700 16px/24px var(--font-heading); color: var(--sea-blue); }
.pc-strip__cap span { display: block; font: 600 12px/20px var(--font-body); letter-spacing: .12em; text-transform: uppercase; color: var(--stone-600); }
@media (max-width: 767px) {
  .pc-glance__row { grid-template-columns: minmax(0, 1fr); gap: 32px; }
  .pc-glance__copy, .pc-glance__know { grid-column: auto; }
  .pc-glance__know { grid-template-columns: minmax(0, 1fr); padding: 24px; }
  .pc-strip__grid { grid-template-columns: 1fr 1fr; gap: 16px; }
}
"""


def option_c(L, c, dates, cat):
    know = "".join(f"<div><dt>{B.t(k)}</dt><dd>{B.missing(v)}</dd></div>" for k, v in c["know"])
    paras = "".join(f'<p class="loc-p">{B.t(p)}</p>' for p in c["intro"])
    glance = f"""
  <section class="pc-glance"><div class="loc-wrap pc-glance__row">
    <div class="pc-glance__copy"><p class="loc-kicker">Why we go</p><h2 class="loc-h2">{B.t(c['intro_title'])}</h2>{paras}</div>
    <dl class="pc-glance__know">{know}</dl>
  </div></section>"""
    items = "".join(
        f'<li>{B.guide_fig(L, c, g, "pc-strip__fig")}<p class="pc-strip__cap"><span>{B.t(g["kicker"])}</span>{B.t(g["title"])}</p></li>'
        for g in c["guide"])
    strip = f"""
  <section class="pc-strip"><div class="loc-wrap">
    <h2 class="loc-h2">{B.t(c['title'])}, the way we know it</h2>
    <ul class="pc-strip__grid">{items}</ul>
  </div></section>"""
    band = B.dates_band(L, c, 0) if dates else B.waitlist_band(L, c)
    return B.hero(L, c, dates) + band + glance + strip + B.cta(L, c, dates)


def build(cat):
    os.makedirs(OUT, exist_ok=True)
    loc_css = open(os.path.join(B.REPO, "css", "location.css"), encoding="utf-8").read()
    locs = {L["id"]: L for L in cat["locations"]}
    for vid, fn, css, label in [("b", option_b, CSS_B, "Option B · Editorial long-read"),
                                ("c", option_c, CSS_C, "Option C · Compact, sales first")]:
        for lid in IDS:
            L, c = locs[lid], B.C[lid]
            dates = B.has_upcoming(cat["retreats"], lid)
            top, tail = B.chrome()
            head = B.head(L, c).replace("<head>", '<head>\n<base href="../../">\n<meta name="robots" content="noindex">', 1)
            head = head.replace(f'<link rel="stylesheet" href="css/location.css?v={B.A}">',
                                "<style>/* frozen copy of css/location.css + this option */\n" + loc_css + css + "</style>")
            banner = (f'<p style="margin:0;padding:10px 24px;background:#283139;color:#F7F2F0;font:600 12px/20px Raleway,sans-serif;'
                      f'letter-spacing:.12em;text-transform:uppercase;text-align:center">Proposal · {label} · not live</p>')
            tail = tail.replace("</body>", B.page_script(L, dates) + "\n"
                                f'<script src="js/lead-modal.js?v={B.A}" defer></script>\n</body>')
            out = head + top.replace("<body>", "<body>\n" + banner, 1) + "<main>\n" + fn(L, c, dates, cat) + "\n</main>" + tail
            path = os.path.join(OUT, f"option-{vid}-{lid}.html")
            open(path, "w", encoding="utf-8").write(out)
            print(os.path.relpath(path, B.REPO))
