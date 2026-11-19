# -*- coding: utf-8 -*-
"""Build one retreat page per edition listed in data/retreats.js.

    python3 retreats/_tools/build_page.py            # every listed edition
    python3 retreats/_tools/build_page.py <id> ...   # just these

Each page is retreatpage-elcuyo.html with its variable regions replaced. The
template is read, never written: its CSS, scripts and layout carry over
untouched, and it stays where it is as the design reference.

Where the content comes from, in order of precedence:

  data/retreats.js      the listing's own record of the edition — name,
                        dates, teachers, status, collection. The page agrees
                        with the card that links to it because both read this.
  data/teachers.js,     names, photos, short bios, page links for the hosts.
  data/team.js
  retreats/<id>/data.json
                        everything scraped from the old site: about copy,
                        includes, itinerary, rooms and prices, extras, FAQs,
                        booking terms. Cleaned on the way in (see clean_*).
  page_content.py       the editorial layer: Travel Tips in the template's
                        shape, venue names, facilitators.
  the template itself   for El Cuyo. The template IS an El Cuyo page, and
                        its photographs, accommodation copy, gallery and the
                        standard week's itinerary are the hand-finished
                        versions of what data.json holds, so an El Cuyo
                        edition keeps them rather than the raw scrape.

Photographs are exported from the masters under retreats/ (git-ignored) to
images/retreat-photos/<location>/ as WebP at the width each one is shown at
— see Assets — and only when the file is missing or too narrow.
"""
import datetime
import html as htmllib
import json
import os
import re
import subprocess
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import page_content as PC  # noqa: E402

REPO = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", ".."))
TPL = os.path.join(REPO, "retreatpage-elcuyo.html")
HOSTS_JS = '<script src="js/hosts.js?v=202611181717" defer></script>'
CSS_VERSION = "202611190430"   # bump with any change to css/retreat.css

# The El Cuyo editions that run the standard 5-night week the template was
# written for: same itinerary, same includes, same extras.
EL_CUYO_STANDARD = {"el-cuyo-dec-2026-delaney", "el-cuyo-jan-2027-berger", "el-cuyo-feb-2027-delaney"}

MONTHS = ["January", "February", "March", "April", "May", "June", "July",
          "August", "September", "October", "November", "December"]


# ---------------------------------------------------------------- helpers --

def esc(s):
    if s is None:
        return ""
    return htmllib.escape(str(s), quote=True)


def jstr(s):
    """A JS single-quoted string literal (the template's own style)."""
    if s is None:
        return "null"
    return "'" + str(s).replace("\\", "\\\\").replace("'", "\\'").replace("\n", " ") + "'"


def cut(doc, start, end, new, label):
    """Replace what lies between two markers, keeping both markers."""
    i = doc.find(start)
    if i < 0:
        raise SystemExit(f"[{label}] start marker not found: {start[:70]!r}")
    j = doc.find(end, i + len(start))
    if j < 0:
        raise SystemExit(f"[{label}] end marker not found: {end[:70]!r}")
    return doc[:i + len(start)] + new + doc[j:]


def swap(doc, old, new, label):
    if old not in doc:
        raise SystemExit(f"[{label}] text not found: {old[:70]!r}")
    return doc.replace(old, new, 1)


def section(doc, cls):
    """The full <section class="cls">…</section> block."""
    m = re.search(r'  <section class="%s"[^>]*>.*?\n  </section>' % re.escape(cls), doc, re.S)
    if not m:
        raise SystemExit(f"section {cls} not found")
    return m


def date_range(a, b):
    """Same output as YAWW.formatDateRange() in js/retreat-cards.js."""
    y1, m1, d1 = (int(x) for x in a.split("-"))
    y2, m2, d2 = (int(x) for x in b.split("-"))
    if y1 != y2:
        return f"{MONTHS[m1-1]} {d1}, {y1} – {MONTHS[m2-1]} {d2}, {y2}"
    if m1 != m2:
        return f"{MONTHS[m1-1]} {d1} – {MONTHS[m2-1]} {d2}, {y2}"
    return f"{MONTHS[m1-1]} {d1} – {d2}, {y2}"


def load_catalogs():
    """The site's data files are browser scripts; run them in node's vm."""
    js = ("const vm=require('vm'),fs=require('fs');const c={window:{}};vm.createContext(c);"
          "for(const f of ['data/locations.js','data/teachers.js','data/team.js','data/retreats.js'])"
          "vm.runInContext(fs.readFileSync(f,'utf8'),c);"
          "const w=c.window;process.stdout.write(JSON.stringify({locations:w.YAWW_LOCATIONS,"
          "teachers:w.YAWW_TEACHERS,team:w.YAWW_TEAM,retreats:w.YAWW_RETREATS}))")
    out = subprocess.run(["node", "-e", js], cwd=REPO, capture_output=True, text=True, check=True)
    return json.loads(out.stdout)


# ----------------------------------------------------------------- assets --

