#!/usr/bin/env python3
"""Export the static site's content into one JSON bundle for bin/import.php.

    python3 wordpress-theme/yaww/bin/export-content.py      # writes bin/content.json

The static site is the spec, so the content is read from what it shows:

  data/*.js                    locations, teachers, team, retreats, journal (run in node)
  retreatpage-<id>.html        every retreat page as built by retreats/_tools/build_page.py:
                               about, includes, itinerary, stays, rooms, extras, tips,
                               gallery, FAQs, testimonials. Parsing the built page (not
                               data.json) keeps every edit the builder and the hand-finished
                               El Cuyo template made.
  retreats/_tools/location_content.py
                               the location pages' copy (build_location.py renders it)
  retreats/_tools/page_content.py
                               facilitator per destination

Images are listed by their repo path (images/...); import.php puts each file in the
Media Library once, under the same file name, so css/photo-focus.css keeps matching.
Nothing here writes outside bin/content.json.
"""
import glob
import html
import json
import os
import re
import subprocess
import sys

THEME = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
REPO = os.path.abspath(os.path.join(THEME, "..", ".."))
TOOLS = os.path.join(REPO, "retreats", "_tools")
sys.path.insert(0, TOOLS)
import location_content as LC  # noqa: E402
import page_content as PC  # noqa: E402

SKIP_RETREATS = {"santorini-sep-2027-berger"}   # removed edition (BRIEF-WP)


def load_js():
    js = ("const vm=require('vm'),fs=require('fs');const c={window:{}};vm.createContext(c);"
          "for(const f of ['data/locations.js','data/teachers.js','data/team.js','data/retreats.js','data/journal.js'])"
          "vm.runInContext(fs.readFileSync(f,'utf8'),c);const w=c.window;"
          "process.stdout.write(JSON.stringify({locations:w.YAWW_LOCATIONS,regions:w.YAWW_REGIONS,teachers:w.YAWW_TEACHERS,"
          "teachersIntro:w.YAWW_TEACHERS_INTRO,team:w.YAWW_TEAM,teamIntro:w.YAWW_TEAM_INTRO,retreats:w.YAWW_RETREATS,journal:w.YAWW_JOURNAL}))")
    out = subprocess.run(["node", "-e", js], cwd=REPO, capture_output=True, text=True, check=True)
    return json.loads(out.stdout)


def js_values(snippets):
    """Evaluate JS literals (the pages' var TIERS = [...] etc.) in node."""
    js = ("let s='';process.stdin.on('data',d=>s+=d).on('end',()=>{const a=JSON.parse(s);"
          "process.stdout.write(JSON.stringify(a.map(x=>x===null?null:(0,eval)('('+x+')'))))})")
    out = subprocess.run(["node", "-e", js], input=json.dumps(snippets), capture_output=True, text=True, check=True)
    return json.loads(out.stdout)


def text(s):
    """Markup -> plain text (tags dropped, entities decoded, whitespace folded)."""
    s = re.sub(r"<[^>]+>", "", s or "")
    return re.sub(r"\s+", " ", html.unescape(s)).strip()


def block(doc, start_re, end_re):
    m = re.search(start_re, doc, re.S)
    if not m:
        return ""
    e = re.search(end_re, doc[m.end():], re.S)
    return doc[m.end(): m.end() + (e.start() if e else 0)]


def var(doc, name, closing):
    m = re.search(r"var %s = (\[.*?\n%s\]|\{[^\n]*?\}|null);" % (name, closing), doc, re.S)
    return m.group(1) if m else None


def img_list(chunk):
    return [{"src": s, "alt": html.unescape(a)} for s, a in re.findall(r'<img[^>]*?src="([^"]+)"[^>]*?alt="([^"]*)"', chunk)]


def num(s):
    m = re.search(r"\$?([\d,]+)", s or "")
    return int(m.group(1).replace(",", "")) if m else None


# ------------------------------------------------------------------ retreats --

