"""Build the location pages and the locations grid.

    python3 retreats/_tools/build_location.py            # every location
    python3 retreats/_tools/build_location.py iceland    # just these
    python3 retreats/_tools/build_location.py --proposals

What it writes
    locationpage-<id>.html   one per location in data/locations.js, from
                             the content in location_content.py
    locations.html           only the cards between the GRID markers:
                             photo, name, blurb, link to the page
    data/locations.js        pageUrl of each location, so retreat cards and
                             the retreats listing link to the new pages
    --proposals              proposals/location-template/option-*.html:
                             the same content in the other layouts, frozen
                             (CSS inline, <base> at the site root)

The chrome (header, nav, footer, their scripts) is copied from
locations.html on every build, so a nav change there reaches these pages
on the next run. Whether a page shows its retreats or the waitlist is
decided here from data/retreats.js (upcoming = end date not past); the
cards themselves are drawn in the browser by js/retreat-cards.js, so
dates and spaces stay current between builds. Rebuild when a location
gains its first date or loses its last one.
"""
import glob, html, json, os, re, subprocess, sys, datetime

REPO = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
sys.path.insert(0, os.path.dirname(__file__))
from location_content import LOCATIONS as C  # noqa: E402
import seo  # noqa: E402

V = "202611201900"          # cache-buster for css/location.css and the JS touched here
TODAY = datetime.date(2026, 11, 20)


def load_catalogs():
    js = ("const vm=require('vm'),fs=require('fs');const c={window:{}};vm.createContext(c);"
          "for(const f of ['data/locations.js','data/teachers.js','data/retreats.js'])"
          "vm.runInContext(fs.readFileSync(f,'utf8'),c);const w=c.window;"
          "process.stdout.write(JSON.stringify({locations:w.YAWW_LOCATIONS,retreats:w.YAWW_RETREATS}))")
    out = subprocess.run(["node", "-e", js], cwd=REPO, capture_output=True, text=True, check=True)
    return json.loads(out.stdout)


def t(s):
    """Text for HTML: escaped, straight apostrophes made typographic."""
    return html.escape(s, quote=False).replace("'", "’")


def a(s):
    return html.escape(s, quote=True)


def photo(loc_id, key):
    hits = glob.glob(os.path.join(REPO, "images", "retreat-photos", loc_id, "*-" + key + ".webp"))
    if not hits:
        raise SystemExit(f"{loc_id}: no photo ending in -{key}.webp")
    return os.path.relpath(hits[0], REPO)


def alt(key, place):
    words = key.replace("-", " ")
    return words[0].upper() + words[1:] + ", " + place


def img(loc_id, key, place, cls=""):
    return (f'<img{(" class=" + chr(34) + cls + chr(34)) if cls else ""} src="{photo(loc_id, key)}" '
            f'alt="{a(alt(key, place))}" loading="lazy" decoding="async">')


# ------------------------------------------------------------------- parts --

def hero(L, c, has_dates):
    place = c["title"]
    facts = "".join(f"<div><dt>{t(k)}</dt><dd>{t(v)}</dd></div>" for k, v in c["facts"])
    primary = (f'<a class="btn btn--light" href="#retreats">See the retreats</a>' if has_dates
               else f'<a class="btn btn--light" href="#waitlist">Join the waitlist</a>')
    return f"""
  <section class="retreat-hero retreat-hero--overlay loc-hero" data-location-id="{L['id']}" data-lead-kind="location">
    <div class="retreat-hero__image" role="img" aria-label="{a(alt(c['hero'], place))}" style="background-image: url('{photo(L['id'], c['hero'])}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>

    <div class="retreat-hero__inner">
      <div class="retreat-hero__copy">
        <p class="loc-hero__eyebrow">{t(c['eyebrow'])}</p>
        <h1 class="retreat-hero__title">{t(place)}</h1>
        <p class="retreat-hero__location">{t(c['tagline'])}</p>
        <dl class="loc-hero__facts">{facts}</dl>
      </div>

      <div class="retreat-hero__ctas">
        {primary}
        <a class="btn btn--ghost" href="contact.html#general-inquiry" data-lead-open data-lead-source="hero">Ask us about {t(place)}</a>
      </div>
    </div>
  </section>
"""


