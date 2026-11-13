# -*- coding: utf-8 -*-
"""Build retreats/<slug>/data.json for the 19 open editions."""
import json, os, re, sys, datetime, copy
SC = "/private/tmp/claude-501/-Users-manuelserra-Documents-02-Clients-01-YAWW-DemoYAWW/b10d527e-4572-4d86-911f-4c50a3b035a2/scratchpad"
sys.path.insert(0, SC)
from editions import E, LOC, SUBTITLE
from curation import CUR
REPO = "/Users/manuelserra/Documents/02_Clients/01_YAWW/Website/yaww-website"
OUT = REPO + "/retreats"

ST = json.load(open(SC + "/structured.json"))
TEACHERS = json.load(open(SC + "/teachers.json"))
IMGSEC = json.load(open(SC + "/img_sections.json"))
ASSIGN = json.load(open(SC + "/img_assign.json")) if os.path.exists(SC + "/img_assign.json") else {}
CANON = json.load(open(SC + "/img_canon.json")) if os.path.exists(SC + "/img_canon.json") else {}

HOST = "https://yogaadventuresworldwide.com"
TODAY = "2026-11-12"

TEACHER_ALIAS = {"Renata Centi": "Renata Centi"}
TEACHER_ROLE = {"Rachel Gray": "Boudoir & portrait photographer"}

# The old site runs the photos of both venues through one gallery. These
# patterns split them back out; they were checked against the photographs
# themselves, not guessed from the file names alone.
HOTEL_SPLIT = {
 "yoga-retreat-portugal": [r"/DL-|11\.56\.52", r"herdade|Herdade|Family-Farmhouse|11\.56\.35"],
 "yoga-retreat-morocco": [r"/riad-|/1\.jpg", r"poolsidehat|essapool|jardins|JVM-Yoga-Shala"],
 "treasures-of-thailand-a-yoga-wellness-retreat": [r"/2\d{3}(-|\.)", r"beach|seaview|Yoga-01|Drone-Resort|Superior-Studio|bathroom"],
}

# Travel tips borrowed from the location page that covers the same destination.
TIPS_FROM = {"embodied-the-art-of-being-seen": "beach-yoga-retreat-mexico",
             "mini-yoga-retreats-in-paradise": "beach-yoga-retreat-mexico"}

DEPOSIT = {s: (750 if s in ("beach-yoga-retreat-mexico", "embodied-the-art-of-being-seen",
                            "mini-yoga-retreats-in-paradise") else 1000) for s in CUR}

CANCELLATION = [
 "All payments are 100% non refundable and non-transferable. We recommend that you buy trip cancellation insurance immediately upon making your initial deposit to cover any travel hiccups or complications that may arise between now and the time of your retreat. No exceptions are possible so please cover your trip by connecting with an insurance company that offers the right policy for you.",
]

STEM = {}
for _k, _v in ASSIGN.items():
    STEM.setdefault(os.path.basename(_k).split(".")[0], _v)

def img(url, section=None):
    """Turn a source path into the schema's image object, using the vision pass
    assignment when it exists."""
    if not url: return None
    a = ASSIGN.get(url) or ASSIGN.get(CANON.get(url, "")) or STEM.get(os.path.basename(url).split(".")[0])
    if not a:
        return {"file": None, "original_url": HOST + url, "alt": None,
                "shared": None, "_unresolved": True}
    o = {"file": a["file"], "original_url": a["original_url"], "alt": a.get("alt"),
         "shared": a.get("shared", False)}
    return o

def imgs(urls, section=None):
    return [x for x in (img(u, section) for u in (urls or [])) if x]

def strip_heading_lines(lines, title, destination):
    """Drop the heading lines the old site repeats at the top of a section."""
    norm = lambda s: re.sub(r'[^a-z0-9]', '', s.lower())
    nt, nd = norm(title), norm(destination)
    out = []
    for i, l in enumerate(lines):
        n = norm(l)
        if i < 4 and (n in nt or nt in n or n == nd or
                      re.match(r'^(in|the best yoga retreat|accommodations?|pricing|includes?|itinerary|travel tips.*|reservations?|payments?)', l.strip(), re.I) and len(l) < 60):
            continue
        out.append(l)
    return out