class Assets:
    """Maps a master under retreats/ to the WebP the page serves, and
    remembers the widest size any page asked of it. Widths are the ones the
    first three pages were exported at, measured from the layout: 2784 hero,
    1280 gallery (the lightbox's ceiling), 1152 sliders and room photos,
    624 portraits."""
    HERO, GALLERY, SLIDE, PORTRAIT, BAND = 2784, 1280, 1152, 624, 1920

    def __init__(self):
        self.want = {}

    def web(self, src, width, loc):
        if not src:
            return None
        if src.startswith("images/"):
            return src                       # already a served file
        base = os.path.splitext(os.path.basename(src))[0] + ".webp"
        m = re.match(r"retreats/_shared/([^/]+)/img/", src)
        folder = m.group(1) if m else loc
        out = f"images/retreat-photos/{folder}/{base}"
        self.want[out] = (src, max(width, self.want.get(out, (src, 0))[1]))
        return out

    def export(self):
        from PIL import Image, ImageOps
        made = 0
        for out, (src, width) in sorted(self.want.items()):
            sp, op = os.path.join(REPO, src), os.path.join(REPO, out)
            if not os.path.exists(sp):
                raise SystemExit(f"master missing: {src}")
            with Image.open(sp) as im:
                im = ImageOps.exif_transpose(im)
                target = min(width, im.width)
                if os.path.exists(op):
                    with Image.open(op) as have:
                        if have.width >= target:
                            continue
                if im.mode not in ("RGB", "RGBA"):
                    im = im.convert("RGBA" if "A" in im.getbands() else "RGB")
                if im.width > target:
                    im = im.resize((target, round(im.height * target / im.width)), Image.LANCZOS)
                os.makedirs(os.path.dirname(op), exist_ok=True)
                im.save(op, "WEBP", quality=82, method=6)
                made += 1
        return made


def usable(images):
    """Drop the handful of photos that only survive at thumbnail size on the
    old server (see _REPORT.md §5), unless nothing else is left."""
    good = [im for im in images or []
            if not (im.get("low_resolution") and (im["low_resolution"].get("width") or 9999) < 800)]
    return good or list(images or [])


# --------------------------------------------------------------- cleaning --

CTA = re.compile(r"^(reserve|book this|secure your space|reserve my space|reserve your space)\b", re.I)
SUBJECT_TO = re.compile(r"^(this|our) itinerary (is|may be) subject", re.I)


def tidy(s):
    s = re.sub(r"\s+", " ", s or "").strip()
    s = re.sub(r"\s*[;,]\s*$", "", s)                  # dangling list punctuation
    s = re.sub(r"\s*[*+]+\s*$", "", s)                 # footnote markers…
    s = re.sub(r"^[*+]+\s*", "", s)                    # …at either end
    s = re.sub(r"(\d)\s*usd\b", r"\1 USD", s, flags=re.I)
    s = s.replace(" : ", ": ").replace("Y&AWW", "YAWW")
    return s


def clean_list(items, join=True):
    """Scraped <li> runs: drop calls to action, glue continuation lines
    ("tips are preferred in Mexican pesos please", "(taxis are …)") and
    "heading :" lines back onto their neighbours. Itinerary bullets can
    legitimately start lower-case ("by 8am …"), so they pass join=False."""
    out = []
    for raw in items or []:
        s = tidy(raw)
        if s.endswith(" :"):
            s = s[:-2] + ":"
        if not s or CTA.match(s):
            continue
        if join and out and (s[0].islower() or s.startswith("(")):
            out[-1] = out[-1] + ("; " if s[0].islower() else " ") + s
            continue
        if out and out[-1].endswith(":"):
            out[-1] = out[-1] + " " + s
            continue
        out.append(s)
    return out


def title_case(t):
    t = tidy(re.sub(r"^[–-]\s*", "", t or ""))
    if t.isupper():
        t = t.capitalize()
        t = re.sub(r"\b(koh yao noi|chiang mai|buenos aires|vla)\b",
                   lambda m: {"vla": "VLA"}.get(m.group(0), m.group(0).title()), t, flags=re.I)
    return t


# A clock time or range at the start of a bullet ("8:15am", "8-9:15am",
# "6:30–7:30pm", "9:30am-9pm", a bare "6:30"). The schedule reads as a
# sequence, so the times go and the line starts at the activity. A plain
# number ("3 lunches") is left alone: it needs am/pm or a colon to match.
_T = r"\d{1,2}(?::\d{2})?\s*(?:[ap]\.?m\.?)?"
LEAD_TIME = re.compile(
    rf"^\s*(?:(?:by|at|from|approx\.?|around|~)\s*)?(?=\d{{1,2}}(?::\d{{2}}|\s*(?:[-–—]\s*\d{{1,2}}(?::\d{{2}})?)?\s*[ap]\.?m\b))"
    rf"{_T}(?:\s*(?:[-–—]|to)\s*{_T})?\s*[-–—:,.]?\s*", re.I)


def strip_time(b):
    rest = LEAD_TIME.sub("", b, count=1)
    if rest == b or not rest:
        return b
    return rest[0].upper() + rest[1:]


def clean_itinerary(d):
    notes = [tidy(n) for n in d.get("itinerary_notes") or []]
    days = []
    for day in d["itinerary"]:
        bullets = []
        for b in map(strip_time, clean_list(day["bullets"], join=False)):
            if SUBJECT_TO.match(b) or len(b) > 220:
                if b not in notes:
                    notes.append(b)
                continue
            bullets.append(b)
        days.append({"label": day["label"], "title": title_case(day.get("title")),
                     "bullets": bullets, "images": day.get("images") or []})
    # the "subject to change" disclaimer reads best last
    notes.sort(key=lambda n: 1 if SUBJECT_TO.match(n) else 0)
    return days, notes