def parse_retreat_page(path):
    doc = open(path, encoding="utf-8").read()
    r = {}
    m = re.search(r'<meta name="description" content="([^"]*)"', doc)
    r["seo_description"] = html.unescape(m.group(1)) if m else ""
    m = re.search(r'class="retreat-hero__image" role="img" aria-label="([^"]*)"(?: style="background-image: url\(\'([^\']+)\'\))?', doc)
    r["hero"] = {"src": m.group(2), "alt": html.unescape(m.group(1))} if m and m.group(2) else None

    about = block(doc, r'<div class="retreat-about__body">', r"\n    </div>\n  </section>")
    r["about"] = re.findall(r"<p>(.*?)</p>", about, re.S)          # kept as HTML paragraphs

    hosts = re.findall(r'<template data-host="([^"]+)">(.*?)</template>', doc, re.S)
    if not hosts:
        slot = block(doc, r'<div class="hosts__slot" data-host-slot>', r"\n      </div>")
        hosts = [("teacher", slot)] if slot else []
    r["hosts"] = []
    for key, h in hosts:
        link = re.search(r'class="host__link" href="([^"]+)"', h)
        bio = re.search(r'<p class="host__bio">(.*?)</p>', h, re.S)
        r["hosts"].append({"key": key, "name": text((re.search(r'host__name">(.*?)<', h) or [None, ""])[1]),
                           "href": link.group(1) if link else "", "bio": text(bio.group(1)) if bio else ""})
    tabs = re.findall(r'data-host-tab="([^"]+)"[^>]*>([^<]+)</button>', doc)
    for h in r["hosts"]:
        h["tab"] = dict(tabs).get(h["key"], "")

    inc = block(doc, r'<div class="retreat-includes__panel[^"]*" data-panel="includes"[^>]*>', r"\n        </div>")
    r["includes"] = [text(x) for x in re.findall(r"<li>(.*?)</li>", inc, re.S)]
    notinc = block(doc, r'<div class="retreat-includes__panel[^"]*" data-panel="not-included"[^>]*>', r"\n        </div>")
    r["not_included"] = [text(x) for x in re.findall(r"<li>(.*?)</li>", notinc, re.S)]
    r["includes_notes"] = [text(x) for x in re.findall(r'<p class="retreat-includes__footnote">(.*?)</p>', notinc, re.S)]
    m = re.search(r'class="retreat-includes__bg" aria-hidden="true" style="background-image: url\(\'([^\']+)\'\)', doc)
    r["includes_image"] = m.group(1) if m else None

    r["itinerary"] = []
    for day in re.findall(r'<li class="retreat-itinerary__day">(.*?)\n        </li>', doc, re.S):
        label = text(re.search(r'retreat-itinerary__label">(.*?)</span>', day).group(1))
        t = re.search(r'<li class="retreat-itinerary__day-title">(.*?)</li>', day, re.S)
        ul = block(day, r'<ul class="retreat-itinerary__bullets">', r"</ul>")
        bullets = [text(b) for b in re.findall(r"<li>(.*?)</li>", ul, re.S)]
        slider = block(day, r'<div class="photo-slider retreat-itinerary__slider"', r'<button class="photo-slider__arrow')
        r["itinerary"].append({"label": label, "title": text(t.group(1)) if t else "", "bullets": bullets,
                               "images": img_list(slider)})
    r["itinerary_notes"] = [text(x) for x in re.findall(r'<p class="retreat-itinerary__note">(.*?)</p>', doc, re.S)]

    names = ["HOTELS", "EXTRAS", "TIERS", "ROOM_VENUES", "TESTIMONIALS", "URGENCY"]
    closing = {"HOTELS": "    ", "EXTRAS": "    ", "TIERS": "  ", "TESTIMONIALS": "    "}
    snippets = []
    for n in names:
        if n == "ROOM_VENUES":
            m = re.search(r"var ROOM_VENUES = (\[[^\n]*\]);", doc)
            snippets.append(m.group(1) if m else None)
        elif n == "URGENCY":
            m = re.search(r"var URGENCY = ([^\n]*);", doc)
            snippets.append(m.group(1) if m else None)
        else:
            snippets.append(var(doc, n, closing[n]))
    hotels, extras, tiers, venues, quotes, urgency = js_values(snippets)
    # Extras only exist on the page when the EXTRAS section is still there.
    if "<!-- Optional Extras: none published" in doc:
        extras = []
    r["stays"] = [{"name": html.unescape(h["name"]), "tab": html.unescape(h.get("tab") or ""),
                   "text": [text(p) for p in h.get("text") or []], "gallery": h.get("gallery") or []} for h in hotels or []]
    r["extras"] = [{"name": html.unescape(x["name"]), "tab": html.unescape(x.get("tab") or ""), "desc": text(x.get("desc")),
                    "price": x.get("price") or "", "unit": x.get("unit") or "", "gallery": x.get("gallery") or []}
                   for x in extras or []]
    r["rooms"] = [{"name": t["name"], "price_usd": t.get("priceUSD"), "price_unit": t.get("priceUnit") or "",
                   "description": t.get("desc") or "", "pills": t.get("pills") or [], "gallery": t.get("gallery") or []}
                  for t in tiers or []]
    r["room_venues"] = venues or []
    r["testimonials"] = quotes or []
    r["urgency"] = urgency

    steps = re.findall(r"<li>\s*<strong>[^<]*</strong>\s*<span>(.*?)</span>", block(doc, r'<ol class="rooms__steps">', r"</ol>"), re.S)
    r["deposit_usd"] = num(steps[1]) if len(steps) > 1 else None
    m = re.search(r"due (\d+) days before", steps[2] if len(steps) > 2 else "")
    r["balance_due_days"] = int(m.group(1)) if m else None

    tips = block(doc, r'<section class="tips">', r"</section>")
    r["travel_tips"] = [{"heading": text(h), "items": [text(i) for i in re.findall(r"<li>(.*?)</li>", ul, re.S)]}
                        for h, ul in re.findall(r'<h3 class="tips__heading">(.*?)</h3>\s*<ul class="tips__list">(.*?)</ul>', tips, re.S)]

    gal = block(doc, r'<section class="gallery">', r"\n  </section>")
    m = re.search(r'<h2 class="gallery__title">(.*?)</h2>', gal, re.S)
    r["gallery_title"] = text(m.group(1)) if m else ""
    m = re.search(r'<p class="gallery__intro">(.*?)</p>', gal, re.S)
    r["gallery_intro"] = text(m.group(1)) if m else ""
    r["gallery"] = [{"ratio": ratio, "src": s, "alt": html.unescape(a)} for ratio, s, a in
                    re.findall(r'<figure class="gallery__slide" style="--r: ([^"]+)"><img src="([^"]+)" alt="([^"]*)"', gal)]

    r["faqs"] = []
    for q, ans in re.findall(r'<span class="faq__q">(.*?)</span>.*?<div class="faq__answer"[^>]*>(.*?)\n          </div>', doc, re.S):
        r["faqs"].append({"question": text(q), "answer": "\n".join(x.strip() for x in ans.strip().splitlines())})
    r["has_waitlist"] = 'id="waitlist"' in doc
    return r


