/* ============================================================
   YAWW — LOCATIONS CATALOG
   Single source of truth for every destination we run retreats in.

   Maps to WordPress as a `location` taxonomy term (or a `location`
   CPT) once the prototype moves over:
     id          -> term slug
     displayName -> ACF text  (the italic line on every retreat card)
     name        -> term name  (short destination name, as on locations.html)
     region      -> parent term / ACF select  (filter grouping)
     country     -> ACF text
     pageUrl     -> permalink of the location page, null until one exists
     program     -> only for a destination with no upcoming dates: the
                    retreat we run there, between editions. name is its
                    title on the old site, pageUrl its dateless retreat
                    page (built by retreats/_tools/build_page.py in
                    waitlist mode, where the waitlist lead is captured),
                    image/image2x its card photo (the builder crops it
                    from the page's hero), collection when known.
                    Remove it once dates are out.
     image       -> ACF image  (card media; null falls back to the
                    gradient placeholder the prototype has always used)

   displayName FORMAT: "City & City, Country" or "City, Country".
   These strings are lifted verbatim from the italic .card__loc /
   .loc-card__where lines already in the HTML, so nothing on the page
   changes wording. Entries marked PROPOSED had no italic label
   anywhere in the site yet and follow the same pattern.

   placeholderHue only exists for locations with no photo on the site
   yet — it drives the same hsl() gradient the hardcoded cards used.
   ============================================================ */