def clean_includes(d, loc):
    inc = clean_list(d["includes"])
    notinc = clean_list(d["not_included"])
    foot = [tidy(x) for x in (d.get("includes_footnote"), d.get("not_included_footnote")) if x]

    if loc == "camino-de-santiago":
        # The old page pairs every heading with a sentence of description;
        # the list keeps the headings, the way every other retreat reads.
        inc = inc[0::2]
    if loc == "portugal":
        keep = []
        for x in inc:
            if x.lower().startswith(("airport transfer schedule", "leaving to lisbon", "any transfers outside")):
                continue
            keep.append(x)
        inc = keep
        foot.append("The group leaves the Costa Vicentina for Lisbon airport at 7am on the last day "
                    "(about 2.5 hours without traffic). Transfers at other times are private rides, "
                    "approximately 250 euro, and not included.")
    if loc == "thailand":
        flight = [x for x in notinc if re.match(r"^(FOR OUR|DECEMBER 6|Thai AirAsia|Make sure you book|Likely you)", x)]
        notinc = [x for x in notinc if x not in flight]
        foot.append("For the December 1 – 10, 2026 retreat, book Thai AirAsia FD 3162 on December 6, "
                    "leaving Chiang Mai (CNX) at 12:40pm and arriving in Phuket (HKT) at 2:50pm. "
                    "Book your flight home out of Phuket or Krabi after 1pm, to allow for the boat "
                    "transfer from the island.")
    if loc == "argentina":
        inc = [x for x in inc if not x.startswith("Expert YAWW facilitator onsite")]   # listed twice
        moved = [x for x in notinc if x.startswith(("We will advise flight times", "Please see the Travel Tips"))]
        notinc = [x for x in notinc if x not in moved]
        foot.append("You buy the round-trip domestic flights yourself, as well as your airport "
                    "transfers on opening and closing day; we advise flight times and details "
                    "about 4 months before the retreat.")

    # Lines that are really notes about transfers, not things excluded.
    for x in list(notinc):
        if x.startswith(("Pricing will depend on how many", "If you need a private ride")):
            notinc.remove(x)
            if foot:
                foot[0] = foot[0].rstrip(".") + ". " + x
            else:
                foot.append(x)
    return inc, notinc, foot


def chips(attrs):
    out = []
    bed, bath, view = (attrs or {}).get("bed"), (attrs or {}).get("bath"), (attrs or {}).get("view")
    if bed:
        out.append(bed if re.search(r"\bbeds?$|\bor\b", bed, re.I) else bed + " bed")
    if bath:
        out.append(bath + " bath")
    if view:
        out.append(view if "view" in view.lower() else view + " view")
    return out


def price_unit(r):
    u = r.get("price_unit")
    return {"per person": "per person", "private room": "per room"}.get(u, u)


# ---------------------------------------------------------------- markup --

SLIDER_ARROWS = (
    '\n              <button class="photo-slider__arrow photo-slider__arrow--prev" aria-label="Previous photo">'
    '\n                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>'
    '\n              </button>'
    '\n              <button class="photo-slider__arrow photo-slider__arrow--next" aria-label="Next photo">'
    '\n                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>'
    '\n              </button>')


def day_slider(images, label):
    """The template's itinerary slider. Arrows are always written: the
    slider script binds to them unconditionally."""
    if not images:
        return ""
    slides = "".join(
        f'\n                <div class="photo-slider__slide"><img src="{src}" alt="{esc(alt)}" loading="lazy"></div>'
        for src, alt in images)
    dots = "".join(
        f'\n                <button class="photo-slider__dot{" is-active" if i == 0 else ""}" aria-label="Photo {i + 1}"></button>'
        for i in range(len(images)))
    return (f'\n\n            <div class="photo-slider retreat-itinerary__slider" role="group" '
            f'aria-roledescription="carousel" aria-label="{esc(label)}">'
            f'\n              <div class="photo-slider__track">{slides}\n              </div>{SLIDER_ARROWS}'
            f'\n              <div class="photo-slider__dots">{dots}\n              </div>'
            f'\n            </div>')


def host_html(h, indent="        "):
    photo = (f'<img class="host__photo" src="{h["photo"]}" alt="{esc(h["name"])}" loading="lazy">'
             if h.get("photo") else '<div class="host__photo host__photo--empty" aria-hidden="true"></div>')
    meta = f'\n{indent}<p class="host__meta">{esc(h["meta"])}</p>' if h.get("meta") else ""
    bio = f'\n{indent}<p class="host__bio">{esc(h["bio"])}</p>' if h.get("bio") else ""
    link = (f'\n{indent}<a class="host__link" href="{h["href"]}">{esc(h["link"])} <span aria-hidden="true">&rarr;</span></a>'
            if h.get("href") else "")
    return (f'{indent}{photo}\n{indent}<div class="host">'
            f'\n{indent}<p class="host__role">{esc(h["role"])}</p>'
            f'\n{indent}<h3 class="host__name">{esc(h["name"])}</h3>{meta}{bio}{link}'
            f'\n{indent}</div>')


