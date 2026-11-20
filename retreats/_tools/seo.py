"""SEO <head> for every official page, in one place.

    python3 retreats/_tools/seo.py            # (re)write the SEO head of every official page,
                                              # noindex the drafts, write sitemap.xml
    python3 retreats/_tools/seo.py --audit    # report H1 counts, title/description lengths, alt text

build_page.py and build_location.py call apply() on the HTML they are about to
write, so a rebuild keeps the SEO head. Running this script directly does the
same thing in place (useful for the pages nothing generates: home, about, the
teacher, facilitator and journal pages). It only ever touches <head>.

What apply() owns, inside <head>: <title>, meta description, meta robots,
canonical, Open Graph, Twitter card and JSON-LD. Anything of those already in
the file is removed first, then one marked block is written after the viewport
meta, so the function is idempotent.

URLs: every canonical, og:url, og:image and sitemap entry uses BASE and the
filenames the static site has today. When the site moves to WordPress the
permalinks change (/retreats/<slug>/, /journal/<slug>/ ...): change BASE and
url_for() and re-run.
"""
import datetime
import glob
import html
import json
import os
import re
import subprocess
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
REPO = os.path.abspath(os.path.join(os.path.dirname(__file__), "..", ".."))
BASE = "https://yogaadventuresworldwide.com/"
SITE = "Yoga Adventures Worldwide"
SUFFIX = " | " + SITE
SHORT = " | YAWW"
MARK_A, MARK_B = "<!-- SEO: written by retreats/_tools/seo.py, edit the tables there -->", "<!-- /SEO -->"

MONTHS = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]

# Pages that stay out of the index (and the sitemap): drafts, variants, the
# retreat-page template, the premium reference copy, the proposal prototypes.
NOINDEX_GLOBS = ["index-*.html", "proto-*.html", "premium/**/*.html", "proposals/**/*.html",
                 "retreats-full.html", "yaww-home-wireframe.html", "team.html", "team-*.html",
                 "retreatpage-elcuyo.html"]


# ------------------------------------------------------------------ catalogs --

_CAT = None


def catalogs():
    global _CAT
    if _CAT is None:
        js = ("const vm=require('vm'),fs=require('fs');const c={window:{}};vm.createContext(c);"
              "for(const f of ['data/locations.js','data/teachers.js','data/team.js','data/retreats.js','data/journal.js'])"
              "vm.runInContext(fs.readFileSync(f,'utf8'),c);const w=c.window;"
              "process.stdout.write(JSON.stringify({locations:w.YAWW_LOCATIONS,teachers:w.YAWW_TEACHERS,"
              "team:w.YAWW_TEAM,retreats:w.YAWW_RETREATS,journal:w.YAWW_JOURNAL}))")
        out = subprocess.run(["node", "-e", js], cwd=REPO, capture_output=True, text=True, check=True)
        _CAT = json.loads(out.stdout)
    return _CAT


def by_id(rows):
    return {r["id"]: r for r in rows}


# ------------------------------------------------------------------- helpers --

def esc(s):
    return html.escape(str(s), quote=True)


def clean(s):
    """Plain text from a data string (entities, tags, runs of spaces)."""
    s = re.sub(r"<[^>]+>", " ", s or "")
    return re.sub(r"\s+", " ", html.unescape(s)).strip()


def url_for(fname):
    if fname == "index.html":
        return BASE
    return BASE + fname


def abs_img(path):
    if not path:
        return None
    if path.startswith("http"):
        return path
    return BASE + re.sub(r"^(\.\./)+", "", path.lstrip("/"))


def title_of(core, short_ok=True):
    """'core | Yoga Adventures Worldwide' when that is 60 characters or fewer,
    else 'core | YAWW'."""
    if len(core) + len(SUFFIX) <= 60:
        return core + SUFFIX
    return core + SHORT


PADS = [" Read it on the Yoga Adventures Worldwide journal.", " From the Yoga Adventures Worldwide journal.",
        " Read it on the YAWW journal.", " On the YAWW journal.", " More on the journal."]


