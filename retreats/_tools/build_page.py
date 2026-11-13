# -*- coding: utf-8 -*-
"""Fill retreatpage-elcuyo.html with a retreat's data.json.

Proof that the schema carries enough to drive the template. The template is
read, never written: every edit is a replacement of one marked region, so the
CSS, the scripts and the layout stay exactly as they are.
"""
import json, os, re, sys, datetime

REPO = "/Users/manuelserra/Documents/02_Clients/01_YAWW/Website/yaww-website"
TPL = REPO + "/retreatpage-elcuyo.html"


def esc(s):
    if s is None: return ""
    return (str(s).replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;")
            .replace('"', "&quot;"))


def jstr(s):
    """A JS single-quoted string literal."""
    if s is None: return "null"
    return "'" + str(s).replace("\\", "\\\\").replace("'", "\\'").replace("\n", " ") + "'"


def cut(html, start, end, new, label):
    """Replace html[start_marker .. end_marker) keeping both markers."""
    i = html.find(start)
    if i < 0: raise SystemExit(f"[{label}] start marker not found: {start[:60]!r}")
    j = html.find(end, i + len(start))
    if j < 0: raise SystemExit(f"[{label}] end marker not found: {end[:60]!r}")
    return html[:i + len(start)] + new + html[j:]


def bgstyle(im):
    if not im or not im.get("file"): return ""
    return (f" style=\"background-image: url('{im['file']}'); background-size: cover; "
            f"background-position: center; background-repeat: no-repeat;\"")


def slider(images, label, fallback_alt):
    """The template's photo-slider markup, for any number of photos."""
    if not images:
        return ""
    slides = "".join(
        f'\n                <div class="photo-slider__slide"><img src="{im["file"]}" '
        f'alt="{esc(im.get("alt") or fallback_alt)}" loading="lazy"></div>'
        for im in images)
    dots = "".join(
        f'\n                <button class="photo-slider__dot{" is-active" if i == 0 else ""}" '
        f'aria-label="Photo {i+1}"></button>' for i in range(len(images)))
    arrows = ('\n              <button class="photo-slider__arrow photo-slider__arrow--prev" aria-label="Previous photo">'
              '\n                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>'
              '\n              </button>'
              '\n              <button class="photo-slider__arrow photo-slider__arrow--next" aria-label="Next photo">'
              '\n                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>'
              '\n              </button>') if len(images) > 1 else ""
    return (f'\n            <div class="photo-slider retreat-itinerary__slider" role="group" '
            f'aria-roledescription="carousel" aria-label="{esc(label)}">'
            f'\n              <div class="photo-slider__track">{slides}\n              </div>'
            f'{arrows}'
            f'\n              <div class="photo-slider__dots">{dots}\n              </div>'
            f'\n            </div>')


def js_gallery(images, fallback_alt):
    if not images: return "[]"
    return "[" + ", ".join(
        "{src: %s, alt: %s}" % (jstr(im["file"]), jstr(im.get("alt") or fallback_alt))
        for im in images) + "]"