def hosts_section(hosts, placeholder_note):
    first = hosts[0]
    if len(hosts) == 1:
        return f'''  <section class="retreat-teacher" aria-labelledby="hosts-title">
    <h2 class="retreat-teacher__title" id="hosts-title">{"Your host" if first["key"] != "teacher" else "Your teacher"}</h2>
    <div class="hosts" data-hosts>
      <div class="hosts__slot" data-host-slot>
{host_html(first)}
      </div>
    </div>
  </section>'''
    tabs = "".join(
        f'\n        <button class="hosts__tab" type="button" role="tab" data-host-tab="{h["key"]}" '
        f'aria-selected="{"true" if i == 0 else "false"}" tabindex="{0 if i == 0 else -1}">{esc(h["tab"])}</button>'
        for i, h in enumerate(hosts))
    tpls = "".join(f'\n      <template data-host="{h["key"]}">\n{host_html(h)}\n      </template>' for h in hosts)
    note = f"\n         {placeholder_note}" if placeholder_note else ""
    return f'''  <section class="retreat-teacher" aria-labelledby="hosts-title">
    <h2 class="retreat-teacher__title" id="hosts-title">Your hosts</h2>
    <!-- One slot, several people. The first is in the slot in the markup, so
         the block reads complete with scripting off; js/hosts.js swaps the
         contents when a tab is pressed.{note} -->
    <div class="hosts" data-hosts>
      <div class="hosts__tabs" role="tablist" aria-label="Your hosts">{tabs}
      </div>
      <div class="hosts__slot" data-host-slot>
{host_html(first)}
      </div>{tpls}
    </div>
  </section>'''


def first_sentences(paragraph, limit=260):
    """A host blurb from a full bio: whole sentences, as many as fit."""
    parts = re.split(r"(?<=[.!?])\s+", (paragraph or "").strip())
    out = ""
    for p in parts:
        if len(out) >= 120 and len(out) + len(p) + 1 > limit:
            break
        out = (out + " " + p).strip()
    return out


# ------------------------------------------------------------------ build --