def fit(text, pads=(), lo=140, hi=160):
    """Make text land in lo..hi characters: cut at a sentence end when too
    long, append the first pad that fits when too short. Returns text as is
    when nothing works (the audit lists those)."""
    text = clean(text)
    if len(text) > hi:
        cut = None
        for m in re.finditer(r"[.!?](?=\s|$)", text):
            if m.end() <= hi:
                cut = m.end()
        if cut and cut >= lo:
            return text[:cut]
        return text
    if len(text) < lo:
        for p in pads:
            if lo <= len(text + p) <= hi:
                return text + p
    return text


def date_short(a, b):
    y1, m1, d1 = (int(x) for x in a.split("-"))
    y2, m2, d2 = (int(x) for x in b.split("-"))
    if m1 == m2:
        return f"{MONTHS[m1-1]} {d1}–{d2}, {y1}"
    return f"{MONTHS[m1-1]} {d1}–{MONTHS[m2-1]} {d2}, {y2}"


def date_long(a, b):
    full = ["January", "February", "March", "April", "May", "June", "July", "August", "September",
            "October", "November", "December"]
    y1, m1, d1 = (int(x) for x in a.split("-"))
    y2, m2, d2 = (int(x) for x in b.split("-"))
    if m1 == m2:
        return f"{full[m1-1]} {d1}–{d2}, {y1}"
    return f"{full[m1-1]} {d1} – {full[m2-1]} {d2}, {y2}"


ORG = {"@type": "Organization", "@id": BASE + "#organization", "name": SITE, "url": BASE}


def crumbs(items):
    return {"@context": "https://schema.org", "@type": "BreadcrumbList",
            "itemListElement": [{"@type": "ListItem", "position": i + 1, "name": n, "item": u}
                                for i, (n, u) in enumerate(items)]}


HOME = ("Home", BASE)


# ----------------------------------------------------------------- page data --
# Pages nothing generates. Title core (suffix is added), description, og:image.

STATIC = {
    "index.html": dict(
        title="Yoga Retreats Around the World",
        desc="Small-group yoga retreats since 2008. Hand-picked teachers, thoughtful hotels, local food and real cultural experiences in destinations from Iceland to Oaxaca.",
        image="images/hero-home-poster.jpg"),
    "retreats.html": dict(
        title="Upcoming Yoga Retreats",
        desc="Browse every upcoming Yoga Adventures Worldwide retreat by destination, date and collection, from short Deep Dives to the Signature Collection.",
        image="images/hero-home-poster.jpg",
        crumbs=[HOME, ("Retreats", BASE + "retreats.html")]),
    "locations.html": dict(
        title="Yoga Retreat Destinations",
        desc="{n} destinations we return to year after year, with the hotels, teachers and guides we trust. Pick a place to see when we go and what a week looks like.",
        image="images/hero-home-poster.jpg",
        crumbs=[HOME, ("Locations", BASE + "locations.html")]),
    "about.html": dict(
        title="About Us",
        desc="Since 2008, Christina Thomas and her team have taken small groups inside the places they love, with a hand-picked teacher and yoga woven into every day.",
        image="images/about-hero.jpg",
        crumbs=[HOME, ("About", BASE + "about.html")]),
    "teachers.html": dict(
        title="Our Yoga Teachers",
        desc="Meet the teachers who lead our retreats, from restorative and yoga nidra to strength and alignment, each paired with a place and a group they suit.",
        image="images/about-teachers.jpg",
        crumbs=[HOME, ("Teachers", BASE + "teachers.html")]),
    "facilitators.html": dict(
        title="Our Retreat Facilitators",
        desc="Meet the facilitators who run our retreats on the ground. They look after the details so you can arrive, practice, eat well and rest, wherever we are.",
        image="images/about-how-team.jpg",
        crumbs=[HOME, ("Facilitators", BASE + "facilitators.html")]),
    "journal.html": dict(
        title="Journal",
        desc="Stories, guides and reflections from our retreats and travels: yoga, food, astrology and life on the road, written by the Yoga Adventures Worldwide team.",
        image="images/hero-home-poster.jpg",
        crumbs=[HOME, ("Journal", BASE + "journal.html")]),
    "contact.html": dict(
        title="Contact",
        desc="Questions about a retreat, a private trip for your group, or anything else: write to the Yoga Adventures Worldwide team and a real person will reply.",
        image="images/lead-yoga-stretch.jpg",
        crumbs=[HOME, ("Contact", BASE + "contact.html")]),
    "teach-with-us.html": dict(
        title="Lead a Yoga Retreat With Us",
        desc="Lead a yoga retreat with Yoga Adventures Worldwide. We plan the itinerary, book the hotels, run the logistics and help fill your spots, so you can teach.",
        image="images/lead-yoga-stretch.jpg",
        crumbs=[HOME, ("Teach with us", BASE + "teach-with-us.html")]),
}