def retreats(cat):
    out = []
    teacher_ids = {t["id"] for t in cat["teachers"]}
    for x in cat["retreats"]:
        if x["id"] in SKIP_RETREATS:
            continue
        page = os.path.join(REPO, x.get("pageUrl") or f"retreatpage-{x['id']}.html")
        p = parse_retreat_page(page)
        dj = json.load(open(os.path.join(REPO, "retreats", x["id"], "data.json"), encoding="utf-8"))
        out.append(dict(p, source_id=x["id"], slug=x["slug"], title=x["officialName"], location=x["locationId"],
                        teachers=[t for t in x["teacherIds"] if t in teacher_ids and t != "tbd"],
                        facilitators=[PC.FACILITATORS[x["locationId"]]] if x["locationId"] in PC.FACILITATORS else [],
                        start_date=x["startDate"], end_date=x["endDate"], status=x.get("status") or "available",
                        spaces_left=x.get("spacesLeft"), collection=x.get("collection"),
                        card_image=x.get("image"), card_image_2x=x.get("image2x"),
                        subtitle=dj.get("subtitle"), nights=dj.get("nights"),
                        roommate_note=(dj.get("booking") or {}).get("roommate_note"), waitlist=False,
                        static_url=x.get("pageUrl")))
    # Dateless retreats between editions (location.program): retreatpage-<location>.html, waitlist mode.
    for loc in cat["locations"]:
        prog = loc.get("program")
        if not prog:
            continue
        p = parse_retreat_page(os.path.join(REPO, prog["pageUrl"]))
        out.append(dict(p, source_id="program:" + loc["id"], slug=loc["id"], title=prog["name"], location=loc["id"],
                        teachers=[], facilitators=[], start_date=None, end_date=None, status="waitlist",
                        spaces_left=None, collection=prog.get("collection"),
                        card_image=prog.get("image"), card_image_2x=prog.get("image2x"),
                        subtitle=None, nights=None, roommate_note=None, waitlist=True, static_url=prog["pageUrl"]))
    return out