def dates_band(L, c, n):
    place = c["title"]
    return f"""
  <section class="loc-dates" id="retreats" aria-labelledby="loc-dates-title">
    <div class="loc-wrap">
      <div class="loc-dates__head">
        <div>
          <p class="loc-kicker">Upcoming dates</p>
          <h2 class="loc-h2" id="loc-dates-title">Retreats in {t(place)}</h2>
          <p class="loc-p loc-dates__count" data-loc-count></p>
        </div>
        <p class="loc-dates__aside"><button class="loc-link" type="button" data-lead-open data-lead-source="dates">Ask us which one suits you</button></p>
      </div>
      <div class="loc-dates__grid" data-loc-grid></div>
    </div>
  </section>
"""


def waitlist_band(L, c):
    place = c["title"]
    prog = L.get("program") or {}
    name = prog.get("name") or f"our retreat in {place}"
    link = prog.get("pageUrl")
    itin = (f' The full itinerary is on the <a class="loc-inline" href="{a(link)}">retreat page</a>.' if link else "")
    return f"""
  <section class="loc-dates loc-dates--waitlist" id="waitlist" aria-labelledby="loc-wait-title">
    <div class="loc-wrap loc-wait">
      <div class="loc-wait__card" data-loc-wait-card></div>
      <div class="loc-wait__panel">
        <div class="loc-wait__form-wrap" data-loc-wait-body>
          <p class="loc-kicker">Join the waitlist</p>
          <h2 class="loc-h2" id="loc-wait-title">Our next dates in {t(place)} go to the waitlist first</h2>
          <p class="loc-p">Leave your name and email and we will write to you as soon as the dates for {t(name)} are set, before they are published anywhere else.{itin}</p>
          <!-- Same fields and validation as the lead form. No endpoint yet:
               YAWW.WAITLIST_ENDPOINT in js/retreat-cards.js. -->
          <form class="lead-form loc-wait__form" data-waitlist-form data-waitlist-success="loc-wait-done" novalidate>
            <div class="lead-form__row">
              <div class="lead-form__field">
                <label class="lead-form__label" for="wl-first">First name</label>
                <input class="lead-form__input" id="wl-first" name="first_name" type="text" autocomplete="given-name" required aria-required="true" aria-describedby="wl-first-err">
                <p class="lead-form__error" id="wl-first-err" data-error-for="first_name"></p>
              </div>
              <div class="lead-form__field">
                <label class="lead-form__label" for="wl-email">Email</label>
                <input class="lead-form__input" id="wl-email" name="email" type="email" autocomplete="email" required aria-required="true" aria-describedby="wl-email-err">
                <p class="lead-form__error" id="wl-email-err" data-error-for="email"></p>
              </div>
            </div>
            <input type="hidden" name="location" value="{a(L['id'])}">
            <div class="loc-wait__actions">
              <button class="btn btn--primary" type="submit">Join the waitlist</button>
              <a class="btn btn--secondary" href="contact.html#general-inquiry" data-lead-open data-lead-source="waitlist">Ask us about {t(place)}</a>
            </div>
            <p class="loc-wait__fine">We only use your details to tell you about these dates.</p>
          </form>
        </div>
        <div class="loc-wait__done" id="loc-wait-done" hidden>
          <svg class="loc-wait__tick" viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="22.5"/><path d="M15 24.5l6 6 12-13"/></svg>
          <h2 class="loc-h2" data-wait-done-title>You are on the list</h2>
          <p class="loc-p" data-wait-done-text>We will write to you as soon as the dates for {t(place)} are set.</p>
        </div>
      </div>
    </div>
  </section>
"""


def intro(L, c):
    paras = "\n".join(f'        <p class="loc-p">{t(p)}</p>' for p in c["intro"])
    return f"""
  <section class="loc-intro">
    <div class="loc-wrap loc-intro__row">
      <figure class="loc-intro__fig">{img(L['id'], c['intro_img'], c['title'])}</figure>
      <div class="loc-intro__copy">
        <p class="loc-kicker">Why we go</p>
        <h2 class="loc-h2">{t(c['intro_title'])}</h2>
{paras}
      </div>
    </div>
  </section>
"""