def static_page(fname, doc):
    p = STATIC[fname]
    d = dict(title=title_of(p["title"]), desc=p["desc"], image=p["image"], type="website", ld=[])
    if "{n}" in d["desc"]:
        m = re.search(r'locations__lede">(\w+) destinations', doc)
        d["desc"] = d["desc"].replace("{n}", m.group(1) if m else "Fourteen")
    if fname == "index.html":
        d["ld"] = [
            dict(ORG, **{"@context": "https://schema.org",
                         "description": "Small-group yoga retreats around the world, hosted by people who know the place.",
                         "foundingDate": "2008",
                         "founder": {"@type": "Person", "name": "Christina Thomas"}}),
            {"@context": "https://schema.org", "@type": "WebSite", "name": SITE, "url": BASE,
             "publisher": {"@id": BASE + "#organization"}},
        ]
    elif p.get("crumbs"):
        d["ld"] = [crumbs(p["crumbs"])]
    return d


# Descriptions and titles that the automatic rules cannot land in range.
DESC_OVERRIDE = {
    "facilitator-christina-thomas.html": "Founder of YAWW, Christina brings global retreat expertise and a clear, creative vision. She leads with intention, precision and a love of meaningful travel.",
    "facilitator-renata-centi.html": "Renata brings warm presence and steady care to our retreats in El Cuyo and Sardinia. A teacher and therapist, she supports each guest with clarity and ease.",
    "teacher-dawn-thie.html": "Dawn guides students toward a deeper relationship with Self. Her trauma-informed classes, rooted in Sivananda Hatha, are classical with an accessible twist.",
    "journal/namaste-and-mean-it.html": "Being gay has helped me understand the meaning of yoga. A Pride Month reflection on svadhyaya, community and our connection to all beings everywhere.",
    "journal/the-utopian-newsletter-eat-drink-be-merry-issue-august-8-2018.html": "On retreat, food and drink come right after the hotel in our planning. Santorini wines and more from the Eat, Drink & Be Merry issue of The Utopian.",
    "journal/the-utopian-newsletter-gives-global-issue-august-1-2018.html": "Our Gives Global week, August 24 to September 2, 2018: teachers offered donation-based classes and events, with proceeds to women's and children's charities.",
    "journal/the-utopian-newsletter-shopping-issue-august-22-2018.html": "Our favorite (and weirdest) markets on retreat, from the souks of Morocco to the shops of Greece, plus how we pack light to bring it all home.",
    "journal/the-utopian-newsletter-summer-issue-2018.html": "The first Utopian newsletter, celebrating ten years of retreats and travel, with our Cuba Yoga + Art retreat, Express Yourself Morocco and more.",
    "journal/i-include-myself-gay-havana.html": "A look at gay culture in Havana: how Cuba's history of discrimination is changing, and what the author found walking the city on her visits.",
}
TITLE_OVERRIDE = {
    "journal/the-utopian-newsletter-eat-drink-be-merry-issue-august-8-2018.html": "Utopian Newsletter: Eat, Drink & Be Merry Issue" + SHORT,
    "journal/the-utopian-newsletter-interior-design-issue-september-12-2018.html": "Utopian Newsletter: Interior Design Issue" + SHORT,
}
# Iceland's October 2026 edition is the same retreat as retreatpage-iceland.html.
CANONICAL_OVERRIDE = {"retreatpage-iceland-oct-2026.html": "retreatpage-iceland.html"}

