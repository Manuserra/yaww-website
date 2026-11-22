# -*- coding: utf-8 -*-
"""Build one retreat page per edition listed in data/retreats.js.

    python3 retreats/_tools/build_page.py            # every listed edition
    python3 retreats/_tools/build_page.py <id> ...   # just these
    python3 retreats/_tools/build_page.py --waitlist [<location> ...]

--waitlist builds the dateless page of a destination between editions,
one per location with a `program` in data/locations.js (or just the ones
named): retreatpage-<location>.html. Same template and same content as an
edition (about, includes, itinerary, stay, gallery, FAQs), from that
program's data.json (WAITLIST_SOURCE says which), but no dates, teacher,
rooms or prices: the hero says "Dates to be announced" and the pricing
block is replaced by the waitlist form, which is where the lead is
captured. Listing cards link here through location.program.pageUrl.

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
import seo  # noqa: E402
import chrome  # noqa: E402
import additional_info as AI  # noqa: E402

REPO = os.path.abspath(os.path.join(os.path.dirname(os.path.abspath(__file__)), "..", ".."))
TPL = os.path.join(REPO, "retreatpage-elcuyo.html")
# css/ and js/ cache-busters: chrome.ASSET_VERSION, applied to the whole page by chrome.apply().
HOSTS_JS = f'<script src="js/hosts.js?v={chrome.ASSET_VERSION}" defer></script>'
CARDS_JS = f'<script src="js/retreat-cards.js?v={chrome.ASSET_VERSION}"></script>'

# Past editions that still have a page but are no longer listed in
# data/retreats.js. Built like any edition so they follow the template;
# update_listing() leaves them out.
ARCHIVE = {
    "iceland-oct-2026": {"id": "iceland-oct-2026", "officialName": "Between Fire & Ice", "locationId": "iceland",
                         "teacherIds": ["jenniferlyn-chiemingo"], "startDate": "2026-10-11", "endDate": "2026-10-19",
                         "status": None, "collection": "signature-collection", "_src": "iceland"},
}

# Which data.json a waitlist page reads: the location's own scrape, or for
# Morocco the November 2026 edition (same retreat, last time it ran).
WAITLIST_SOURCE = {"morocco": "morocco-nov-2026"}

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
                if os.path.exists(op):
                    continue                 # served copy already exported; masters live outside the repo
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
    return {"per person": "per person", "private room": "private room"}.get(u, u)


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


def host_portrait(master, tid):
    """A 312:408 portrait, at 2x, from the old site's teacher photo, for a
    host with no site portrait. Most of those photos are landscape; the
    crop is centred on the face (PC.PORTRAIT_FOCUS) rather than the frame."""
    if not master:
        return None
    from PIL import Image
    src = os.path.join(REPO, master)
    rel = f"images/retreat-photos/teachers/yoga-teacher-{tid}-portrait.webp"
    out = os.path.join(REPO, rel)
    if os.path.exists(out) and os.path.getmtime(out) >= os.path.getmtime(src):
        return rel
    im = Image.open(src).convert("RGB")
    w, h = im.size
    ratio = 312 / 408
    if w / h > ratio:
        cw = round(h * ratio)
        cx = PC.PORTRAIT_FOCUS.get(tid, 0.5) * w
        left = int(min(max(cx - cw / 2, 0), w - cw))
        im = im.crop((left, 0, left + cw, h))
    else:
        ch = round(w / ratio)
        im = im.crop((0, 0, w, ch))          # keep the top: that is where the face is
    if im.width > 624:
        im = im.resize((624, 816), Image.LANCZOS)
    im.save(out, "WEBP", quality=82)
    return rel


def host_html(h, indent="        "):
    pos = f' style="object-position: {esc(h["pos"])}"' if h.get("pos") else ""
    photo = (f'<img class="host__photo" src="{h["photo"]}" alt="{esc(h["name"])}"{pos} loading="lazy">'
             if h.get("photo") else '<div class="host__photo host__photo--empty" aria-hidden="true"></div>')
    meta = ""   # credentials and team lines live in the bio now, not under the name
    bio = f'\n{indent}<p class="host__bio">{esc(h["bio"])}</p>' if h.get("bio") else ""
    link = (f'\n{indent}<a class="host__link" href="{h["href"]}"{h.get("attrs", "")}>{esc(h["link"])} <span aria-hidden="true">&rarr;</span></a>'
            if h.get("href") else "")
    return (f'{indent}{photo}\n{indent}<div class="host">'
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
        f'\n        <button class="tabs__item hosts__tab{" is-active" if i == 0 else ""}" type="button" role="tab" data-host-tab="{h["key"]}" '
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
      <div class="tabs tabs--hug hosts__tabs" role="tablist" aria-label="Your hosts">{tabs}
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


def waitlist_section(location, last_edition):
    """Where the rooms and prices go on a dated page. The form posts through
    YAWW.wireWaitlistForm (js/retreat-cards.js), tagged with the location."""
    when = ""
    start = (last_edition or {}).get("date_start")
    if start and datetime.date.fromisoformat(start) < datetime.date.today():
        y, m, _ = (int(x) for x in start.split("-"))
        when = f" in {MONTHS[m-1]} {y}"
    note = (f"The itinerary, inclusions and stay on this page are from our last retreat here{when}; "
            "the next one may differ, and its dates, teacher and prices come out together.")
    return f'''  <!-- =========================================================
       WAITLIST (in place of Rooms pricing: no dates yet). The lead is
       captured here. TODO(backend): YAWW.WAITLIST_ENDPOINT.
       ========================================================= -->
  <section class="waitlist" id="waitlist" aria-labelledby="waitlist-title">
    <div class="waitlist__inner">
      <h2 class="waitlist__title" id="waitlist-title">Join the waitlist</h2>
      <p class="waitlist__body">We are planning the next retreat in {esc(location["name"])}. Leave your name and we will write to you first, before the dates go public.</p>
      <form class="waitlist__form" data-waitlist-form data-waitlist-success="waitlist-done" novalidate>
        <label class="waitlist__label-sr" for="waitlist-first">First name</label>
        <input class="waitlist__input" id="waitlist-first" name="first_name" type="text" placeholder="First name" autocomplete="given-name" required>
        <label class="waitlist__label-sr" for="waitlist-email">Email address</label>
        <input class="waitlist__input" id="waitlist-email" name="email" type="email" placeholder="Email address" autocomplete="email" required>
        <input type="hidden" name="location" value="{esc(location["id"])}">
        <button class="btn btn--primary waitlist__submit" type="submit">Join the waitlist</button>
        <p class="waitlist__error" data-waitlist-error role="alert" hidden></p>
      </form>
      <div class="waitlist__done" id="waitlist-done" hidden>
        <p class="waitlist__title">You are on the list</p>
        <p class="waitlist__body">We will write to you as soon as the dates are set.</p>
      </div>
      <p class="waitlist__note">{esc(note)}</p>
      <p class="waitlist__links"><a href="contact.html#general-inquiry" data-lead-open data-lead-source="waitlist">Questions? Talk to us</a></p>
    </div>
  </section>'''


# ------------------------------------------------------------------ build --

def build(rid, cat, assets, template, waitlist=False):
    if waitlist:
        # rid is a location id; the "edition" is the program between dates.
        location = next(x for x in cat["locations"] if x["id"] == rid)
        prog = location["program"]
        r = {"id": rid, "officialName": prog["name"], "locationId": rid, "teacherIds": [],
             "collection": prog.get("collection"), "status": None}
        src_dir = WAITLIST_SOURCE.get(rid, rid)
    elif rid in ARCHIVE:
        r = ARCHIVE[rid]
        src_dir = r["_src"]
    else:
        r = next(x for x in cat["retreats"] if x["id"] == rid)
        src_dir = rid
    d = json.load(open(os.path.join(REPO, "retreats", src_dir, "data.json"), encoding="utf-8"))
    loc = r["locationId"]
    location = next((x for x in cat["locations"] if x["id"] == loc), {})
    is_cuyo = loc == "el-cuyo"
    standard = rid in EL_CUYO_STANDARD
    teachers = {t["id"]: t for t in cat["teachers"]}
    team = {t["id"]: t for t in cat["team"]}
    doc = template
    place = d["location_label"]
    name = r["officialName"]
    dates = "Dates to be announced" if waitlist else date_range(r["startDate"], r["endDate"])
    tids = [t for t in r["teacherIds"] if t != "tbd"]
    who = " & ".join(teachers[t]["name"] for t in tids)

    # ---------------------------------------------------------------- head
    if waitlist:
        desc = (f"{name}, a yoga retreat in {place}. The next dates are being planned: "
                f"join the waitlist to hear first.")
    else:
        nights = (datetime.date.fromisoformat(r["endDate"]) - datetime.date.fromisoformat(r["startDate"])).days
        desc = (f"{nights}-night yoga retreat in {place}, {dates}"
                + (f", with {who}." if who else ". Teacher to be announced."))
    doc = swap(doc, "<title>YAWW · Beach Escape Retreat</title>",
               f"<title>YAWW · {esc(name)}</title>\n<meta name=\"description\" content=\"{esc(desc)}\">", "title")
    doc = swap(doc, "<body>\n",
               f"<body>\n<!-- Built by retreats/_tools/build_page.py"
               + (f" --waitlist from data/locations.js (program) and" if waitlist else " from data/retreats.js and")
               + f" retreats/{src_dir}/data.json,\n     on the retreatpage-elcuyo.html template. Edit the data, "
               f"not this file. -->\n", "stamp")

    # ---------------------------------------------------------------- hero
    hero = d.get("hero_image") or {}
    hero_src = assets.web(hero.get("file"), Assets.HERO, loc)
    d["_hero_web"] = hero_src
    hero_alt = hero.get("alt") or f"{name} — {place}"
    doc = swap(doc, '<div class="retreat-hero__image" role="img" aria-label="Beach Escape Retreat — El Cuyo, Mexico"></div>',
               f'<div class="retreat-hero__image" role="img" aria-label="{esc(hero_alt)}"'
               + (f" style=\"background-image: url('{hero_src}'); background-size: cover; background-position: center; background-repeat: no-repeat;\""
                  if hero_src else "") + "></div>", "hero image")
    doc = swap(doc, '<h1 class="retreat-hero__title">Beach Escape Retreat</h1>',
               f'<h1 class="retreat-hero__title">{esc(name)}</h1>', "hero title")
    doc = swap(doc, '<p class="retreat-hero__location">El Cuyo, Mexico</p>',
               f'<p class="retreat-hero__location">{esc(place)}</p>', "hero location")
    # The label over the title: tier and status, worded and coloured as on
    # the retreat cards (YAWW.cardLabelHTML in js/retreat-cards.js).
    tier = {"signature-collection": "Signature", "deep-dives": "Deep Dive"}.get(r.get("collection"))
    st = r.get("status")
    status = ("Waitlist" if waitlist else
              {"sold_out": "Sold out", "filling_fast": "Filling fast", "new": "New retreat",
               "spaces_left": f"{r.get('spacesLeft')} space{'s' if r.get('spacesLeft') != 1 else ''} left"}.get(st))
    parts = []
    if tier:
        parts.append(f'<span class="card__label-tier">{tier}</span>')
    if status:
        if parts:
            parts.append('<span class="card__label-sep" aria-hidden="true"></span>')
        parts.append(f'<span class="card__label-status">{esc(status)}</span>')
    sig = " card__label--signature" if r.get("collection") == "signature-collection" else ""
    label = (f'        <p class="retreat-hero__label card__label{sig}">{"".join(parts)}</p>\n' if parts else "")
    doc = swap(doc, '        <p class="retreat-hero__label card__label card__label--signature"><span class="card__label-tier">Signature</span>'
               '<span class="card__label-sep" aria-hidden="true"></span><span class="card__label-status">Filling fast</span></p>\n',
               label, "hero label")
    doc = swap(doc, '<section class="retreat-hero retreat-hero--overlay">',
               f'<section class="retreat-hero retreat-hero--overlay" data-retreat-id="{esc(rid)}">', "hero id")
    if waitlist:
        doc = swap(doc, '''          <span>September 4-8, 2026</span>
          <span class="retreat-hero__meta-dot" aria-hidden="true">·</span>
          <span>with Renata Centi</span>''', "          <span>Dates to be announced</span>", "hero meta")
        doc = swap(doc, '<a class="btn btn--light" href="#rooms">Book now</a>',
                   '<a class="btn btn--light" href="#waitlist">Join the waitlist</a>', "hero ctas")
    else:
        doc = swap(doc, "<span>September 4-8, 2026</span>", f"<span>{esc(dates)}</span>", "hero date")
        doc = swap(doc, "<span>with Renata Centi</span>",
                   f"<span>with {esc(who)}</span>" if who else "<span>Teacher to be announced</span>", "hero who")

    # --------------------------------------------------------------- about
    if not standard:
        # Drop scraped dividers ("______") — a run of underscores is one
        # unbreakable word and pushes the page wider than a phone.
        paras = [tidy(p) for p in d["about_body"] if re.search(r"[A-Za-z0-9]", tidy(p))]
        if d.get("subtitle") and not waitlist:
            paras.insert(0, d["subtitle"] + ".")
        doc = cut(doc, '<div class="retreat-about__body">', "\n    </div>\n  </section>",
                  "".join(f"\n      <p>{esc(p)}</p>" for p in paras), "about")

    # --------------------------------------------------------------- hosts
    hosts = []
    for i, tid in enumerate(tids):
        t = teachers[tid]
        dt = next((x for x in d["teachers"] if x.get("slug") == tid), {})
        photo = t.get("photo") or host_portrait((dt.get("photo") or {}).get("file"), tid)
        bio = t.get("shortBio") or first_sentences((dt.get("bio") or [""])[0])
        if PC.BIO_ADD.get(tid):
            bio = bio + " " + PC.BIO_ADD[tid]
        tab, role = PC.HOST_ROLE.get(tid, ("Teacher", "Your teacher"))
        hosts.append({"key": "teacher" if i == 0 else tid, "tab": tab,
                      "role": role, "name": t["name"], "photo": photo, "pos": t.get("photoPosition"),
                      "bio": bio, "href": t.get("pageUrl"), "link": f"Meet {t['name'].split()[0]}"})
    if not hosts:
        hosts.append({"key": "teacher", "tab": "Teacher", "role": "Your teacher", "name": "To be announced",
                      "bio": "The teacher for these dates has not been announced yet. Ask us and we’ll let you know as soon as it’s confirmed.",
                      "href": "contact.html#general-inquiry", "link": "Ask about these dates",
                      # Opens the lead pop-up with this retreat attached, like
                      # "I want more info"; the href is the no-script fallback.
                      "attrs": ' data-lead-open data-lead-source="hosts"'})
    note = ""
    fid = PC.FACILITATORS.get(loc)
    if fid:
        f = team[fid]
        hosts.append({"key": "facilitator", "tab": "Facilitator", "role": "Your facilitator", "name": f["name"],
                      "photo": f.get("photo"), "pos": f.get("photoPosition"), "bio": f.get("shortBio"),
                      "href": f.get("pageUrl"), "link": f"Meet {f['name'].split()[0]}"})
        note = (f"PLACEHOLDER facilitator — {f['name']} is the team member whose own bio ties\n"
                f"         her to {location.get('name', loc)}, but who travels on these dates is unconfirmed.")
    m = section(doc, "retreat-teacher")
    if waitlist:
        # Who teaches the next one is not announced; the page says nothing
        # rather than naming last time's teacher.
        doc = doc[:m.start()] + "  <!-- Hosts: announced with the next dates. -->" + doc[m.end():]
    else:
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
            # The label ("Riad in the Marrakech medina") before the name: a
            # property's own name is never shown (PC.SCRUB has the rest).
            vname = a.get("label") or PC.VENUE_NAMES.get(loc) or f"Where you stay in {location.get('name', loc)}"
            short = PC.VENUE_TABS.get(a.get("label") or "", vname)
            venues.append(short)
            gal = [assets.web(g["file"], Assets.SLIDE, loc) for g in usable(a.get("gallery"))]
            hotels.append("      {\n        name: %s,\n        tab: %s,\n        gallery: [%s],\n        text: [\n%s\n        ]\n      }" % (
                jstr(vname), jstr(short), ", ".join(jstr(g) for g in gal),
                ",\n".join("          " + jstr(esc(tidy(p))) for p in a.get("text") or [])))
        doc = cut(doc, "    var HOTELS = [", "\n    ];", "\n" + ",\n".join(hotels), "HOTELS")

    # ---------------------------------------------------------------- rooms
    # One entry per room type, rendered by js/retreat-rooms.js as a stacked
    # list and a room modal. A room the old site shows no photo for borrows
    # the photos of the same room at another rate ("…, shared bed" is the
    # room above it, shared); failing that it gets a visible placeholder,
    # never the hotel's general photos dressed up as the room's.
    def base_name(n):
        return re.sub(r"\s*[,—–-]\s*shared bed\b.*$|\s*[—–-]\s*shared$", "", n, flags=re.I).strip()
    own = {}
    for room in d["rooms"]:
        g = [assets.web(x["file"], Assets.SLIDE, loc) for x in usable(room.get("gallery"))]
        if g:
            own.setdefault(base_name(room["name"]), g)
    tiers = []
    for room in d["rooms"]:
        gal = ([assets.web(x["file"], Assets.SLIDE, loc) for x in usable(room.get("gallery"))]
               or own.get(base_name(room["name"])) or [])
        unit = price_unit(room)
        nm = room["name"]
        occ = ("Shared bed" if re.search(r"shared bed|shared double bed", nm, re.I)
               else "Shared room" if re.match(r"shared", nm, re.I) or unit == "per person" and re.search(r"\bshared\b", nm, re.I)
               else "Private room" if re.search(r"private", nm, re.I) or unit == "private room"
               else None)
        pills = ([occ] if occ else []) + chips(room.get("attrs"))
        tiers.append("    {\n      name: %s,\n      priceUSD: %s,\n      priceUnit: %s,\n      desc: %s,\n      pills: [%s],\n      gallery: [%s]\n    }" % (
            jstr(nm), room["price_usd"] if room.get("price_usd") is not None else "null",
            jstr(unit), jstr(tidy(room.get("description")) or None),
            ", ".join(jstr(c) for c in pills), ", ".join(jstr(g) for g in gal)))
    doc = cut(doc, "  var TIERS = [", "\n  ];", "" if waitlist else "\n" + ",\n".join(tiers), "TIERS")
    doc = swap(doc, "  var ROOM_VENUES = [];", "  var ROOM_VENUES = [%s];" % ", ".join(jstr(v) for v in venues), "venues")

    # -------------------------------------------------------------- urgency
    st = r.get("status")
    urg = {"sold_out": "{ type: 'soldout' }",
           "filling_fast": "{ type: 'scarce', label: 'Filling fast' }",
           "new": "{ type: 'scarce', label: 'New retreat' }",
           "spaces_left": "{ type: 'scarce', label: '%s' }" % (
               f"{r.get('spacesLeft')} space{'s' if r.get('spacesLeft') != 1 else ''} left")}.get(st, "null")
    if waitlist:
        urg = "{ type: 'scarce', label: 'Waitlist' }"   # the same word as its card
    doc = swap(doc, "var URGENCY = { type: 'scarce', label: 'Filling fast' };", f"var URGENCY = {urg};", "urgency")

    # --------------------------------------------------------- how to book
    b = d["booking"]
    priced = [x for x in d["rooms"] if x.get("price_usd") is not None]
    units = {price_unit(x) for x in priced}
    step1 = ("Prices for these dates are coming soon." if not priced
             else "Every price is per person, in USD." if units == {"per person"}
             else "Prices are in USD, per person or for a private room, as marked.")
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
    ROOMS_CTAS = '''          <a class="btn btn--primary" href="contact.html#general-inquiry" data-lead-open data-lead-intent="reserve" data-lead-source="rooms" data-wetravel>Reserve your spot</a>
          <a class="btn btn--secondary" href="contact.html#general-inquiry" data-lead-open data-lead-source="rooms">I want more info</a>'''
    # The template carries these links itself (so the reference page opens
    # the pop-up too); older copies of it had plain buttons.
    if ROOMS_CTAS not in doc:
        doc = swap(doc, '''          <button class="btn btn--primary" type="button">Reserve your spot</button>
          <button class="btn btn--secondary" type="button">I want more info</button>''', ROOMS_CTAS, "rooms ctas")
    doc = swap(doc, '<section class="rooms">', '<section class="rooms" id="rooms">', "rooms id")
    if waitlist:
        m = section(doc, "rooms")
        doc = doc[:m.start()] + waitlist_section(location, d) + doc[m.end():]

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
            unit = x.get("unit") or ""
            if unit and price.lower().endswith(" " + unit.lower()):
                price = price[:-len(unit)].rstrip()          # "250 EUR each way" + unit "each way"
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

    # --------------------------------------------------------------- carbon
    # The band had no link and no programme behind it; its words now close
    # Additional info (see additional_info()). Options: proposals/carbon/.
    m = section(doc, "carbon")
    doc = doc[:m.start()] + "  <!-- Carbon offset: now the last question in Additional info. -->" + doc[m.end():]

    # ------------------------------------------------------------------ faq
    faqs = ""
    for i, f in enumerate(additional_info(rid, loc, d, waitlist), 1):
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
    # Three real reviews per page, chosen in docs/testimonials-by-retreat.md.
    quotes = TESTIMONIALS.get(f"retreatpage-{rid}.html") or []
    doc = cut(doc, "    var TESTIMONIALS = [", "\n    ];", "\n" + ",\n".join(
        "      {\n        quote: %s,\n        name: %s,\n        detail: %s\n      }" % (jstr(q), jstr(n), jstr(dt))
        for q, n, dt in quote_rows(quotes, location.get("name", ""))), "testimonials")

    if waitlist:
        doc = swap(doc, "\n</body>", "\n" + CARDS_JS +
                   "\n<script>YAWW.wireWaitlistForm(document.querySelector('[data-waitlist-form]'));</script>\n</body>",
                   "waitlist js")
    else:
        doc = swap(doc, "\n</body>", "\n" + HOSTS_JS + "\n</body>", "hosts js")
    for pat, rep in PC.SCRUB:
        doc = re.sub(pat, rep, doc)
    return f"retreatpage-{rid}.html", doc, d


# -------------------------------------------------------- additional info --

GENERIC_FAQ = {"Do you accept bitcoin?", "What is the cancellation policy?",
               "What happens if my travel plans change?", "Are flight delays or cancellations covered?",
               "Where can I find the full terms & conditions?"}
# Questions from the old site's own FAQ that the written ones already cover.
FAQ_COVERED = {"Do I have to carry my luggage?", "Can I come on my own?", "What if it rains?"}


def additional_info(rid, loc, d, waitlist):
    """The "Additional info" accordion, in the order a reader decides in:
    what the days and the place are like (retreats/_tools/additional_info.py,
    written per retreat from the page and the old site), the old site's own
    retreat-specific questions, then booking and the old site's policies,
    worded as published. Nothing here is a new policy."""
    out = [{"question": q, "answer": a} for q, a in AI.ADDITIONAL_INFO.get(rid) or AI.ADDITIONAL_INFO.get(loc) or []]
    have = {x["question"] for x in out}
    for f in d["faqs"]:
        if f["question"] not in GENERIC_FAQ and f["question"] not in FAQ_COVERED and f["question"] not in have:
            out.append(f)
    b = d.get("booking") or {}
    book = []
    if b.get("deposit_instructions"):
        book.append(tidy(b["deposit_instructions"]))
    if b.get("balance_note"):
        book.append(tidy(b["balance_note"]))
    if book:
        out.append({"question": "How do I reserve my place?", "answer": book})
    methods = b.get("payment_methods") or []
    if methods:
        names = [m.lower() if m in ("Credit card", "Wire transfer") else m for m in methods]
        out.append({"question": "How can I pay?", "answer": [
            "We accept " + (", ".join(names[:-1]) + " and " + names[-1] if len(names) > 1 else names[0])
            + ". Please ask us for the details of the one you would like to use."]})
    if b.get("roommate_note") and "Can I come on my own?" not in have:
        out.append({"question": "I would like to share a room. How does that work?", "answer": [tidy(b["roommate_note"])]})
    # The policy answers are the company's, the same on every retreat of
    # the old site; a page whose scrape lacks them takes them from one
    # that has them, word for word.
    policy = [f for f in d["faqs"] if f["question"] in GENERIC_FAQ]
    if not policy:
        ref = json.load(open(os.path.join(REPO, "retreats", "argentina-mar-2027", "data.json"), encoding="utf-8"))
        policy = [f for f in ref["faqs"] if f["question"] in GENERIC_FAQ]
    out += [f for f in policy if f["question"] != "Do you accept bitcoin?"]
    # What was the "Offset Your Carbon Footprint" band: the same words, as
    # a question, until there is a programme to link to.
    out.append({"question": "Can I offset the carbon footprint of my trip?", "answer": [
        "Every trip has an environmental impact. If you would like to offset your carbon footprint, "
        "you can contribute to climate projects through trusted organizations.",
        "[missing data: the offsetting organization YAWW recommends, with its link]"]})
    return out


# ---------------------------------------------------------- testimonials --

def load_testimonials():
    """docs/testimonials-by-retreat.md: a '## retreatpage-<id>.html' heading
    per page, then '- "quote" — Name, Source, [Location,] date' lines."""
    out, cur = {}, None
    path = os.path.join(REPO, "docs", "testimonials-by-retreat.md")
    for line in open(path, encoding="utf-8"):
        m = re.match(r"^## (retreatpage-[\w-]+\.html)", line)
        if m:
            cur = m.group(1)
            out[cur] = []
            continue
        m = re.match(r'^- "(.*)" — (.+)$', line.strip())
        if m and cur:
            out[cur].append((m.group(1), m.group(2)))
    return out


def quote_rows(quotes, place):
    """Name, and "Google review · El Cuyo". The reviewer's destination is
    shown only when it is this page's; otherwise just the source, as the
    notes in the .md ask. A retreat teacher reviewing says so."""
    rows = []
    for q, by in quotes:
        parts = [p.strip() for p in re.sub(r"\s*\((?:location not named)[^)]*\)", "", by).split(",")]
        name, source, rest = parts[0], parts[1], parts[2:]
        detail = {"Google": "Google review", "Tripadvisor": "Tripadvisor review"}.get(source, source)
        where = rest[0] if rest and not re.match(r"^(c\.|\w{3} \d{4}|\d{4})", rest[0]) else ""
        if "retreat teacher" in where:
            detail = "Retreat teacher · " + detail
        first = re.split(r"[/(]", where)[0].strip()
        if place and first and first.lower() == place.lower():
            detail += " · " + first
        rows.append((q, name, detail))
    return rows


TESTIMONIALS = load_testimonials()


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


def waitlist_card(lid, hero_web):
    """The listing card's photo for a destination between editions: the
    retreat's own hero, centre-cropped to the card's 312x408 frame, at 1x
    and 2x like every edition's card (images/retreat-cards/). Returns the
    two paths for location.program.image / image2x."""
    from PIL import Image
    if not hero_web:
        return None
    one, two = f"images/retreat-cards/{lid}-waitlist.webp", f"images/retreat-cards/{lid}-waitlist@2x.webp"
    with Image.open(os.path.join(REPO, hero_web)) as im:
        im = im.convert("RGB")
        w, h = im.size
        ratio = 312 / 408
        if w / h > ratio:
            cw = round(h * ratio)
            im = im.crop(((w - cw) // 2, 0, (w - cw) // 2 + cw, h))
        else:
            ch = round(w / ratio)
            im = im.crop((0, (h - ch) // 2, w, (h - ch) // 2 + ch))
        for path, size in ((two, (624, 816)), (one, (312, 408))):
            im.resize(size, Image.LANCZOS).save(os.path.join(REPO, path), "WEBP", quality=82, method=6)
    return one, two


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
    waitlist = bool(argv) and argv[0] == "--waitlist"
    if waitlist:
        argv = argv[1:]
        ids = argv or [l["id"] for l in cat["locations"] if l.get("program")]
    else:
        ids = argv or [r["id"] for r in cat["retreats"]] + list(ARCHIVE)
    template = patch_template_js(open(TPL, encoding="utf-8").read())
    assets = Assets()
    built = []
    heroes = []
    for rid in ids:
        fname, doc, d = build(rid, cat, assets, template, waitlist)
        doc = chrome.apply(doc)       # skip link, <main id>, css/js cache-buster (chrome.py)
        doc = seo.apply(doc, fname)   # title, description, canonical, Open Graph, JSON-LD (seo.py)
        open(os.path.join(REPO, fname), "w", encoding="utf-8").write(doc)
        built.append(fname)
        heroes.append((rid, d.get("_hero_web")))
        print(f"{fname:52} rooms={len(d['rooms'])} days={len(d['itinerary'])} extras={len(d['extras'])}")
    made = assets.export()
    print(f"photos: {len(assets.want)} referenced, {made} exported")
    if waitlist:
        for rid, hero_web in heroes:
            card = waitlist_card(rid, hero_web)
            print(f"card {rid}: {card[0] if card else 'no hero'}")
    if not waitlist:
        update_listing([i for i in ids if i not in ARCHIVE])


if __name__ == "__main__":
    main(sys.argv[1:])