def build(rid, cat, assets, template):
    r = next(x for x in cat["retreats"] if x["id"] == rid)
    d = json.load(open(os.path.join(REPO, "retreats", rid, "data.json"), encoding="utf-8"))
    loc = r["locationId"]
    location = next((x for x in cat["locations"] if x["id"] == loc), {})
    is_cuyo = loc == "el-cuyo"
    standard = rid in EL_CUYO_STANDARD
    teachers = {t["id"]: t for t in cat["teachers"]}
    team = {t["id"]: t for t in cat["team"]}
    doc = template
    place = d["location_label"]
    name = r["officialName"]
    dates = date_range(r["startDate"], r["endDate"])
    tids = [t for t in r["teacherIds"] if t != "tbd"]
    who = " & ".join(teachers[t]["name"] for t in tids)

    # ---------------------------------------------------------------- head
    nights = (datetime.date.fromisoformat(r["endDate"]) - datetime.date.fromisoformat(r["startDate"])).days
    desc = (f"{nights}-night yoga retreat in {place}, {dates}"
            + (f", with {who}." if who else ". Teacher to be announced."))
    doc = swap(doc, "<title>YAWW · Beach Escape Retreat</title>",
               f"<title>YAWW · {esc(name)}</title>\n<meta name=\"description\" content=\"{esc(desc)}\">", "title")
    # The built pages use rules the template predates (day titles, notes,
    # empty host photo), so they ask for the current stylesheet.
    doc = re.sub(r"css/retreat\.css\?v=\d+", "css/retreat.css?v=" + CSS_VERSION, doc, count=1)
    doc = swap(doc, "<body>\n",
               f"<body>\n<!-- Built by retreats/_tools/build_page.py from data/retreats.js and "
               f"retreats/{rid}/data.json,\n     on the retreatpage-elcuyo.html template. Edit the data, "
               f"not this file. -->\n", "stamp")

    # ---------------------------------------------------------------- hero
    hero = d.get("hero_image") or {}
    hero_src = assets.web(hero.get("file"), Assets.HERO, loc)
    hero_alt = hero.get("alt") or f"{name} — {place}"
    doc = swap(doc, '<div class="retreat-hero__image" role="img" aria-label="Beach Escape Retreat — El Cuyo, Mexico"></div>',
               f'<div class="retreat-hero__image" role="img" aria-label="{esc(hero_alt)}"'
               + (f" style=\"background-image: url('{hero_src}'); background-size: cover; background-position: center; background-repeat: no-repeat;\""
                  if hero_src else "") + "></div>", "hero image")
    doc = swap(doc, '<h1 class="retreat-hero__title">Beach Escape Retreat</h1>',
               f'<h1 class="retreat-hero__title">{esc(name)}</h1>', "hero title")
    doc = swap(doc, '<p class="retreat-hero__location">El Cuyo, Mexico</p>',
               f'<p class="retreat-hero__location">{esc(place)}</p>', "hero location")
    coll = {"signature-collection": "Signature Collection", "deep-dives": "Deep Dives"}.get(r.get("collection"))
    doc = swap(doc, '      <p class="retreat-hero__badge">Signature Collection</p>\n',
               f'      <p class="retreat-hero__badge">{coll}</p>\n' if coll else "", "badge")
    doc = swap(doc, "<span>September 4-8, 2026</span>", f"<span>{esc(dates)}</span>", "hero date")
    doc = swap(doc, "<span>with Renata Centi</span>",
               f"<span>with {esc(who)}</span>" if who else "<span>Teacher to be announced</span>", "hero who")
    doc = swap(doc, '''      <button class="btn btn--primary" type="button">Book now</button>
      <button class="btn btn--secondary" type="button">I want more info</button>''',
               '''      <a class="btn btn--primary" href="#rooms">Book now</a>
      <a class="btn btn--secondary" href="contact.html#general-inquiry">I want more info</a>''', "hero ctas")

    # --------------------------------------------------------------- about
    if not standard:
        # Drop scraped dividers ("______") — a run of underscores is one
        # unbreakable word and pushes the page wider than a phone.
        paras = [tidy(p) for p in d["about_body"] if re.search(r"[A-Za-z0-9]", tidy(p))]
        if d.get("subtitle"):
            paras.insert(0, d["subtitle"] + ".")
        doc = cut(doc, '<div class="retreat-about__body">', "\n    </div>\n  </section>",
                  "".join(f"\n      <p>{esc(p)}</p>" for p in paras), "about")

    # --------------------------------------------------------------- hosts
    hosts = []
    for i, tid in enumerate(tids):
        t = teachers[tid]
        dt = next((x for x in d["teachers"] if x.get("slug") == tid), {})
        photo = t.get("photo") or assets.web((dt.get("photo") or {}).get("file"), Assets.PORTRAIT, "teachers")
        bio = t.get("shortBio") or first_sentences((dt.get("bio") or [""])[0])
        tab, role = PC.HOST_ROLE.get(tid, ("Teacher", "Your teacher"))
        hosts.append({"key": "teacher" if i == 0 else tid, "tab": tab,
                      "role": role, "name": t["name"], "photo": photo, "meta": PC.HOST_META.get(tid),
                      "bio": bio, "href": t.get("pageUrl"), "link": f"Meet {t['name'].split()[0]}"})
    if not hosts:
        hosts.append({"key": "teacher", "tab": "Teacher", "role": "Your teacher", "name": "To be announced",
                      "bio": "The teacher for these dates has not been announced yet. Ask us and we’ll let you know as soon as it’s confirmed.",
                      "href": "contact.html#general-inquiry", "link": "Ask about these dates"})
    note = ""
    fid = PC.FACILITATORS.get(loc)
    if fid:
        f = team[fid]
        hosts.append({"key": "facilitator", "tab": "Facilitator", "role": "Your facilitator", "name": f["name"],
                      "photo": f.get("photo"), "meta": PC.HOST_META.get(fid), "bio": f.get("shortBio"),
                      "href": f.get("pageUrl"), "link": f"Meet {f['name'].split()[0]}"})
        note = (f"PLACEHOLDER facilitator — {f['name']} is the team member whose own bio ties\n"
                f"         her to {location.get('name', loc)}, but who travels on these dates is unconfirmed.")
    m = section(doc, "retreat-teacher")
    doc = doc[:m.start()] + hosts_section(hosts, note) + doc[m.end():]

    # ------------------------------------------------------------ includes
    if standard:
        doc = swap(doc, "<li>Toes-in-the-sand dinner party with salsa music, open bar and dinner</li>",
                   "<li>Toes-in-the-sand dinner party at a local venue, with salsa music, a margarita and dinner</li>",
                   "open bar")
    else:
        inc, notinc, foot = clean_includes(d, loc)
        doc = cut(doc, '<div class="retreat-includes__panel" data-panel="includes" role="tabpanel">\n          <ul class="retreat-includes__list">',
                  "\n          </ul>", "".join(f"\n            <li>{esc(x)}</li>" for x in inc), "includes")
        m = re.search(r'(<div class="retreat-includes__panel is-hidden" data-panel="not-included"[^>]*>\n)(.*?)(\n        </div>)', doc, re.S)
        body = ('          <ul class="retreat-includes__list">'
                + "".join(f"\n            <li>{esc(x)}</li>" for x in notinc) + "\n          </ul>"
                + "".join(f'\n          <p class="retreat-includes__footnote">{esc(x)}</p>' for x in foot))
        doc = doc[:m.start(2)] + body + doc[m.end(2):]
        if not is_cuyo:
            band = usable(d["gallery"])[0] if d["gallery"] else hero
            src = assets.web(band.get("file"), Assets.BAND, loc)
            doc = swap(doc, '<div class="retreat-includes__bg" aria-hidden="true"></div>',
                       f'<div class="retreat-includes__bg" aria-hidden="true" style="background-image: '
                       f'url(\'{src}\'), linear-gradient(155deg, hsl(150 20% 68%), hsl(150 24% 38%));"></div>', "band")

    # ----------------------------------------------------------- itinerary
    if not standard:
        days, notes = clean_itinerary(d)
        out = ""
        for day in days:
            title = (f'\n                <li class="retreat-itinerary__day-title">{esc(day["title"])}</li>'
                     if day["title"] else "")
            pics = [(assets.web(im["file"], Assets.SLIDE, loc), im.get("alt") or f"{day['label']} — {place}")
                    for im in usable(day["images"])]
            out += f'''
        <li class="retreat-itinerary__day">
          <div class="retreat-itinerary__rail">
            <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
            <span class="retreat-itinerary__dot" aria-hidden="true"></span>
            <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
          </div>

          <div class="retreat-itinerary__content">
            <div class="retreat-itinerary__text">
              <div class="retreat-itinerary__head"><span class="retreat-itinerary__label">{esc(day["label"])}</span></div>

              <ul class="retreat-itinerary__bullets">{title}{"".join(f"{chr(10)}                <li>{esc(b)}</li>" for b in day["bullets"])}
              </ul>
            </div>{day_slider(pics, day["label"] + " photos")}
          </div>
        </li>
'''
        doc = cut(doc, '<ol class="retreat-itinerary__list">\n', "\n      </ol>", out, "itinerary")
        if notes:
            doc = swap(doc, "\n      </ol>\n    </div>\n  </section>",
                       "\n      </ol>\n    </div>\n    <div class=\"retreat-itinerary__notes\">"
                       + "".join(f'\n      <p class="retreat-itinerary__note">{esc(n)}</p>' for n in notes)
                       + "\n    </div>\n  </section>", "itinerary notes")

    # ------------------------------------------------------ accommodations
    venues = []
    if is_cuyo:
        venues = ["Utopia Guesthouse & Yoga Studio"]        # the template's HOTELS stand
    else:
        hotels = []
        for a in d["accommodations"]:
            vname = a.get("name") or a.get("label") or PC.VENUE_NAMES.get(loc) or f"Where you stay in {location.get('name', loc)}"
            short = PC.VENUE_TABS.get(a.get("label") or "", vname)
            venues.append(short)
            gal = [assets.web(g["file"], Assets.SLIDE, loc) for g in usable(a.get("gallery"))]
            hotels.append("      {\n        name: %s,\n        tab: %s,\n        gallery: [%s],\n        text: [\n%s\n        ]\n      }" % (
                jstr(vname), jstr(short), ", ".join(jstr(g) for g in gal),
                ",\n".join("          " + jstr(esc(tidy(p))) for p in a.get("text") or [])))
        doc = cut(doc, "    var HOTELS = [", "\n    ];", "\n" + ",\n".join(hotels), "HOTELS")

    # ---------------------------------------------------------------- rooms
    tiers = []
    # A room type the old site shows no photo for takes three of the
    # property's own, starting at a different one per card so a grid of
    # them does not repeat the same picture.
    pool = []
    if d["accommodations"] and d["accommodations"][0].get("gallery"):
        pool = [assets.web(g["file"], Assets.SLIDE, loc) for g in usable(d["accommodations"][0]["gallery"])]
    for ri, room in enumerate(d["rooms"]):
        fallback = (pool[ri % len(pool):] + pool[:ri % len(pool)])[:3] if pool else []
        gal = [assets.web(g["file"], Assets.SLIDE, loc) for g in usable(room.get("gallery"))] or fallback
        rows = []
        for vi, v in enumerate(venues):
            rows.append("        {\n          name: %s,\n          chips: [%s],\n          gallery: [%s]\n        }" % (
                jstr(v), ", ".join(jstr(c) for c in (chips(room.get("attrs")) if vi == 0 else [])),
                ", ".join(jstr(g) for g in (gal if vi == 0 else []))))
        tiers.append("    {\n      name: %s,\n      priceUSD: %s,\n      priceUnit: %s,\n      hotels: [\n%s\n      ]\n    }" % (
            jstr(room["name"]), room["price_usd"] if room.get("price_usd") is not None else "null",
            jstr(price_unit(room)), ",\n".join(rows)))
    doc = cut(doc, "  var TIERS = [", "\n  ];", "\n" + ",\n".join(tiers), "TIERS")

    # -------------------------------------------------------------- urgency
    st = r.get("status")
    urg = {"sold_out": "{ type: 'soldout' }",
           "filling_fast": "{ type: 'scarce', label: 'Filling fast' }",
           "new": "{ type: 'scarce', label: 'New retreat' }",
           "spaces_left": "{ type: 'scarce', label: '%s' }" % (
               f"{r.get('spacesLeft')} space{'s' if r.get('spacesLeft') != 1 else ''} left")}.get(st, "null")
    doc = swap(doc, "var URGENCY = { type: 'scarce', label: 'Filling fast' };", f"var URGENCY = {urg};", "urgency")

    # --------------------------------------------------------- how to book
    b = d["booking"]
    priced = [x for x in d["rooms"] if x.get("price_usd") is not None]
    units = {price_unit(x) for x in priced}
    step1 = ("Prices for these dates are coming soon." if not priced
             else "Every price is per person, in USD." if units == {"per person"}
             else "Prices are in USD, per person or per room as marked.")
    doc = swap(doc, "<span>Every price is per person, in USD.</span>", f"<span>{step1}</span>", "step1")
    dep = b.get("deposit_usd")
    doc = swap(doc, "<span>A $1,000 deposit per person holds your place. It comes off your total &mdash; it is not an extra.</span>",
               f"<span>A ${dep:,} deposit per person holds your place. It comes off your total &mdash; it is not an extra.</span>"
               if dep else "<span>A deposit per person holds your place. It comes off your total &mdash; it is not an extra.</span>",
               "step2")
    days_due = b.get("balance_due_days")
    doc = swap(doc, "<span>The rest is due 90 days before the retreat.</span>",
               f"<span>The rest is due {days_due} days before the retreat.</span>" if days_due
               else "<span>The rest is due before the retreat.</span>", "step3")
    doc = re.sub(r"        <!-- TODO: add the refund policy line here.*?-->\n",
                 '        <p class="rooms__aside-note">All payments are non-refundable and non-transferable, '
                 "so we recommend trip cancellation insurance from the day you pay your deposit.</p>\n",
                 doc, count=1, flags=re.S)
    doc = swap(doc, '''          <button class="btn btn--primary" type="button">Reserve your space</button>
          <button class="btn btn--secondary" type="button">I want more info</button>''',
               '''          <a class="btn btn--primary" href="contact.html#general-inquiry">Reserve your space</a>
          <a class="btn btn--secondary" href="contact.html#general-inquiry">I want more info</a>''', "rooms ctas")
    doc = swap(doc, '<section class="rooms">', '<section class="rooms" id="rooms">', "rooms id")

    # --------------------------------------------------------------- extras
    tpl_extras = {
        "Excursion to Las Coloradas": ("Excursion to Las Coloradas", "Excursion to Las Coloradas to see flamingos and pink lagoons; dip in a secluded beach &amp; lunch at a local fish joint.", ["images/extra1-3.jpg", "images/extra1-1.jpg", "images/extra1-2.jpg"]),
        "Kayaking in Nature": ("Kayaking in Nature", "Excursion to our local nature preserve to kayak amongst the mangroves and lily pads. A true nature bath experience.", ["images/extra2-1.jpg", "images/extra2-2.jpg"]),
        "Yucatan Peninsula day trip": ("Yucatan Peninsula day trip", "Yucatan Peninsula day trip: Ruins of Ek Balam, cenote swim, lunch &amp; colonial city of Valladolid. *Minimum participation of 4 guests to host the excursion.", ["images/extra3-1.jpg", "images/extra3-2.jpg", "images/extra3-3.jpg"]),
        "Additional 75 minute massages": ("Massages", "Additional 75 minute massages.", ["images/extra4-1.jpg", "images/extra4-2.jpg"]),
    }
    extras = []
    order = ["Excursion to Las Coloradas", "Kayaking in Nature", "Yucatan Peninsula day trip",
             "Temazcal Ceremony", "Additional 75 minute massages"]
    src = sorted(d["extras"], key=lambda x: order.index(x["name"]) if x["name"] in order else 99)
    for x in src:
        if x.get("price_usd") is None and not x.get("price_label"):
            continue                       # e.g. "airport transfers": a note, not an extra
        nm, desc_html, gal = tpl_extras.get(x["name"], (x["name"], esc(tidy(x.get("desc"))), []))
        if not is_cuyo:
            gal = []                       # the template's photos are of El Cuyo
        if x.get("price_usd") is not None:
            price = f"{int(x['price_usd'])} USD"
        else:
            price = re.sub(r"(?i)\s*each$", "", tidy(x["price_label"])).replace("euro", "EUR")
        extras.append("      {\n        name: %s,\n        tab: %s,\n        desc: %s,\n        price: %s, unit: %s,\n        gallery: [%s]\n      }" % (
            jstr(nm), jstr(PC.EXTRA_TABS.get(nm, nm)), jstr(desc_html), jstr(price), jstr(x.get("unit") or "each"), ", ".join(jstr(g) for g in gal)))
    if extras:
        doc = cut(doc, "    var EXTRAS = [", "\n    ];", "\n" + ",\n".join(extras), "EXTRAS")
    else:
        m = section(doc, "extras")
        doc = doc[:m.start()] + "  <!-- Optional Extras: none published for this retreat. -->" + doc[m.end():]

    # ---------------------------------------------------------- travel tips
    tips = PC.TRAVEL_TIPS.get(loc)
    if tips and not is_cuyo:
        groups = "\n".join(
            f'      <div class="tips__group">\n        <h3 class="tips__heading">{esc(h)}</h3>\n'
            f'        <ul class="tips__list">' + "".join(f"\n          <li>{esc(i)}</li>" for i in items)
            + "\n        </ul>\n      </div>\n" for h, items in tips)
        doc = cut(doc, '<div class="tips__body">\n', "    </div>\n  </section>", groups, "tips")
    elif not tips:
        m = section(doc, "tips")
        doc = doc[:m.start()] + "  <!-- Travel Tips: none published for this destination. -->" + doc[m.end():]

    # -------------------------------------------------------------- gallery
    if not is_cuyo:
        ratios = ["28/17", "1/1", "28/17", "5/4", "28/17", "1/1", "28/17", "5/4", "1/1", "28/17"]
        pics = usable(d["gallery"])[:10]
        slides = "".join(
            f'\n        <figure class="gallery__slide" style="--r: {ratios[i]}"><img src="{assets.web(g["file"], Assets.GALLERY, loc)}" '
            f'alt="{esc(g.get("alt") or place)}" loading="lazy"></figure>' for i, g in enumerate(pics))
        doc = cut(doc, '<div class="gallery__rail">', "\n      </div>", slides, "gallery")
        doc = swap(doc, 'aria-label="Photos from El Cuyo"', f'aria-label="Photos from {esc(location.get("name", place))}"', "gallery label")
        doc = re.sub(r'<p class="gallery__intro">.*?</p>',
                     f'<p class="gallery__intro">Photographs from past retreats in {esc(place)}.</p>', doc, count=1, flags=re.S)

    # ------------------------------------------------------------------ faq
    faqs = ""
    for i, f in enumerate(d["faqs"], 1):
        ans = ""
        for p in f["answer"]:
            p = esc(tidy(p))
            p = re.sub(r"(https?://[^\s<]+?)(\s*[.,]?)(?=\s|$)",
                       lambda m: f'<a href="{m.group(1)}">{m.group(1).split("//")[1].rstrip("/")}</a>{m.group(2).strip()}', p)
            p = p.replace(" .", ".")
            ans += f"\n            <p>{p}</p>"
        faqs += f'''
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="faq-{i}">
            <span class="faq__q">{esc(tidy(f["question"]))}</span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="faq-{i}" role="region">{ans}
          </div>
        </div>
      </div>'''
    doc = cut(doc, '<div class="faq-list">', "\n    </div>\n  </section>", faqs, "faqs")
    doc = swap(doc, "       Answers are PLACEHOLDER lorem until the real copy arrives.\n",
               "       Answers quoted from the old site's policies for this retreat.\n", "faq comment")

    # --------------------------------------------------------- testimonials
    # The template's own placeholder quotes, kept on every page for now: they
    # are labelled "Placeholder quote, to be replaced" until real guest
    # words arrive. The old site has none to bring over.

    doc = swap(doc, "\n</body>", "\n" + HOSTS_JS + "\n</body>", "hosts js")
    return f"retreatpage-{rid}.html", doc, d