# One real sentence per retreat for the description, from its own page.
HOOK = {
    "argentina-mar-2027": "Tango and wine, then lakes and mountains.",
    "camino-de-santiago-apr-2027": "Yoga and reflection on the old pilgrim way across Galicia.",
    "camino-de-santiago-sep-2027": "Yoga and reflection on the old pilgrim way across Galicia.",
    "el-cuyo-dec-2026-delaney": "Open-air yoga in a quiet Yucatán fishing village, right by the sea.",
    "el-cuyo-feb-2027-delaney": "Open-air yoga in a quiet Yucatán fishing village, right by the sea.",
    "el-cuyo-jan-2027-berger": "Open-air yoga in a quiet Yucatán fishing village, right by the sea.",
    "el-cuyo-feb-2027-morin-gray": "A yoga and boudoir photography experience.",
    "el-cuyo-jan-2027-coffey": "A long-weekend mini retreat: daily yoga, beach walks, paddleboarding.",
    "montenegro-may-2027-cordover": "Yoga, hiking and serene water activities in the Balkans.",
    "montenegro-may-2027-erickson": "Yoga, hiking and serene water activities in the Balkans.",
    "morocco-nov-2026": "From lantern-lit Marrakech to the slow Atlantic air of Essaouira.",
    "oaxaca-oct-2027": "Day of the Dead in Oaxaca City: an altar for loved ones, local food and artisans.",
    "portugal-may-2027": "City neighborhoods and morning yoga with strong coffee.",
    "santorini-may-2027": "Soulful yoga, Greek food and wines, beaches and a stunning hike.",
    "santorini-sep-2027-berger": "Soulful yoga, Greek food and wines, beaches and a stunning hike.",
    "santorini-sep-2027-d26": "Soulful yoga, Greek food and wines, scenery, beaches and a stunning hike.",
    "santorini-sep-2027-erickson": "Soulful yoga, Greek food and wines, beaches and a stunning hike.",
    "thailand-dec-2026": "Temples, cooking classes and a monk's hike.",
    # waitlist pages
    "france": "Based on the Left Bank, Saint-Germain.",
    "iceland": "Raw, quiet, shifting landscapes.",
    "iceland-oct-2026": "Raw, quiet, shifting landscapes.",
    "morocco": "Marrakech, then Essaouira's Atlantic air.",
    "sardinia": "A curated glimpse of the island, from the Maddalena Islands.",
    "spain-basque-country": "Atlantic cliffs and soulful yoga.",
    "turkey": "Mosques, galleries, hammams.",
}
WAITLIST_NAME = {
    "france": "The Paris-Champagne Experience", "iceland": "The Iceland Experience",
    "iceland-oct-2026": "Between Fire & Ice", "morocco": "Mesmerized in Morocco",
    "sardinia": "Majestic Sardinia", "spain-basque-country": "Basque Country Yoga & Gastronomy",
    "turkey": "Yoga & Culture Retreat Turkey",
}


# ------------------------------------------------------- teachers & facilitators --

def person_page(fname, kind):
    cat = catalogs()
    slug = re.sub(r"^(teacher|facilitator)-|\.html$", "", fname)
    rows = cat["teachers"] if kind == "teacher" else cat["team"]
    p = by_id(rows)[slug]
    name = p["name"]
    if kind == "teacher":
        label = p.get("role") or "Yoga Teacher"
        listing = ("Teachers", BASE + "teachers.html")
    else:
        label = "Retreat Facilitator"
        listing = ("Facilitators", BASE + "facilitators.html")
    core = f"{name}, {label}"
    title = core + SUFFIX if len(core + SUFFIX) <= 60 else name + SUFFIX
    desc = DESC_OVERRIDE.get(fname) or (fit(p["shortBio"], [" Teaches with YAWW.", " Works with YAWW."]) if p.get("shortBio") else "")
    img = p.get("photo")
    me = {"@context": "https://schema.org", "@type": "Person", "name": name,
          "jobTitle": label, "description": clean(p.get("shortBio", "")),
          "url": url_for(fname), "worksFor": {"@id": BASE + "#organization"}}
    if img:
        me["image"] = abs_img(img)
    if kind == "teacher" and p.get("specialties"):
        me["knowsAbout"] = p["specialties"]
    return dict(title=title, desc=desc, image=img or "images/about-teachers.jpg", type="profile",
                ld=[me, crumbs([HOME, listing, (name, url_for(fname))])])