def guide_fig(L, c, g, cls="loc-guide__fig"):
    if g.get("img"):
        return f'<figure class="{cls}">{img(L["id"], g["img"], c["title"])}</figure>'
    return f'<figure class="{cls}"><div class="loc-ph" role="img" aria-label="Photo to come: {a(g["ph"])}">[photo: {t(g["ph"])}]</div></figure>'


def guide(L, c):
    items = "\n".join(
        f"""        <li class="loc-guide__item">
          {guide_fig(L, c, g)}
          <p class="loc-kicker">{t(g['kicker'])}</p>
          <h3 class="loc-guide__title">{t(g['title'])}</h3>
          <p class="loc-p">{t(g['text'])}</p>
        </li>""" for g in c["guide"])
    return f"""
  <section class="loc-guide" aria-labelledby="loc-guide-title">
    <div class="loc-wrap">
      <div class="loc-guide__head">
        <p class="loc-kicker">Our guide</p>
        <h2 class="loc-h2" id="loc-guide-title">{t(c['title'])}, the way we know it</h2>
      </div>
      <ul class="loc-guide__grid">
{items}
      </ul>
    </div>
  </section>
"""


def missing(s):
    return re.sub(r"\[missing data: ([^\]]+)\]", r'<span class="loc-missing">[missing data: \1]</span>', t(s))


def know(L, c):
    rows = "".join(f"<div><dt>{t(k)}</dt><dd>{missing(v)}</dd></div>" for k, v in c["know"])
    return f"""
  <section class="loc-know" aria-labelledby="loc-know-title">
    <div class="loc-wrap">
      <div class="loc-know__head">
        <h2 class="loc-h2" id="loc-know-title">Good to know</h2>
        <button class="loc-link" type="button" data-lead-open data-lead-source="good-to-know">Ask us anything else</button>
      </div>
      <dl class="loc-know__grid">{rows}</dl>
    </div>
  </section>
"""


def quote(c):
    q, who, where = c["quote"]
    return f"""
  <section class="loc-quote">
    <div class="loc-wrap">
      <figure>
        <blockquote>&ldquo;{t(q)}&rdquo;</blockquote>
        <figcaption>{t(who)} <span>· {t(where)}</span></figcaption>
      </figure>
    </div>
  </section>
"""


CAMINO_ALT = "The cathedral of Santiago de Compostela seen through iron gates"


def grid_alts():
    """Each grid photo's alt text as it stands on locations.html (already
    escaped), so a rebuild keeps what was written for the photo."""
    src = open(os.path.join(REPO, "locations.html"), encoding="utf-8").read()
    alts = dict(re.findall(r'<img src="images/location-([a-z-]+)\.jpg" alt="([^"]*)"', src))
    alts.setdefault("camino-de-santiago", a(CAMINO_ALT))
    return alts


def place_card(L, c, alts):
    href = f"locationpage-{L['id']}.html"
    src = f"images/location-{L['id']}.jpg"
    return f"""      <article class="location">
        <a class="location__link" href="{href}">
          <div class="location__image">
            <img src="{src}" alt="{alts.get(L['id']) or a(c['title'])}" width="672" height="888" loading="lazy">
          </div>
          <h2 class="location__title">{t(c['title'])}</h2>
          <p class="location__caption">{t(c['blurb'])}</p>
        </a>
      </article>"""


def more(L, locs):
    same = [x for x in locs if x["region"] == L["region"] and x["id"] != L["id"]]
    other = [x for x in locs if x["region"] != L["region"]]
    pick = (same + other)[:3]
    alts = grid_alts()
    cards = "\n".join(place_card(x, C[x["id"]], alts).replace("<h2 ", "<h3 ").replace("</h2>", "</h3>") for x in pick)
    return f"""
  <section class="loc-more" aria-labelledby="loc-more-title">
    <div class="loc-wrap">
      <div class="loc-more__head">
        <h2 class="loc-h2" id="loc-more-title">More places we love</h2>
        <a class="loc-link" href="locations.html">All locations</a>
      </div>
      <div class="locations-grid">
{cards}
      </div>
    </div>
  </section>
"""