def sentence_cut(s, n):
    if len(s) <= n: return s
    cut = s[:n]
    for sep in ('. ', ' — ', ' · ', ', ', ' '):
        i = cut.rfind(sep)
        if i > n * 0.55: return cut[:i].rstrip(' ,.—·')
    return cut.rstrip()

def seo(ed, L, nights):
    dest = L["destination"]
    yr = ed["start"][:4]
    mon = datetime.date.fromisoformat(ed["start"]).strftime("%b %Y")
    t = f"{dest} Yoga Retreat · {mon}"
    if len(t) > 60: t = sentence_cut(t, 60)
    who = ed["teachers"][0] if ed["teachers"] else None
    bits = [f"{nights}-night yoga retreat in {L['location_label']}", ed["dates_label"]]
    if who: bits.append(f"with {who}")
    d = ". ".join([bits[0], ", ".join(bits[1:])]) + "."
    if ed["status"] == "sold_out": d = d[:-1] + " — sold out."
    if len(d) > 155: d = sentence_cut(d, 152) + "."
    return t, d

def build(ed):
    src = ed["src"]; L = LOC[src]; S = ST[src]; C = CUR[src]; IS = IMGSEC[src]
    d0 = datetime.date.fromisoformat(ed["start"]); d1 = datetime.date.fromisoformat(ed["end"])
    nights = (d1 - d0).days
    notes, missing = {}, []
    shared = len(S["dates"]) > 1

    # ---- intro / about -------------------------------------------------
    hero_lines = S["hero_lines"]
    intro_paras = [l for l in hero_lines if len(l) > 120]
    intro = intro_paras[0] if intro_paras else None
    about = strip_heading_lines(S["about"], ed["title"], L["destination"])
    about = [l for l in about if len(l) > 60]
    if not about:
        about = intro_paras
        notes["about_body"] = "The source page's About block holds no prose of its own; the paragraphs from the top of the location page are used instead."
    if shared:
        notes["about_body"] = (notes.get("about_body", "") + " ").strip() + \
            f" Location-level copy: shared by all {len(S['dates'])} dates on {src}."
    if intro and shared:
        notes["intro"] = f"Location-level copy from the top of {src}, shared by all its dates."

    # ---- includes ------------------------------------------------------
    inc = [l for l in S["includes"] if not re.match(r'^(join us|book|sign|reserve)', l, re.I)]
    notinc = [l for l in S["not_included"] if not re.match(r'^(join us|book|sign|reserve)', l, re.I)]
    if shared:
        notes["includes"] = f"Location-level list from {src}, shared by all its dates."

    # ---- itinerary -----------------------------------------------------
    itin = []
    for day in S["itinerary"]:
        d = dict(day)
        d["images"] = imgs(IS.get("itinerary_by_day", {}).get(str(day["day"]), []), "itinerary")
        itin.append(d)
    if shared and itin:
        notes["itinerary"] = f"Location-level itinerary from {src}, shared by all its dates."

    # ---- accommodations ------------------------------------------------
    accom = []
    byhotel = IS.get("stay_by_hotel", {})
    split = HOTEL_SPLIT.get(src)
    stay_all = IS["stay"] + IS.get("pricing_all", [])
    for i, h in enumerate(C["hotels"]):
        lab = h.get("label")
        if split and i < len(split):
            g = [u for u in stay_all if re.search(split[i], u)]
        else:
            g = byhotel.get(lab) if lab else None
            if not g: g = IS["stay"] if len(C["hotels"]) == 1 else list(IS["stay"])
        accom.append({"name": h["name"], "label": lab, "text": h["text"],
                      "instagram": h.get("instagram"), "gallery": imgs(g, "stay")})
    if len(C["hotels"]) > 1 and not split:
        notes["accommodations.gallery"] = ("The old site runs the photographs of both venues through a single "
                                           "gallery with no marker of which is which, so each venue here carries "
                                           "the whole set.")
    elif split:
        notes["accommodations.gallery"] = ("The old site runs both venues through one gallery; the photographs were "
                                           "looked at and split back to the venue they show.")
    if any(a["name"] is None for a in accom):
        notes["accommodations"] = "The old site describes the property but never names it, so `name` is null."

    # ---- rooms ---------------------------------------------------------
    byroom = IS.get("pricing_by_room", {})
    rooms = []
    base_gal = {}
    for r in C["rooms"]:
        rr = copy.deepcopy(r)
        anchor = r["name"].split(" — ")[0]
        g = byroom.get(anchor) or byroom.get(r["name"]) or base_gal.get(anchor) or []
        if g: base_gal.setdefault(anchor, g)
        rr["gallery"] = imgs(g, "pricing")
        rooms.append(rr)
    if any(not r["gallery"] for r in rooms):
        notes["rooms.gallery"] = ("Some room types have no photographs of their own: the old site shows images "
                                  "only under some of the headings in its pricing block.")
    if all(r["price_usd"] is None for r in rooms):
        notes["rooms"] = C.get("extras_note") or "The old site publishes no prices for these dates."
    pnotes = [l for l in S["pricing_notes"] if not re.match(r'^(join us|book|sign|reserve)', l, re.I)]
    if C.get("extras_note"): pnotes = pnotes + [C["extras_note"]]

    # ---- extras --------------------------------------------------------
    extras = []
    for x in C["extras"]:
        xx = dict(x); xx["gallery"] = []
        extras.append(xx)

    # ---- travel tips ---------------------------------------------------
    tips_src = TIPS_FROM.get(src, src)
    tips = ST[tips_src]["travel_tips"]
    tips = [g for g in tips if g["items"]]
    if not tips:
        missing.append("travel_tips")
        notes["travel_tips"] = "The source page has no Travel Tips section."
    elif tips_src != src:
        notes["travel_tips"] = f"Taken from the El Cuyo location page ({tips_src}): same destination and same guesthouse, but not published on this retreat's own page."
    elif shared:
        notes["travel_tips"] = f"Location-level content from {src}, shared by all its dates."

    # ---- gallery -------------------------------------------------------
    placed = set(re.findall(r'"file": "([^"]+)"', json.dumps([accom, rooms, itin])))
    for u in IS["hero"][:1]:
        a = ASSIGN.get(u) or ASSIGN.get(CANON.get(u, ""))
        if a: placed.add(a["file"])
    gal_urls = list(IS["gallery"])
    for u in IS.get("pricing_by_room", {}).get("_before", []):
        if u not in gal_urls: gal_urls.append(u)
    for u in IS["about"] + IS["includes"]:
        if u not in gal_urls: gal_urls.append(u)
    for bucket in ("gallery", "about", "includes", "stay", "pricing_all", "itinerary", "hero"):
        for u in IS.get(bucket, []):
            if u in gal_urls: continue
            a = ASSIGN.get(u) or ASSIGN.get(CANON.get(u, ""))
            if a and a["file"] not in placed:
                gal_urls.append(u); placed.add(a["file"])
    gallery = imgs(gal_urls, "gallery")
    for g in gallery: g["ratio"] = None
    if shared:
        notes["gallery"] = f"Location-level gallery from {src}, shared by all its dates."

    # ---- faqs ----------------------------------------------------------
    faqs = []
    raw = ST[src]["faqs_raw"]
    if raw:
        q = None
        for l in strip_heading_lines(raw, ed["title"], L["destination"]):
            if re.match(r'^(reserve|book|sign)', l, re.I): continue
            if l.endswith("?"):
                q = {"question": l, "answer": []}; faqs.append(q)
            elif q: q["answer"].append(l)
        notes["faqs"] = "From the source page's own FAQs block."
    else:
        faqs = [
          {"question": "Do you accept bitcoin?",
           "answer": ["In addition to credit card payments we also accept wire transfers, Zelle and Bitcoin. Please inquire for more details."]},
          {"question": "What is the cancellation policy?", "answer": list(CANCELLATION)},
          {"question": "What happens if my travel plans change?",
           "answer": ["There are no refunds as per our cancellation policies. We highly recommend that all of our guests secure a quality travel insurance that will cover them for any event which may cause a change of travel plans, including personal health issues or personal situations in general which may affect travel plans."]},
          {"question": "Are flight delays or cancellations covered?",
           "answer": ["Not by us: transportation delays, cancellations or changes due to strikes, labor disputes, or lockouts from airlines, boats, ferries or any type of transportation are among the events we recommend covering with travel insurance."]},
          {"question": "Where can I find the full terms & conditions?",
           "answer": ["The full terms and conditions are published at https://yogaadventuresworldwide.com/termsandconditions/ . By submitting payments toward your retreat you accept them."]},
        ]
        notes["faqs"] = ("The source page has no FAQs block. These five answers are quoted from the site-wide "
                         "Policies text inside the retreat page's Reserve section, matched to the five questions "
                         "the template ships. They are not retreat-specific.")

    # ---- teachers ------------------------------------------------------
    tt = []
    for name in ed["teachers"]:
        key = name if name in TEACHERS else next((k for k in TEACHERS if k.replace("  ", " ") == name), None)
        t = TEACHERS.get(key) if key else None
        photo = None
        if t and t.get("photo"):
            photo = img(t["photo"], "teacher")
        tt.append({"name": name,
                   "slug": re.sub(r'[^a-z0-9]+', '-', name.lower()).strip('-'),
                   "role": TEACHER_ROLE.get(name),
                   "bio": (t["bio"] if t and t["bio"] else None),
                   "photo": photo,
                   "instagram": None,
                   "source": (HOST + "/team/") if t else None})
    for t in tt:
        if t["bio"] is None:
            notes.setdefault("teachers", "")
            notes["teachers"] += f" No bio or photo for {t['name']} anywhere on the old site (not on /team/, not on the retreat page)."
            missing.append(f"teachers[{t['name']}].bio")
            missing.append(f"teachers[{t['name']}].photo")
    if "teachers" in notes: notes["teachers"] = notes["teachers"].strip()
    if not tt:
        notes["teachers"] = 'The old site advertises this date "with a fabulous YAWW instructor" — no teacher assigned yet.'
        missing.append("teachers")

    # ---- tier ----------------------------------------------------------
    cats = S["categories"]
    tier = "signature" if "signature-retreats" in cats else None
    if tier is None:
        notes["tier"] = ("Not deducible: the old site's retreat_categories are topical (yoga, art-creative, "
                         "yoga-food-and-wine, family-retreats) and only signature-retreats maps to a collection. "
                         "Left null rather than guessed.")
        missing.append("tier")

    # ---- hero ----------------------------------------------------------
    hero = img(IS["hero"][0], "hero") if IS["hero"] else None
    if hero is None: missing.append("hero_image")
    elif shared:
        notes["hero_image"] = f"Location-level hero from {src}, shared by all its dates."

    st, sd = seo(ed, L, nights)


    notes["highlights"] = "The old site has no highlights block; nothing was invented to fill it."
    notes["testimonials"] = ('The source page has an empty "Praise" carousel — the markup is there, the quotes are not. '
                             "Left null; the template ships placeholder quotes.")
    notes["carbon_offset"] = "No carbon-offset copy anywhere on the old site. The template's text is new site-wide copy."
    notes["seo_title"] = "DRAFT. Written only from this retreat's real destination, month and year."
    notes["seo_description"] = "DRAFT. Written only from real data: nights, location, published date range, teacher, status."
    notes["status"] = f'From /all-upcoming-dates/ on {TODAY}' + (f' — flag read "{ed["status_label"]}".' if ed["status_label"] else ".")
    if ed["start"] < TODAY:
        notes["date_start"] = f"This edition started on {ed['start']}, before the extraction date ({TODAY}), so it is already under way. It is still listed under Upcoming Dates on the old site."


    data = {
      "slug": ed["slug"], "title": ed["title"],
      "subtitle": SUBTITLE.get(src),
      "destination": L["destination"], "location_label": L["location_label"],
      "country": L["country"], "region": L["region"],
      "tier": tier, "tier_label": "Signature Collection" if tier == "signature" else None,
      "tier_source": ("retreat_categories-signature-retreats on the source post" if tier else None),
      "status": ed["status"], "status_label": ed["status_label"],
      "urgency": ({"type": "soldout", "label": None} if ed["status"] == "sold_out"
                  else {"type": "scarce", "label": ed["status_label"].rstrip("!").title()} if ed["status"] == "few_spaces"
                  else {"type": "new", "label": "New retreat"} if ed["status_label"] == "New Retreat" else None),
      "date_start": ed["start"], "date_end": ed["end"], "dates_label": ed["dates_label"],
      "season": ed["season"], "nights": nights, "days": (d1 - d0).days + 1,
      "teachers": tt, "teacher_unassigned": not tt,
      "hero_image": hero, "intro": intro,
      "about_title": None, "about_body": about, "highlights": [],
      "includes": inc, "includes_footnote": (" ".join(S["includes_footnotes"]) or None),
      "not_included": notinc, "not_included_footnote": (" ".join(S["not_included_footnotes"]) or None),
      "itinerary": itin, "itinerary_notes": S["itinerary_notes"],
      "optional_excursions_schedule": S["optional_excursions_schedule"],
      "accommodations": accom,
      "rooms": rooms, "pricing_notes": pnotes,
      "extras": extras,
      "travel_tips": tips,
      "gallery_title": None, "gallery_intro": None, "gallery": gallery,
      "faqs": faqs,
      "booking": {
        "deposit_usd": DEPOSIT[src],
        "deposit_label": f"${DEPOSIT[src]:,} USD per person",
        "deposit_instructions": ("Deposits of $750usd are due to secure your space in the retreat via Zelle to utopiaelcuyo@gmail.com or by scanning the QR on the page. Please check availability with us for accommodations before making your deposit."
                                 if DEPOSIT[src] == 750 else
                                 "Deposits of $1,000usd are due to secure your space in the retreat. Please check availability with us for accommodations before making your deposit."),
        "balance_due_days": 90,
        "balance_note": f"The balance of your retreat (minus your ${DEPOSIT[src]:,}usd initial deposit) is due 90 days prior to arrival. All details with instructions for settling up will be emailed to you three months prior to the retreat.",
        "payment_methods": ["Credit card", "Wire transfer", "Zelle", "Bitcoin"],
        "roommate_note": "Roommates should book together. If you would like to share a room and do not have a roommate we will provide one for you if possible. In the case that there is no one to share a room we ask that you take a private room instead.",
        "cancellation_policy": list(CANCELLATION),
        "booking_url": f"{HOST}/retreat/{src}/#reserve",
        "contact_url": f"{HOST}/contact-about-yoga-retreats/",
      },
      "testimonials": None,
      "carbon_offset": None,
      "source_url": f"{HOST}/retreat/{src}/",
      "source_post_slug": src,
      "source_categories": cats,
      "source_is_shared": shared,
      "seo_title": st, "seo_description": sd,
      "_notes": {k: v for k, v in notes.items() if v},
      "_missing": [],
    }
    EMPTY_OK = ("_notes", "_missing", "slug", "title")
    miss = set(m for m in missing if "[" in m)
    for k, v in data.items():
        if k in EMPTY_OK: continue
        if v in (None, [], "", {}): miss.add(k)
    for i, t in enumerate(data["teachers"]):
        for f in ("bio", "photo", "role", "instagram"):
            if not t.get(f): miss.add(f"teachers[{t['name']}].{f}")
    if not any(r.get("price_usd") for r in data["rooms"]): miss.add("rooms[].price_usd")
    if not any(a.get("name") for a in data["accommodations"]): miss.add("accommodations[].name")
    data["_missing"] = sorted(miss)
    return data

def main():
    os.makedirs(OUT, exist_ok=True)
    for ed in E:
        d = build(ed)
        p = f"{OUT}/{ed['slug']}"
        os.makedirs(p, exist_ok=True)
        json.dump(d, open(p + "/data.json", "w"), indent=1, ensure_ascii=False)
        n_img = sum(1 for x in json.dumps(d).split('"original_url"') if 1) - 1
        print(f'{ed["slug"]:30} about={len(d["about_body"])} inc={len(d["includes"])} days={len(d["itinerary"])} '
              f'rooms={len(d["rooms"])} extras={len(d["extras"])} tips={len(d["travel_tips"])} faqs={len(d["faqs"])} '
              f'gal={len(d["gallery"])} imgs={n_img} missing={len(d["_missing"])}')

main()