# ---------------------------------------------------------------------- journal --

def journal_page(fname):
    cat = catalogs()
    slug = re.sub(r"^journal/|\.html$", "", fname)
    post = next(j for j in cat["journal"] if j["slug"] == slug)
    t = clean(post["title"])
    title = TITLE_OVERRIDE.get(fname) or (t + SUFFIX if len(t + SUFFIX) <= 60 else (t + SHORT if len(t + SHORT) <= 60 else t))
    ex = post.get("excerpt")
    if not ex:
        for b in post["blocks"]:
            if b.get("type") == "text":
                ex = " ".join(" ".join(p) for p in b["paras"][:1])
                break
    desc = DESC_OVERRIDE.get(fname) or fit(ex or "", PADS)
    img = (post.get("hero") or {}).get("src")
    ld = {"@context": "https://schema.org", "@type": "BlogPosting", "headline": t,
          "description": desc, "url": url_for(fname), "mainEntityOfPage": url_for(fname),
          "publisher": {"@id": BASE + "#organization"}}
    if post.get("dateISO"):
        ld["datePublished"] = post["dateISO"]
    if img:
        ld["image"] = abs_img(img)
    if post.get("author"):
        ld["author"] = {"@type": "Person", "name": post["author"]["name"]}
    return dict(title=title, desc=desc, image=img or "images/hero-home-poster.jpg", type="article",
                published=post.get("dateISO"),
                ld=[ld, crumbs([HOME, ("Journal", BASE + "journal.html"), (t, url_for(fname))])])


# -------------------------------------------------------------------- locations --

def location_page(fname):
    import location_content as lc
    cat = catalogs()
    lid = re.sub(r"^locationpage-|\.html$", "", fname)
    L = by_id(cat["locations"])[lid]
    c = lc.LOCATIONS[lid]
    name = c["title"]
    pads = [" See dates and how to get there.", " Dates and how to get there.", " See when we go.", " Dates and travel tips."]
    desc = fit(c["blurb"], pads)
    hero = None
    return dict(title=title_of(f"{name} Yoga Retreats"), desc=desc, image=None, type="website",
                hero_from_page=True,
                ld=[{"@context": "https://schema.org", "@type": "TouristDestination", "name": name,
                     "description": clean(c["blurb"]), "url": url_for(fname),
                     "touristType": "Yoga retreat travellers"},
                    crumbs([HOME, ("Locations", BASE + "locations.html"), (name, url_for(fname))])])


# --------------------------------------------------------------------- retreats --

WAITLIST_TITLE = {  # waitlist pages have no dates: title from the retreat's own name
    "france": "Paris & Champagne Yoga Retreat",
    "iceland": "The Iceland Experience",
    "morocco": "Mesmerized in Morocco Yoga Retreat",
    "sardinia": "Majestic Sardinia Yoga Retreat",
    "spain-basque-country": "Basque Country Yoga & Gastronomy Retreat",
    "turkey": "Istanbul & Cappadocia Yoga Retreat",
    "iceland-oct-2026": "Between Fire & Ice Iceland Retreat",
}

_TITLES = None


def retreat_titles():
    """One title per dated edition, unique across the site: the long form
    (destination, month, year) when it is unique and fits with the full
    suffix; the exact days when editions share a month."""
    global _TITLES
    if _TITLES:
        return _TITLES
    cat = catalogs()
    locs = by_id(cat["locations"])
    rows = [r for r in cat["retreats"] if r.get("startDate")]
    base = {}
    for r in rows:
        dest = locs[r["locationId"]]["name"]
        y, m = int(r["startDate"][:4]), int(r["startDate"][5:7])
        base[r["id"]] = f"{dest} Yoga Retreat, {MONTHS[m-1]} {y}"
    counts = {}
    for v in base.values():
        counts[v] = counts.get(v, 0) + 1
    out = {}
    for r in rows:
        b = base[r["id"]]
        if counts[b] == 1 and len(b + SUFFIX) <= 60:
            out[r["id"]] = b + SUFFIX
        elif counts[b] == 1:
            out[r["id"]] = b + SHORT
        else:
            dest = locs[r["locationId"]]["name"]
            out[r["id"]] = f"{dest} Yoga Retreat, {date_short(r['startDate'], r['endDate'])}" + SHORT
    _TITLES = out
    return out