def cta(L, c, has_dates):
    place = c["title"]
    first = ('<a class="btn btn--primary" href="#retreats">See the retreats</a>' if has_dates
             else '<a class="btn btn--primary" href="#waitlist">Join the waitlist</a>')
    return f"""
  <section class="home-cta">
    <div class="home-cta__bg" aria-hidden="true" style="background-image: linear-gradient(rgba(40, 49, 57, 0.5), rgba(40, 49, 57, 0.5)), url('{photo(L['id'], c['hero'])}');"></div>
    <div class="home-cta__content">
      <h2 class="home-cta__title">Your week in {t(place)} starts here</h2>
      <p class="home-cta__desc">Leave the planning to us. All you need to do is arrive.</p>
      <div class="home-cta__btns">
        {first}
        <a class="btn btn--light" href="contact.html#general-inquiry" data-lead-open data-lead-source="closing">Ask us about {t(place)}</a>
      </div>
    </div>
  </section>
"""


def page_script(L, has_dates):
    if has_dates:
        body = ("var grid=document.querySelector('[data-loc-grid]');"
                "var list=Y.getRetreatsByLocation(id);"
                "if(grid){grid.innerHTML=list.map(function(r){"
                "return Y.renderRetreatCard(r,{currentUrl:location.pathname.split('/').pop()});}).join('');"
                "var band=grid.closest('.loc-dates');if(list.length<4){band.classList.add('loc-dates--few');grid.style.setProperty('--n',list.length);}"
                "var c=document.querySelector('[data-loc-count]');if(c)c.textContent=list.length===1?'One date open':list.length+' dates open';}")
    else:
        body = ("var slot=document.querySelector('[data-loc-wait-card]');var loc=Y.getLocation(id);"
                "if(slot&&loc&&(loc.program||Y.getLastEdition(id))){slot.innerHTML=Y.renderWaitlistCard(loc,{action:false});"
                "var d=slot.querySelector('.card__date');if(d)d.textContent='See the itinerary';}"
                "else if(slot){slot.remove();}"
                "Y.wireWaitlistForm(document.querySelector('[data-waitlist-form]'));")
    return f"<script>(function(){{var Y=window.YAWW,id={json.dumps(L['id'])};if(!Y)return;{body}}})();</script>"


# ------------------------------------------------------------------- chrome --

def chrome():
    src = open(os.path.join(REPO, "locations.html"), encoding="utf-8").read()
    head_end = src.index("</head>")
    body_start = src.index("<body>")
    main_start = src.index("<main>")
    main_end = src.index("</main>") + len("</main>")
    top = src[body_start:main_start]
    tail = src[main_end:]
    # The lead modal pauses smooth scroll while it is open; anchors
    # (#retreats, #waitlist) glide instead of jumping, clear of the sticky nav.
    tail = tail.replace("  var lenis = new Lenis({", "  var lenis = new Lenis({", 1)
    tail = tail.replace("  function raf(time) {",
                        "  window.yawwLenis = lenis;\n"
                        "  document.addEventListener('click', function (e) {\n"
                        "    var a = e.target.closest && e.target.closest('a[href^=\"#\"]');\n"
                        "    if (!a || a.getAttribute('href').length < 2) return;\n"
                        "    var to = document.querySelector(a.getAttribute('href'));\n"
                        "    if (!to) return;\n"
                        "    e.preventDefault();\n"
                        "    lenis.scrollTo(to, { offset: -56 });\n"
                        "  });\n\n  function raf(time) {", 1)
    return top, tail


def head(L, c):
    title = f"{c['title']} yoga retreats · YAWW"
    desc = f"{c['title']} through our eyes: {c['intro_title'][0].lower() + c['intro_title'][1:]}. When we go, how to get there and our retreats in {c['title']}."
    return f"""<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{a(title)}</title>
<meta name="description" content="{a(desc)}">
<link rel="canonical" href="locationpage-{L['id']}.html">
<meta property="og:title" content="{a(title)}">
<meta property="og:description" content="{a(desc)}">
<meta property="og:image" content="{photo(L['id'], c['hero'])}">

<!-- Fonts: Libre Baskerville (headings) + Raleway (body) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Raleway:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/base.css?v=202611192000">
<link rel="stylesheet" href="css/retreat.css?v=202611200635">
<link rel="stylesheet" href="css/lead-modal.css?v=202611200635">
<link rel="stylesheet" href="css/locations.css?v={V}">
<link rel="stylesheet" href="css/location.css?v={V}">
<script src="data/locations.js?v={V}"></script>
<script src="data/teachers.js?v=202611201200"></script>
<script src="data/retreats.js?v=202611191000"></script>
<script src="js/retreat-cards.js?v={V}"></script>
</head>
"""