def build(slug):
    d = json.load(open(f"{REPO}/retreats/{slug}/data.json", encoding="utf-8"))
    html = open(TPL, encoding="utf-8").read()
    dest = d["destination"]
    place = d["location_label"]
    galt = f"{dest}, {d['country']}"

    # ---------------- head -------------------------------------------------
    html = html.replace("<title>YAWW · Beach Escape Retreat</title>",
                        f"<title>{esc(d['seo_title'])}</title>\n"
                        f'<meta name="description" content="{esc(d["seo_description"])}">', 1)

    # ---------------- hero -------------------------------------------------
    teach = d["teachers"]
    who = (" & ".join(t["name"] for t in teach) if teach else "a fabulous YAWW instructor")
    badge = (f'\n      <p class="retreat-hero__badge">{esc(d["tier_label"])}</p>'
             if d.get("tier_label") else "")
    hero_new = f'''
    <div class="retreat-hero__image" role="img" aria-label="{esc(d['hero_image']['alt'] if d.get('hero_image') and d['hero_image'].get('alt') else d['title'] + ' — ' + place)}"{bgstyle(d.get("hero_image"))}></div>

    <div class="retreat-hero__copy">
      <h1 class="retreat-hero__title">{esc(d['title'])}</h1>
      <p class="retreat-hero__location">{esc(place)}</p>{badge}
      <p class="retreat-hero__meta">
        <span>{esc(d['dates_label'])}</span>
        <span class="retreat-hero__meta-dot" aria-hidden="true">·</span>
        <span>with {esc(who)}</span>
      </p>
    </div>
'''
    html = cut(html, '<section class="retreat-hero">', '    <div class="retreat-hero__ctas">',
               hero_new, "hero")

    # ---------------- about ------------------------------------------------
    about = "".join(f"\n      <p>{esc(p)}</p>" for p in d["about_body"])
    html = cut(html, '<div class="retreat-about__body">', '\n    </div>\n  </section>',
               about, "about")
    if d.get("about_title"):
        html = html.replace('<h2 class="retreat-about__title">About this retreat</h2>',
                            f'<h2 class="retreat-about__title">{esc(d["about_title"])}</h2>', 1)

    # ---------------- teacher ----------------------------------------------
    lead = teach[0] if teach else None
    if lead and lead.get("bio"):
        bio = "".join(f"\n        <p>{esc(p)}</p>" for p in lead["bio"])
        others = [t for t in teach[1:]]
        for t in others:
            role = f" — {t['role']}" if t.get("role") else ""
            bio += (f'\n        <p><strong>{esc(t["name"])}{esc(role)}</strong> joins this edition. '
                    f'The old site publishes no biography for them.</p>')
        tsec = f'''
    <h2 class="retreat-teacher__title">Yoga with {esc(lead['name'])}</h2>
    <div class="retreat-teacher__row">
      <div class="retreat-teacher__photo" role="img" aria-label="{esc((lead.get('photo') or {}).get('alt') or lead['name'])}"{bgstyle(lead.get('photo'))}></div>
      <div class="retreat-teacher__text">{bio}
      </div>
    </div>
'''
    else:
        tsec = f'''
    <h2 class="retreat-teacher__title">Your teacher</h2>
    <div class="retreat-teacher__row">
      <div class="retreat-teacher__text">
        <p>{esc("This date is advertised with a fabulous YAWW instructor — the teacher has not been announced yet." if not teach else "No biography for " + who + " is published on the old site.")}</p>
      </div>
    </div>
'''
    html = cut(html, '<section class="retreat-teacher">', '\n  </section>', tsec, "teacher")

    # ---------------- includes ---------------------------------------------
    inc = "".join(f"\n            <li>{esc(x)}</li>" for x in d["includes"])
    html = cut(html, '<div class="retreat-includes__panel" data-panel="includes" role="tabpanel">\n          <ul class="retreat-includes__list">',
               '\n          </ul>', inc, "includes")
    notinc = "".join(f"\n            <li>{esc(x)}</li>" for x in d["not_included"])
    html = cut(html, '<ul class="retreat-includes__list">\n            <li>Airfare and airport transfers*</li>',
               '\n          </ul>', "", "notinc-clear")
    html = html.replace('<ul class="retreat-includes__list">\n            <li>Airfare and airport transfers*</li>',
                        '<ul class="retreat-includes__list">' + notinc, 1)
    foot = d.get("not_included_footnote") or d.get("includes_footnote")
    html = re.sub(r'<p class="retreat-includes__footnote">.*?</p>',
                  (f'<p class="retreat-includes__footnote">{esc(foot)}</p>' if foot else ""),
                  html, count=1, flags=re.S)
    # the includes background band
    bg = (d["gallery"][0] if d["gallery"] else d.get("hero_image"))
    if bg and bg.get("file"):
        html = html.replace('<div class="retreat-includes__bg" aria-hidden="true"></div>',
                            f'<div class="retreat-includes__bg" aria-hidden="true" style="background-image: '
                            f'url(\'{bg["file"]}\'), linear-gradient(155deg, hsl(150 20% 68%), hsl(150 24% 38%));"></div>', 1)

    # ---------------- itinerary --------------------------------------------
    days = ""
    for day in d["itinerary"]:
        bullets = "".join(f"\n                <li>{esc(b)}</li>" for b in day["bullets"])
        title = (f'\n              <h3 class="retreat-itinerary__day-title">{esc(day["title"])}</h3>'
                 if day.get("title") else "")
        days += f'''
        <li class="retreat-itinerary__day">
          <div class="retreat-itinerary__rail">
            <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
            <span class="retreat-itinerary__dot" aria-hidden="true"></span>
            <span class="retreat-itinerary__rail-line" aria-hidden="true"></span>
          </div>

          <div class="retreat-itinerary__content">
            <div class="retreat-itinerary__text">
              <div class="retreat-itinerary__head"><span class="retreat-itinerary__label">{esc(day["label"])}</span></div>{title}

              <ul class="retreat-itinerary__bullets">{bullets}
              </ul>
            </div>
{slider(day.get("images") or [], f'{day["label"]} photos', f'{day["label"]} of the retreat in {galt}')}
          </div>
        </li>
'''
    html = cut(html, '<ol class="retreat-itinerary__list">', '\n      </ol>', days, "itinerary")
    if d.get("itinerary_notes"):
        notes = "".join(f'\n      <p class="retreat-itinerary__note">{esc(n)}</p>'
                        for n in d["itinerary_notes"])
        html = html.replace('\n      </ol>', '\n      </ol>' + notes, 1)

    # ---------------- accommodations (HOTELS) ------------------------------
    hotels = []
    for h in d["accommodations"]:
        name = h.get("name") or h.get("label") or f"Where you stay in {dest}"
        text = ", ".join(jstr(p) for p in (h.get("text") or []))
        gal = ", ".join(jstr(g["file"]) for g in (h.get("gallery") or []))
        hotels.append("      {\n        name: %s,\n        gallery: [%s],\n        text: [%s]\n      }"
                      % (jstr(name), gal, text))
    html = cut(html, "    var HOTELS = [", "\n    ];", "\n" + ",\n".join(hotels), "HOTELS")

    # ---------------- rooms (TIERS) ----------------------------------------
    tiers = []
    for r in d["rooms"]:
        attrs = ", ".join(f"{k}: {jstr(v)}" for k, v in (r.get("attrs") or {}).items() if v)
        gal = ", ".join(jstr(g["file"]) for g in (r.get("gallery") or []))
        hotel = (d["accommodations"][0].get("name") or d["accommodations"][0].get("label")
                 or f"Where you stay in {dest}")
        price = r["price_usd"] if r.get("price_usd") is not None else "null"
        tiers.append(
            "    {\n      name: %s,\n      priceUSD: %s,\n      priceLabel: %s,\n      priceUnit: %s,\n"
            "      hotels: [\n        {\n          name: %s,\n          attrs: { %s },\n          gallery: [%s]\n        }\n      ]\n    }"
            % (jstr(r["name"]), price, jstr(r.get("price_label")), jstr(r.get("price_unit")),
               jstr(hotel), attrs, gal))
    html = cut(html, "  var TIERS = [", "\n  ];", "\n" + ",\n".join(tiers), "TIERS")

    # ---------------- urgency ----------------------------------------------
    u = d.get("urgency")
    html = re.sub(r"var URGENCY = \{[^}]*\};",
                  ("var URGENCY = { type: %s, label: %s };" % (jstr(u["type"]), jstr(u.get("label")))
                   if u else "var URGENCY = null;"),
                  html, count=1)

    # ---------------- extras ----------------------------------------------
    ex = []
    for x in d["extras"]:
        gal = ", ".join(jstr(g["file"]) for g in (x.get("gallery") or []))
        ex.append("      {\n        name: %s,\n        desc: %s,\n        price: %s, unit: %s,\n        gallery: [%s]\n      }"
                  % (jstr(x["name"]), jstr(x.get("desc")),
                     jstr(x.get("price_label") or (f"{int(x['price_usd'])} USD" if x.get("price_usd") else None)),
                     jstr(x.get("unit")), gal))
    html = cut(html, "    var EXTRAS = [", "\n    ];", "\n" + ",\n".join(ex), "EXTRAS")
    if not d["extras"]:
        html = html.replace('<section class="extras">',
                            '<section class="extras" hidden data-empty="no optional extras published for this retreat">', 1)

    # ---------------- travel tips -----------------------------------------
    if d["travel_tips"]:
        groups = ""
        for g in d["travel_tips"]:
            items = "".join(f"\n          <li>{esc(i)}</li>" for i in g["items"])
            head = (f'\n        <h3 class="tips__heading">{esc(g["heading"])}</h3>'
                    if g.get("heading") else "")
            groups += f'''
      <div class="tips__group">{head}
        <ul class="tips__list">{items}
        </ul>
      </div>
'''
        html = cut(html, '<div class="tips__body">', '\n    </div>\n  </section>', groups, "tips")
    else:
        html = re.sub(r'<section class="tips">.*?</section>',
                      '<!-- Travel Tips: the old site publishes none for this retreat. -->',
                      html, count=1, flags=re.S)

    # ---------------- gallery ---------------------------------------------
    RAT = ["28/17", "1/1", "28/17", "5/4", "28/17", "1/1", "28/17", "5/4", "1/1", "28/17"]
    slides = ""
    for i, g in enumerate(d["gallery"][:10]):
        slides += (f'\n        <figure class="gallery__slide" style="--r: {RAT[i % len(RAT)]}">'
                   f'<img src="{g["file"]}" alt="{esc(g.get("alt") or galt)}" loading="lazy"></figure>')
    html = cut(html, '<div class="gallery__rail">', '\n      </div>', slides, "gallery")
    html = html.replace('aria-label="Photos from El Cuyo"', f'aria-label="Photos from {esc(dest)}"', 1)
    html = re.sub(r'<h2 class="gallery__title">.*?</h2>',
                  f'<h2 class="gallery__title">{esc(d.get("gallery_title") or "Where you&rsquo;ll be")}</h2>',
                  html, count=1, flags=re.S)
    intro = d.get("gallery_intro") or f"Photographs from past retreats in {place}."
    html = re.sub(r'<p class="gallery__intro">.*?</p>',
                  f'<p class="gallery__intro">{esc(intro)}</p>', html, count=1, flags=re.S)

    # ---------------- how to book -----------------------------------------
    b = d["booking"]
    steps = f'''
          <li>
            <strong>Choose your room</strong>
            <span>{esc("Every price is per person, in USD." if any(r.get("price_usd") for r in d["rooms"]) else "Prices for these dates are not published yet.")}</span>
          </li>
          <li>
            <strong>Secure your space</strong>
            <span>{esc((("A $%s deposit per person holds your place. " % f"{int(b['deposit_usd']):,}") if b.get("deposit_usd") else "") + "It comes off your total — it is not an extra.")}</span>
          </li>
          <li>
            <strong>Pay the balance later</strong>
            <span>{esc("The rest is due %s days before the retreat." % b["balance_due_days"] if b.get("balance_due_days") else "The rest is due before the retreat.")}</span>
          </li>
'''
    html = cut(html, '<ol class="rooms__steps">', '\n        </ol>', steps, "steps")
    canc = (b.get("cancellation_policy") or [None])[0]
    html = re.sub(r'<!-- TODO: add the refund policy line here.*?-->',
                  (f'<p class="rooms__aside-note">{esc(canc)}</p>' if canc else ""),
                  html, count=1, flags=re.S)
    html = re.sub(r'<p class="rooms__aside-note">Sharing a room\?.*?</p>',
                  f'<p class="rooms__aside-note">{esc(b.get("roommate_note") or "")}</p>',
                  html, count=1, flags=re.S)

    # ---------------- faqs -------------------------------------------------
    faqs = ""
    for i, f in enumerate(d["faqs"], 1):
        ans = "".join(f"\n            <p>{esc(p)}</p>" for p in f["answer"])
        faqs += f'''
      <div class="faq">
        <h3 class="faq__heading">
          <button class="faq__trigger" type="button" aria-expanded="false" aria-controls="faq-{i}">
            <span class="faq__q">{esc(f["question"])}</span>
            <svg class="faq__chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
          </button>
        </h3>
        <div class="faq__panel">
          <div class="faq__answer" id="faq-{i}" role="region">{ans}
          </div>
        </div>
      </div>
'''
    html = cut(html, '<div class="faq-list">', '\n    </div>\n  </section>', faqs, "faqs")

    # ---------------- testimonials ----------------------------------------
    if not d.get("testimonials"):
        html = html.replace('<section class="quotes">',
                            '<section class="quotes" hidden data-empty="the old site publishes no guest quotes for this retreat">', 1)

    # ---------------- carbon ----------------------------------------------
    if not d.get("carbon_offset"):
        html = html.replace('<section class="carbon">',
                            '<section class="carbon" hidden data-empty="no carbon-offset copy on the old site">', 1)

    # ---------------- provenance banner -----------------------------------
    stamp = (f"\n<!-- Generated from retreats/{slug}/data.json on "
             f"{datetime.date.today().isoformat()} by filling retreatpage-elcuyo.html.\n"
             f"     Test page: it exists to prove the schema covers the template. -->\n")
    html = html.replace("<body>", "<body>" + stamp, 1)

    out = f"{REPO}/retreatpage-{slug}.html"
    open(out, "w", encoding="utf-8").write(html)
    return out, d


if __name__ == "__main__":
    for slug in sys.argv[1:]:
        out, d = build(slug)
        print(f"{os.path.basename(out):48} rooms={len(d['rooms'])} days={len(d['itinerary'])} "
              f"gal={len(d['gallery'])} faqs={len(d['faqs'])} tips={len(d['travel_tips'])}")