def _data_json(rid):
    alt = {"morocco": "morocco-nov-2026", "iceland-oct-2026": "iceland"}.get(rid, rid)
    p = os.path.join(REPO, "retreats", alt, "data.json")
    return json.load(open(p, encoding="utf-8")) if os.path.exists(p) else {}


def retreat_page(fname, doc):
    cat = catalogs()
    rid = re.sub(r"^retreatpage-|\.html$", "", fname)
    locs = by_id(cat["locations"])
    teachers = by_id(cat["teachers"])
    r = next((x for x in cat["retreats"] if x["id"] == rid), None)
    d = _data_json(rid)
    h1 = clean(re.search(r"<h1[^>]*>(.*?)</h1>", doc, re.S).group(1))
    place = clean(re.search(r'retreat-hero__location">(.*?)</p>', doc, re.S).group(1))
    hero = re.search(r"retreat-hero__image[^>]*url\('([^']+)'\)", doc)
    image = hero.group(1) if hero else None
    nights = d.get("nights")
    if not nights:
        m = re.search(r"(\d+)[ -]night", doc)
        nights = int(m.group(1)) if m else None
    first = re.search(r'retreat-about__body">\s*<p>(.*?)</p>', doc, re.S)
    first = clean(first.group(1)) if first else ""
    who = []
    if r:
        who = [teachers[t]["name"] for t in r["teacherIds"] if t in teachers and teachers[t].get("name")]
    prices = [int(x) for x in re.findall(r"priceUSD:\s*(\d+)", doc)]
    ld = []
    if r:  # a dated edition
        title = retreat_titles()[rid]
        when = date_long(r["startDate"], r["endDate"])
        head = f"{nights}-night yoga retreat in {place}, {when}" if nights else f"Yoga retreat in {place}, {when}"
        head += (f", with {' & '.join(who)}." if who else ".")
        desc = (head + " " + HOOK[rid]) if rid in HOOK else head
        status = r.get("status")
        ev = {"@context": "https://schema.org", "@type": "Event", "name": h1, "description": desc,
              "startDate": r["startDate"], "endDate": r["endDate"],
              "eventStatus": "https://schema.org/EventScheduled",
              "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
              "location": {"@type": "Place", "name": place,
                           "address": {"@type": "PostalAddress",
                                       "addressCountry": locs[r["locationId"]].get("country", "")}},
              "organizer": {"@id": BASE + "#organization"}, "url": url_for(fname)}
        if image:
            ev["image"] = [abs_img(image)]
        if who:
            ev["performer"] = [{"@type": "Person", "name": w} for w in who]
        if prices:
            ev["offers"] = {"@type": "AggregateOffer", "priceCurrency": "USD", "lowPrice": min(prices),
                            "highPrice": max(prices), "url": url_for(fname),
                            "availability": "https://schema.org/SoldOut" if status == "sold_out"
                            else "https://schema.org/InStock"}
        ld.append(ev)
    else:  # dateless waitlist page: a trip, not an event
        title = title_of(WAITLIST_TITLE.get(rid, h1.split(":")[0].strip()))
        n = f"{nights}-night " if nights else ""
        n = f"{nights}-night " if nights else ""
        desc = f"{WAITLIST_NAME.get(rid, h1)}: {n}yoga retreat in {place}. {HOOK.get(rid, '')} Join the waitlist for the next dates.".replace("  ", " ")
        trip = {"@context": "https://schema.org", "@type": "TouristTrip", "name": h1, "description": desc,
                "url": url_for(fname), "provider": {"@id": BASE + "#organization"},
                "touristType": "Yoga retreat travellers"}
        if image:
            trip["image"] = [abs_img(image)]
        ld.append(trip)
    ld.append(crumbs([HOME, ("Retreats", BASE + "retreats.html"), (h1, url_for(fname))]))
    return dict(title=title, desc=desc, image=image, type="website", ld=ld)


# ------------------------------------------------------------------------ apply --