# ----------------------------------------------------------- template js --

def patch_template_js(doc):
    """Small behaviour changes every built page needs and the template does
    not (it only ever showed per-person USD prices with a photo per extra)."""
    # A tab may carry a shorter label than the panel's heading, so long
    # venue and excursion names do not push a tab bar onto two rows.
    doc = swap(doc, "      tab.textContent = hotel.name;", "      tab.textContent = hotel.tab || hotel.name;", "hotel tab")
    doc = swap(doc, "      tab.textContent = x.name;", "      tab.textContent = x.tab || x.name;", "extra tab")
    # A tab bar that scrolls sideways (see .tabs--hug) brings the tab you
    # pressed fully into view, so a half-visible label never stays cut off.
    doc = swap(doc, "          if (t && bar.contains(t)) move(t, !reduce);",
               "          if (t && bar.contains(t)) {\n"
               "            move(t, !reduce);\n"
               "            if (bar.scrollWidth > bar.clientWidth) {\n"
               "              bar.scrollTo({ left: t.offsetLeft - (bar.clientWidth - t.offsetWidth) / 2, behavior: reduce ? 'auto' : 'smooth' });\n"
               "            }\n"
               "          }", "tab scroll")
    doc = swap(doc, """      price.appendChild(el('p', 'rooms__price-amount', tier.priceUSD.toLocaleString('en-US') + ' USD'));
      price.appendChild(el('p', 'rooms__price-unit', 'per person'));""",
               """      /* A price may not be published yet, and is per person or per room. */
      price.appendChild(el('p', 'rooms__price-amount', tier.priceUSD == null
        ? 'Coming soon' : tier.priceUSD.toLocaleString('en-US') + ' USD'));
      if (tier.priceUSD != null && tier.priceUnit) price.appendChild(el('p', 'rooms__price-unit', tier.priceUnit));""",
               "price render")
    doc = swap(doc, """        var chips = [
          attrs.bed && attrs.bed + ' bed',""",
               """        var chips = hotel.chips || [
          attrs.bed && attrs.bed + ' bed',""", "chips")
    doc = swap(doc, """    var prices = (typeof TIERS !== 'undefined' ? TIERS : []).map(function (t) { return t.priceUSD; });
    if (!prices.length) return;
    var from = Math.min.apply(null, prices);""",
               """    var priced = (typeof TIERS !== 'undefined' ? TIERS : []).filter(function (t) { return t.priceUSD != null; });
    if (!priced.length) return;
    var cheapest = priced.reduce(function (a, t) { return t.priceUSD < a.priceUSD ? t : a; });
    var from = cheapest.priceUSD;""", "bar prices")
    doc = swap(doc, "    unit.textContent = ' · per person';",
               "    unit.textContent = cheapest.priceUnit ? ' · ' + cheapest.priceUnit : '';", "bar unit")
    doc = swap(doc, """    var cta = document.createElement('button');
    cta.className = 'btn btn--primary';
    cta.type = 'button';""",
               """    var cta = document.createElement('a');
    cta.className = 'btn btn--primary';
    cta.href = '#rooms';""", "bar cta")
    doc = swap(doc, """        '<div class="extras__media">' +
          '<div class="photo-slider extras__slider" data-lightbox role="group" aria-roledescription="carousel" aria-label="' + x.name + ' photos">' +""",
               """        (!x.gallery.length ? '' :
        '<div class="extras__media">' +
          '<div class="photo-slider extras__slider" data-lightbox role="group" aria-roledescription="carousel" aria-label="' + x.name + ' photos">' +""",
               "extras media open")
    doc = swap(doc, """            '</div>' +
          '</div>' +
        '</div>';
      panelsEl.appendChild(panel);

      tab.addEventListener('click', function () {
        tabsEl.querySelectorAll('.extras__tab')""",
               """            '</div>' +
          '</div>' +
        '</div>');
      panelsEl.appendChild(panel);

      tab.addEventListener('click', function () {
        tabsEl.querySelectorAll('.extras__tab')""", "extras media close")
    return doc