# ----------------------------------------------------------------- locations --

def loc_photo(loc_id, key):
    hits = glob.glob(os.path.join(REPO, "images", "retreat-photos", loc_id, "*-" + key + ".webp"))
    return os.path.relpath(hits[0], REPO) if hits else None


def loc_alt(key, place):
    words = key.replace("-", " ")
    return words[0].upper() + words[1:] + ", " + place


def locations(cat):
    grid = open(os.path.join(REPO, "locations.html"), encoding="utf-8").read()
    grid_alts = {k: html.unescape(v) for k, v in re.findall(r'<img src="images/location-([a-z-]+)\.jpg" alt="([^"]*)"', grid)}
    out = []
    for i, L in enumerate(cat["locations"]):
        c = LC.LOCATIONS.get(L["id"], {})
        place = c.get("title") or L["name"]
        img = lambda k: {"src": loc_photo(L["id"], k), "alt": loc_alt(k, place)} if k and loc_photo(L["id"], k) else None  # noqa: E731
        q = c.get("quote") or []
        card = f"images/location-{L['id']}.jpg"
        out.append({
            "source_id": L["id"], "slug": L["id"], "title": c.get("title") or L["name"], "menu_order": i,
            "display_name": L.get("displayName"), "card_name": L.get("cardName"), "short_name": L.get("name"),
            "region": L.get("region"), "country": L.get("country"), "placeholder_hue": L.get("placeholderHue"),
            "card_image": {"src": card, "alt": grid_alts.get(L["id"], place)} if os.path.exists(os.path.join(REPO, card)) else None,
            "hero_image": img(c.get("hero")),
            "eyebrow": c.get("eyebrow"), "tagline": c.get("tagline"), "blurb": c.get("blurb"),
            "facts": [{"label": a, "value": b} for a, b in c.get("facts") or []],
            "intro_title": c.get("intro_title"), "intro": c.get("intro") or [], "intro_image": img(c.get("intro_img")),
            "guide": [dict(kicker=g.get("kicker"), title=g.get("title"), text=g.get("text"), image=img(g.get("img")))
                      for g in c.get("guide") or []],
            "know": [{"label": a, "value": b} for a, b in c.get("know") or []],
            "quote_text": q[0] if len(q) > 0 else None, "quote_name": q[1] if len(q) > 1 else None,
            "quote_source": q[2] if len(q) > 2 else None,
            "static_url": L.get("pageUrl"),
        })
    return out


# -------------------------------------------------------------- people, posts --