def has_upcoming(retreats, loc_id):
    return any(r["locationId"] == loc_id and datetime.date.fromisoformat(r["endDate"]) >= TODAY for r in retreats)


def build_page(L, cat):
    c = C[L["id"]]
    dates = has_upcoming(cat["retreats"], L["id"])
    top, tail = chrome()
    main = ("<main>\n" + hero(L, c, dates)
            + (dates_band(L, c, 0) if dates else waitlist_band(L, c))
            + intro(L, c) + guide(L, c) + know(L, c) + quote(c) + more(L, cat["locations"]) + cta(L, c, dates)
            + "\n</main>")
    tail = tail.replace("</body>", page_script(L, dates) + "\n"
                        f'<script src="js/lead-modal.js?v={V}" defer></script>\n</body>')
    out = (head(L, c) + top.replace("<body>", "<body>\n<!-- Built by retreats/_tools/build_location.py from "
           "retreats/_tools/location_content.py and data/*.js. Edit those, not this file. -->", 1) + main + tail)
    path = os.path.join(REPO, f"locationpage-{L['id']}.html")
    out = seo.apply(out, f"locationpage-{L['id']}.html")   # SEO head (seo.py)
    open(path, "w", encoding="utf-8").write(out)
    return dates


# --------------------------------------------------------- locations grid --

GRID_START = "<!-- GRID: built by retreats/_tools/build_location.py -->"
GRID_END = "<!-- /GRID -->"


def build_grid(cat):
    path = os.path.join(REPO, "locations.html")
    src = open(path, encoding="utf-8").read()
    alts = grid_alts()
    locs = sorted(cat["locations"], key=lambda x: C[x["id"]]["title"].lower())
    cards = [place_card(L, C[L["id"]], alts) for L in locs]
    grid = GRID_START + '\n    <div class="locations-grid">\n' + "\n".join(cards) + "\n    </div>\n    " + GRID_END
    if GRID_START in src:
        src = re.sub(re.escape(GRID_START) + r".*?" + re.escape(GRID_END), lambda m: grid, src, flags=re.S)
    else:
        src = re.sub(r'<div class="locations-grid">.*?\n    </div>', lambda m: grid, src, count=1, flags=re.S)
    n = len(locs)
    words = {13: "Thirteen", 14: "Fourteen", 15: "Fifteen", 16: "Sixteen"}
    src = re.sub(r"(<p class=\"locations__lede\">)\w+ destinations", r"\g<1>" + words.get(n, str(n)) + " destinations", src)
    src = re.sub(r'css/locations\.css\?v=\d+', f"css/locations.css?v={V}", src)
    src = seo.apply(src, "locations.html")   # SEO head (seo.py)
    open(path, "w", encoding="utf-8").write(src)


def set_page_urls(cat):
    path = os.path.join(REPO, "data", "locations.js")
    src = open(path, encoding="utf-8").read()
    for L in cat["locations"]:
        block = re.compile(r'(id: "' + re.escape(L["id"]) + r'",.*?pageUrl: )(null|"[^"]*")', re.S)
        src = block.sub(lambda m: m.group(1) + f'"locationpage-{L["id"]}.html"', src, count=1)
    open(path, "w", encoding="utf-8").write(src)


def main(argv):
    cat = load_catalogs()
    ids = [x for x in argv if not x.startswith("--")]
    missing_c = [L["id"] for L in cat["locations"] if L["id"] not in C]
    if missing_c:
        raise SystemExit("no content for: " + ", ".join(missing_c))
    if "--proposals" in argv:
        import location_proposals
        location_proposals.build(cat)
        return
    for L in cat["locations"]:
        if ids and L["id"] not in ids:
            continue
        print(f"locationpage-{L['id']}.html", "retreats" if build_page(L, cat) else "waitlist")
    if not ids:
        build_grid(cat)
        set_page_urls(cat)
        print("locations.html grid, data/locations.js pageUrl")


if __name__ == "__main__":
    main(sys.argv[1:])