def page_meta(fname, doc):
    if fname in STATIC:
        return static_page(fname, doc)
    if fname.startswith("retreatpage-"):
        return retreat_page(fname, doc)
    if fname.startswith("locationpage-"):
        return location_page(fname)
    if fname.startswith("teacher-"):
        return person_page(fname, "teacher")
    if fname.startswith("facilitator-"):
        return person_page(fname, "team")
    if fname.startswith("journal/"):
        return journal_page(fname)
    return None


_STRIP = [
    re.compile(re.escape(MARK_A) + r".*?" + re.escape(MARK_B) + r"\n?", re.S),
    re.compile(r"[ \t]*<title>.*?</title>[ \t]*\n?", re.S),
    re.compile(r'[ \t]*<meta\s+name="(?:description|robots|twitter:[^"]*)"[^>]*>[ \t]*\n?'),
    re.compile(r'[ \t]*<meta\s+property="(?:og|article|profile):[^"]*"[^>]*>[ \t]*\n?'),
    re.compile(r'[ \t]*<link\s+rel="canonical"[^>]*>[ \t]*\n?'),
    re.compile(r'[ \t]*<script type="application/ld\+json">.*?</script>[ \t]*\n?', re.S),
]


def _head_bounds(doc):
    i = doc.index("<head>") + len("<head>")
    return i, doc.index("</head>")


def block(fname, m, doc):
    img = m.get("image")
    if m.get("hero_from_page"):
        h = re.search(r"retreat-hero__image[^>]*url\('([^']+)'\)", doc)
        img = h.group(1) if h else None
    u = url_for(CANONICAL_OVERRIDE.get(fname, fname))
    t, dsc = esc(m["title"]), esc(m["desc"])
    lines = [MARK_A, f"<title>{t}</title>", f'<meta name="description" content="{dsc}">',
             f'<link rel="canonical" href="{u}">',
             f'<meta property="og:site_name" content="{SITE}">',
             f'<meta property="og:type" content="{m.get("type", "website")}">',
             f'<meta property="og:title" content="{t}">',
             f'<meta property="og:description" content="{dsc}">',
             f'<meta property="og:url" content="{u}">', '<meta property="og:locale" content="en_US">']
    if img:
        lines.append(f'<meta property="og:image" content="{abs_img(img)}">')
    if m.get("published"):
        lines.append(f'<meta property="article:published_time" content="{m["published"]}">')
    lines += ['<meta name="twitter:card" content="summary_large_image">',
              f'<meta name="twitter:title" content="{t}">',
              f'<meta name="twitter:description" content="{dsc}">']
    if img:
        lines.append(f'<meta name="twitter:image" content="{abs_img(img)}">')
    for obj in m.get("ld", []):
        lines.append('<script type="application/ld+json">' + json.dumps(obj, ensure_ascii=False) + "</script>")
    lines.append(MARK_B)
    return "\n".join(lines) + "\n"


def strip_head(doc):
    a, b = _head_bounds(doc)
    head = doc[a:b]
    for rx in _STRIP:
        head = rx.sub("", head)
    return doc[:a] + head + doc[b:]


def apply(doc, fname):
    """Return doc with the SEO head for fname. fname is repo-relative
    ('retreatpage-x.html', 'journal/x.html'); unknown pages come back as is."""
    m = page_meta(fname, doc)
    if m is None:
        return doc
    doc = strip_head(doc)
    a, b = _head_bounds(doc)
    head = doc[a:b]
    vp = re.search(r'<meta name="viewport"[^>]*>\n?', head)
    if not vp:
        raise SystemExit(f"{fname}: no viewport meta to anchor the SEO block")
    at = a + vp.end()
    nl = "" if doc[at - 1] == "\n" else "\n"
    return doc[:at] + nl + block(fname, m, doc) + doc[at:]


def noindex(doc):
    if re.search(r'<meta\s+name="robots"', doc):
        return doc
    m = re.search(r'<meta charset="[^"]*">\n?', doc)
    if not m:
        m = re.search(r"<head[^>]*>\n?", doc)
        if not m:
            return doc
    return doc[:m.end()] + '<meta name="robots" content="noindex">\n' + doc[m.end():]


# ------------------------------------------------------------------- the sweep --