window.YAWW_LOCATIONS = [
  {
    id: "el-cuyo",
    displayName: "El Cuyo, Mexico",
    name: "El Cuyo",
    region: "The Americas",
    country: "Mexico",
    pageUrl: "locationpage-el-cuyo.html",
    image: "images/location-el-cuyo.jpg",
    placeholderHue: 20
  },
  {
    id: "oaxaca",
    displayName: "Oaxaca, Mexico",
    name: "Oaxaca, Mexico",
    region: "The Americas",
    country: "Mexico",
    pageUrl: "locationpage-oaxaca.html",
    image: "images/location-oaxaca.jpg",
    placeholderHue: 36
  },
  {
    // PROPOSED label — the site had no italic Argentina line yet.
    id: "argentina",
    displayName: "Buenos Aires & Patagonia, Argentina",
    name: "Argentina",
    region: "The Americas",
    country: "Argentina",
    pageUrl: "locationpage-argentina.html",
    image: "images/location-argentina.jpg",
    placeholderHue: 28
  },
  {
    id: "santorini",
    displayName: "Santorini, Greece",
    name: "Santorini",
    region: "Europe",
    country: "Greece",
    pageUrl: "locationpage-santorini.html",
    image: "images/location-santorini.jpg",
    placeholderHue: 205
  },
  {
    id: "camino-de-santiago",
    displayName: "Camino de Santiago, Spain",
    name: "Camino de Santiago",
    region: "Europe",
    country: "Spain",
    pageUrl: "locationpage-camino-de-santiago.html",
    // Cropped from the retreat's Santiago cathedral photo (2026-11-20).
    image: "images/location-camino-de-santiago.jpg",
    placeholderHue: 35
  },
  {
    id: "montenegro",
    displayName: "Bay of Kotor, Montenegro",
    name: "Montenegro",
    region: "Europe",
    country: "Montenegro",
    pageUrl: "locationpage-montenegro.html",
    image: "images/location-montenegro.jpg",
    placeholderHue: 150
  },
  {
    id: "portugal",
    displayName: "Lisboa & Costa Vicentina, Portugal",
    name: "Portugal",
    region: "Europe",
    country: "Portugal",
    pageUrl: "locationpage-portugal.html",
    image: "images/location-portugal.jpg",
    placeholderHue: 120
  },
  {
    id: "iceland",
    displayName: "Reykjavík & Vik, Iceland",
    name: "Iceland",
    region: "Europe",
    country: "Iceland",
    pageUrl: "locationpage-iceland.html",
    image: "images/location-iceland.jpg",
    placeholderHue: 200,
    program: { name: "The Iceland Experience", pageUrl: "retreatpage-iceland.html", collection: "signature-collection", image: "images/retreat-cards/iceland-waitlist.webp", image2x: "images/retreat-cards/iceland-waitlist@2x.webp" }
  },
  {
    id: "france",
    displayName: "Paris & Champagne, France",
    name: "France",
    region: "Europe",
    country: "France",
    pageUrl: "locationpage-france.html",
    image: "images/location-france.jpg",
    placeholderHue: 30,
    program: { name: "The Paris-Champagne Experience", pageUrl: "retreatpage-france.html", collection: "signature-collection", image: "images/retreat-cards/france-waitlist.webp", image2x: "images/retreat-cards/france-waitlist@2x.webp" }
  },
  {
    id: "sardinia",
    displayName: "Sardinia, Italy",
    name: "Sardinia",
    region: "Europe",
    country: "Italy",
    pageUrl: "locationpage-sardinia.html",
    image: "images/location-sardinia.jpg",
    placeholderHue: 194,
    program: { name: "Majestic Sardinia : A Yoga & Nature Retreat", pageUrl: "retreatpage-sardinia.html", image: "images/retreat-cards/sardinia-waitlist.webp", image2x: "images/retreat-cards/sardinia-waitlist@2x.webp" }
  },
  {
    // PROPOSED label — destination exists on locations.html, no retreat
    // card for it yet, so no italic line to copy.
    id: "spain-basque-country",
    displayName: "San Sebastián, Spain",
    name: "Spain, Basque Country",
    region: "Europe",
    country: "Spain",
    pageUrl: "locationpage-spain-basque-country.html",
    image: "images/location-spain-basque-country.jpg",
    placeholderHue: 165,
    program: { name: "Basque Country Spain : Yoga & Gastronomy Retreat", pageUrl: "retreatpage-spain-basque-country.html", image: "images/retreat-cards/spain-basque-country-waitlist.webp", image2x: "images/retreat-cards/spain-basque-country-waitlist@2x.webp" }
  },
  {
    id: "morocco",
    displayName: "Marrakesh & Essaouira, Morocco",
    name: "Morocco",
    region: "Africa & Asia",
    country: "Morocco",
    pageUrl: "locationpage-morocco.html",
    image: "images/location-morocco.jpg",
    placeholderHue: 24,
    program: { name: "Mesmerized in Morocco : A Yoga & Culture Retreat", pageUrl: "retreatpage-morocco.html", collection: "signature-collection", image: "images/retreat-cards/morocco-waitlist.webp", image2x: "images/retreat-cards/morocco-waitlist@2x.webp" }
  },
  {
    id: "thailand",
    displayName: "Chiang Mai & Koh Yao Noi, Thailand",
    name: "Thailand",
    region: "Africa & Asia",
    country: "Thailand",
    pageUrl: "locationpage-thailand.html",
    image: "images/location-thailand.jpg",
    placeholderHue: 162
  },
  {
    // PROPOSED label — destination exists on locations.html, no retreat
    // card for it yet.
    id: "turkey",
    displayName: "Istanbul, Turkey",
    name: "Turkey",
    region: "Africa & Asia",
    country: "Turkey",
    pageUrl: "locationpage-turkey.html",
    image: "images/location-turkey.jpg",
    placeholderHue: 15,
    program: { name: "Yoga & Culture Retreat Turkey : Istanbul & Cappadocia", pageUrl: "retreatpage-turkey.html", image: "images/retreat-cards/turkey-waitlist.webp", image2x: "images/retreat-cards/turkey-waitlist@2x.webp" }
  }
];

/* Region order for the Destination filter's groups. Matches the order
   the hardcoded filter used. */
window.YAWW_REGIONS = ["Europe", "The Americas", "Africa & Asia"];