def people(cat, all_retreats):
    host_bio = {}
    for r in all_retreats:
        for h in r["hosts"]:
            m = re.match(r"teacher-([a-z-]+)\.html", h["href"])
            if m:
                host_bio.setdefault(m.group(1), h["bio"])
    teachers = []
    for i, t in enumerate(x for x in cat["teachers"] if x["id"] != "tbd"):
        teachers.append({
            "source_id": t["id"], "slug": t.get("slug") or t["id"], "title": t["name"], "menu_order": i,
            "bio": t.get("bio") or [], "short_bio": t.get("shortBio"), "short_name": t.get("shortName"),
            "role": t.get("role"), "quote": t.get("quote"), "specialties": t.get("specialties") or [],
            "photo": t.get("photo"), "avatar": t.get("avatar"), "photo_position": t.get("photoPosition"),
            "youtube": t.get("youtube") or t.get("video"), "spotify_url": t.get("spotifyPlaylist") or t.get("spotify"),
            "website": (t.get("website") or {}).get("url") if isinstance(t.get("website"), dict) else t.get("website"),
            "website_label": (t.get("website") or {}).get("label") if isinstance(t.get("website"), dict) else None,
            "instagram": t.get("instagram"), "needs_content": bool(t.get("needsContent")),
            "host_bio": host_bio.get(t["id"]) if host_bio.get(t["id"]) and host_bio.get(t["id"]) != t.get("shortBio") else None,
            "static_url": t.get("pageUrl"),
        })
    team = []
    for i, t in enumerate(cat["team"]):
        team.append({
            "source_id": t["id"], "slug": t.get("slug") or t["id"], "title": t["name"], "menu_order": i,
            "bio": t.get("bio") or [], "short_bio": t.get("shortBio"), "role": t.get("role"),
            "photo": t.get("photo"), "photo_position": t.get("photoPosition"),
            "listed": t.get("listed", True) is not False, "static_url": t.get("pageUrl"),
        })
    return teachers, team


def journal(cat):
    out = []
    for i, j in enumerate(cat["journal"]):
        a = j.get("author") or {}
        r = j.get("retreat") or {}
        person = None
        m = re.match(r"(teacher|team|facilitator)-([a-z-]+)\.html", a.get("pageUrl") or "")
        if m:
            person = ("teacher" if m.group(1) == "teacher" else "facilitator", m.group(2))
        out.append({
            "source_id": j["slug"], "slug": j["slug"], "title": j["title"], "excerpt": j.get("excerpt") or "",
            "categories": j.get("categories") or [], "date": j.get("dateISO"), "date_label": j.get("date"), "order": i,
            "hero": {"src": j["hero"]["src"], "src2x": j["hero"].get("src2x"), "alt": j["title"]} if j.get("hero") else None,
            "blocks": j.get("blocks") or [],
            "author_name": a.get("name"), "author_bio": a.get("bio") or [],
            "author_photo": {"src": a["photo"], "src2x": a.get("photo2x"), "alt": a.get("name")} if a.get("photo") else None,
            "author_person": person,
            "retreat": {"title": r.get("title"), "subtitle": r.get("subtitle"), "text": r.get("text"),
                        "location": r.get("locationId"),
                        "image": {"src": r["image"], "src2x": r.get("image2x"), "alt": r.get("title")} if r.get("image") else None}
            if r else None,
        })
    return out


def main():
    cat = load_js()
    rs = retreats(cat)
    teachers, team = people(cat, rs)
    bundle = {
        "generated_from": "static site (data/*.js, retreatpage-*.html, retreats/_tools/location_content.py)",
        "regions": cat["regions"], "teachers_intro": cat["teachersIntro"], "team_intro": cat["teamIntro"],
        "locations": locations(cat), "teachers": teachers, "facilitators": team, "retreats": rs, "journal": journal(cat),
    }
    # Every image path must exist in the repo; report the ones that do not.
    missing = set()

    def walk(v):
        if isinstance(v, dict):
            for k, x in v.items():
                walk(x)
        elif isinstance(v, list):
            for x in v:
                walk(x)
        elif isinstance(v, str) and re.match(r"^images/[^ ]+\.(jpe?g|png|webp|gif)$", v, re.I):
            if not os.path.exists(os.path.join(REPO, v)):
                missing.add(v)
    walk(bundle)
    out = os.path.join(THEME, "bin", "content.json")
    json.dump(bundle, open(out, "w", encoding="utf-8"), ensure_ascii=False, indent=1)
    print(f"wrote {os.path.relpath(out, REPO)}: {len(bundle['locations'])} locations, {len(teachers)} teachers, "
          f"{len(team)} facilitators, {len(rs)} retreats, {len(bundle['journal'])} journal posts")
    for m in sorted(missing):
        print("missing image:", m, file=sys.stderr)


if __name__ == "__main__":
    main()