def official_pages():
    files = list(STATIC) + sorted(
        os.path.relpath(p, REPO) for pat in ("retreatpage-*.html", "locationpage-*.html", "teacher-*.html",
                                              "facilitator-*.html", "journal/*.html")
        for p in glob.glob(os.path.join(REPO, pat)))
    skip = {os.path.join(REPO, "retreatpage-elcuyo.html")}
    return [f for f in files if os.path.join(REPO, f) not in skip]


def sitemap_pages():
    return [f for f in official_pages() if f not in CANONICAL_OVERRIDE]


def draft_pages():
    out = set()
    for pat in NOINDEX_GLOBS:
        for p in glob.glob(os.path.join(REPO, pat), recursive=True):
            out.add(os.path.relpath(p, REPO))
    return sorted(out)


def lastmod(fname):
    r = subprocess.run(["git", "log", "-1", "--format=%cs", "--", fname], cwd=REPO, capture_output=True, text=True)
    return r.stdout.strip() or datetime.date.today().isoformat()


def write_sitemap(pages):
    prio = {"index.html": "1.0", "retreats.html": "0.9", "locations.html": "0.8"}
    rows = []
    for f in pages:
        pr = prio.get(f) or ("0.8" if f.startswith(("retreatpage-", "locationpage-")) else
                             "0.7" if f in STATIC else "0.5" if f.startswith("journal/") else "0.6")
        rows.append(f"  <url>\n    <loc>{esc(url_for(f))}</loc>\n    <lastmod>{lastmod(f)}</lastmod>\n"
                    f"    <priority>{pr}</priority>\n  </url>")
    xml = ('<?xml version="1.0" encoding="UTF-8"?>\n'
           '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n' + "\n".join(rows) + "\n</urlset>\n")
    open(os.path.join(REPO, "sitemap.xml"), "w", encoding="utf-8").write(xml)


def sweep():
    changed = 0
    pages = official_pages()
    for f in pages:
        p = os.path.join(REPO, f)
        src = open(p, encoding="utf-8").read()
        out = apply(src, f)
        if out != src:
            open(p, "w", encoding="utf-8").write(out)
            changed += 1
    for f in draft_pages():
        p = os.path.join(REPO, f)
        src = open(p, encoding="utf-8").read()
        out = noindex(src)
        if out != src:
            open(p, "w", encoding="utf-8").write(out)
            changed += 1
    write_sitemap(sitemap_pages())
    print(f"{len(pages)} official pages, {len(draft_pages())} noindexed, {changed} files written, sitemap.xml")


# ------------------------------------------------------------------------ audit --

def audit():
    titles, bad = {}, []
    h1s = []
    alts = []
    for f in official_pages():
        src = open(os.path.join(REPO, f), encoding="utf-8").read()
        t = html.unescape(re.search(r"<title>(.*?)</title>", src, re.S).group(1))
        d = html.unescape(re.search(r'<meta name="description" content="([^"]*)"', src).group(1))
        titles.setdefault(t, []).append(f)
        if len(t) > 60:
            bad.append((f, "title", len(t), t))
        if not 140 <= len(d) <= 160:
            bad.append((f, "desc", len(d), d))
        n = len(re.findall(r"<h1[\s>]", src))
        if n != 1:
            h1s.append((f, n))
        noalt = [im for im in re.findall(r"<img\b[^>]*>", src)
                 if not re.search(r'\salt="[^"]+"', im) and "lightbox__img" not in im and im != "<img>"
                 and "' + " not in im and "' +" not in im]
        if noalt:
            alts.append((f, len(noalt)))
    print("TITLE/DESC OUT OF RANGE")
    for b in bad:
        print("  ", b)
    print("DUPLICATE TITLES")
    for t, fs in titles.items():
        if len(fs) > 1:
            print("  ", t, fs)
    print("H1 != 1 (static HTML)")
    for h in h1s:
        print("  ", h)
    print("IMG WITHOUT ALT TEXT")
    for a in alts:
        print("  ", a)


if __name__ == "__main__":
    sys.path.insert(0, os.path.dirname(__file__))
    if "--audit" in sys.argv:
        audit()
    else:
        sweep()