def update_listing(ids):
    """Give every edition its pageUrl in data/retreats.js, so every card on
    the site (home, listing, teacher pages) links to the edition's page."""
    p = os.path.join(REPO, "data", "retreats.js")
    src = open(p, encoding="utf-8").read()
    for rid in ids:
        if f'pageUrl: "retreatpage-{rid}.html"' in src:
            continue
        src = swap(src, f'    slug: "{rid}",\n', f'    slug: "{rid}",\n    pageUrl: "retreatpage-{rid}.html",\n', f"pageUrl {rid}")
    open(p, "w", encoding="utf-8").write(src)


def main(argv):
    cat = load_catalogs()
    ids = argv or [r["id"] for r in cat["retreats"]]
    template = patch_template_js(open(TPL, encoding="utf-8").read())
    assets = Assets()
    built = []
    for rid in ids:
        fname, doc, d = build(rid, cat, assets, template)
        open(os.path.join(REPO, fname), "w", encoding="utf-8").write(doc)
        built.append(fname)
        print(f"{fname:52} rooms={len(d['rooms'])} days={len(d['itinerary'])} extras={len(d['extras'])}")
    made = assets.export()
    print(f"photos: {len(assets.want)} referenced, {made} exported")
    update_listing(ids)


if __name__ == "__main__":
    main(sys.argv[1:])
