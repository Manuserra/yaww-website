/* ============================================================
   YAWW — RETREATS (single source of truth)

   One entry per retreat = one concrete edition (destination + dates +
   teacher), which is exactly how the WordPress build will model it:
   a `retreat` custom post type, one post per row below.

     id / slug   -> post slug
     officialName-> post title
     locationId  -> relation to the `location` term   (data/locations.js)
     teacherIds  -> relation to `teacher` posts       (data/teachers.js)
     startDate   -> ACF date (ISO, Y-m-d)
     endDate     -> ACF date (ISO, Y-m-d)
     status      -> ACF select, see STATUS below
     spacesLeft  -> ACF number, only when status === "spaces_left"
     collection  -> ACF select: "deep-dives" | "signature-collection"
     image       -> ACF image, the card thumbnail at 312x408
     image2x     -> the same frame at 624x816, for retina
     url         -> permalink, /retreats/{slug}/
     pageUrl     -> the page built for this edition by
                    retreats/_tools/build_page.py. Every edition has one;
                    retreatHref() in js/retreat-cards.js still falls back
                    to the location page and then to the listing for any
                    row added without one.

   IMAGES. Both sizes are listed explicitly rather than derived from
   one path, so they map onto two registered WordPress image sizes and
   a missing retina file can never 404 behind a browser's back. A
   retreat with no image of its own falls back to its location's photo
   (data/locations.js), and then to the gradient placeholder — see
   retreatMedia() in js/retreat-cards.js.

   Nothing display-shaped lives here on purpose:
     · the italic location line comes from the location catalog
     · the teacher name comes from the teacher catalog
     · the date label is BUILT by YAWW.formatDateRange() in
       js/retreat-cards.js — never written out as text, so a date only
       ever has to be corrected in one place and in one format

   STATUS enum (js/retreat-cards.js turns these into the badge):
     "available"    -> no badge at all
     "filling_fast" -> "Filling fast"
     "spaces_left"  -> "N spaces left" / "1 space left"  (needs spacesLeft)
     "sold_out"     -> "Sold out"
     "new"          -> "New retreat"

   COLLECTION is a field on the retreat, not derived from the location,
   so a single El Cuyo date can be moved to the Signature Collection
   without touching the rest.

   Ordering here is chronological for human readability only — every
   helper sorts by startDate itself.
   ============================================================ */
window.YAWW_RETREATS = [
  {
    id: "morocco-nov-2026",
    slug: "morocco-nov-2026",
    pageUrl: "retreatpage-morocco-nov-2026.html",
    officialName: "Mesmerized in Morocco: A Yoga & Culture Retreat",
    locationId: "morocco",
    teacherIds: ["dawn-thie"],
    startDate: "2026-11-07",
    endDate: "2026-11-14",
    status: "sold_out",
    collection: "signature-collection",
    url: "/retreats/morocco-nov-2026/"
  },
  {
    id: "el-cuyo-dec-2026-delaney",
    slug: "el-cuyo-dec-2026-delaney",
    pageUrl: "retreatpage-el-cuyo-dec-2026-delaney.html",
    officialName: "Inner Glow Retreat",
    locationId: "el-cuyo",
    teacherIds: ["alicia-delaney"],
    startDate: "2026-12-01",
    endDate: "2026-12-06",
    status: "spaces_left",
    spacesLeft: 2,
    image: "images/retreat-cards/el-cuyo-dec-2026-delaney.webp",
    image2x: "images/retreat-cards/el-cuyo-dec-2026-delaney@2x.webp",
    collection: "deep-dives",
    url: "/retreats/el-cuyo-dec-2026-delaney/"
  },
  {
    id: "thailand-dec-2026",
    slug: "thailand-dec-2026",
    pageUrl: "retreatpage-thailand-dec-2026.html",
    officialName: "Treasures of Thailand: A Yoga + Wellness Retreat",
    locationId: "thailand",
    teacherIds: ["amy-sennett-starner"],
    startDate: "2026-12-01",
    endDate: "2026-12-10",
    status: "sold_out",
    collection: "signature-collection",
    url: "/retreats/thailand-dec-2026/"
  },
  {
    id: "el-cuyo-jan-2027-berger",
    slug: "el-cuyo-jan-2027-berger",
    pageUrl: "retreatpage-el-cuyo-jan-2027-berger.html",
    officialName: "Breath Body Soul Retreat",
    locationId: "el-cuyo",
    teacherIds: ["sue-berger"],
    startDate: "2027-01-24",
    endDate: "2027-01-29",
    status: "filling_fast",
    image: "images/retreat-cards/el-cuyo-jan-2027-berger.webp",
    image2x: "images/retreat-cards/el-cuyo-jan-2027-berger@2x.webp",
    collection: "deep-dives",
    url: "/retreats/el-cuyo-jan-2027-berger/"
  },
  {
    id: "el-cuyo-jan-2027-coffey",
    slug: "el-cuyo-jan-2027-coffey",
    pageUrl: "retreatpage-el-cuyo-jan-2027-coffey.html",
    officialName: "Revive & Renew: A Mini Yoga Retreat",
    locationId: "el-cuyo",
    teacherIds: ["jamie-coffey"],
    startDate: "2027-01-29",
    endDate: "2027-02-02",
    status: "available",
    image: "images/retreat-cards/el-cuyo-jan-2027-coffey.webp",
    image2x: "images/retreat-cards/el-cuyo-jan-2027-coffey@2x.webp",
    collection: "deep-dives",
    url: "/retreats/el-cuyo-jan-2027-coffey/"
  },
  {
    id: "el-cuyo-feb-2027-delaney",
    slug: "el-cuyo-feb-2027-delaney",
    pageUrl: "retreatpage-el-cuyo-feb-2027-delaney.html",
    officialName: "Radiance Retreat",
    locationId: "el-cuyo",
    teacherIds: ["alicia-delaney"],
    startDate: "2027-02-02",
    endDate: "2027-02-07",
    status: "filling_fast",
    image: "images/retreat-cards/el-cuyo-feb-2027-delaney.webp",
    image2x: "images/retreat-cards/el-cuyo-feb-2027-delaney@2x.webp",
    collection: "deep-dives",
    url: "/retreats/el-cuyo-feb-2027-delaney/"
  },
  {
    id: "el-cuyo-feb-2027-morin-gray",
    slug: "el-cuyo-feb-2027-morin-gray",
    pageUrl: "retreatpage-el-cuyo-feb-2027-morin-gray.html",
    officialName: "Embodied: The Art of Being Seen",
    locationId: "el-cuyo",
    teacherIds: ["genevieve-morin", "rachel-gray"],
    startDate: "2027-02-14",
    endDate: "2027-02-20",
    status: "available",
    image: "images/retreat-cards/el-cuyo-feb-2027-morin-gray.webp",
    image2x: "images/retreat-cards/el-cuyo-feb-2027-morin-gray@2x.webp",
    collection: "deep-dives",
    url: "/retreats/el-cuyo-feb-2027-morin-gray/"
  },
  {
    id: "argentina-mar-2027",
    slug: "argentina-mar-2027",
    pageUrl: "retreatpage-argentina-mar-2027.html",
    officialName: "Strong at Heart: A Yoga & Culture Retreat",
    locationId: "argentina",
    teacherIds: ["alison-buchanan"],
    startDate: "2027-03-12",
    endDate: "2027-03-19",
    status: "spaces_left",
    spacesLeft: 2,
    image: "images/retreat-cards/argentina-mar-2027.webp",
    image2x: "images/retreat-cards/argentina-mar-2027@2x.webp",
    collection: "signature-collection",
    url: "/retreats/argentina-mar-2027/"
  },
  {
    id: "camino-de-santiago-apr-2027",
    slug: "camino-de-santiago-apr-2027",
    pageUrl: "retreatpage-camino-de-santiago-apr-2027.html",
    officialName: "A Journey Home to Yourself",
    locationId: "camino-de-santiago",
    teacherIds: ["jenniferlyn-chiemingo"],
    startDate: "2027-04-18",
    endDate: "2027-04-27",
    status: "spaces_left",
    spacesLeft: 2,
    image: "images/retreat-cards/camino-de-santiago-apr-2027.webp",
    image2x: "images/retreat-cards/camino-de-santiago-apr-2027@2x.webp",
    collection: "signature-collection",
    url: "/retreats/camino-de-santiago-apr-2027/"
  },
  {
    id: "montenegro-may-2027-erickson",
    slug: "montenegro-may-2027-erickson",
    pageUrl: "retreatpage-montenegro-may-2027-erickson.html",
    officialName: "Into the Fire: A Yoga & Nature Retreat",
    locationId: "montenegro",
    teacherIds: ["bonnie-erickson"],
    startDate: "2027-05-15",
    endDate: "2027-05-21",
    status: "available",
    image: "images/retreat-cards/montenegro-may-2027-erickson.webp",
    image2x: "images/retreat-cards/montenegro-may-2027-erickson@2x.webp",
    collection: "signature-collection",
    url: "/retreats/montenegro-may-2027-erickson/"
  },
  {
    id: "portugal-may-2027",
    slug: "portugal-may-2027",
    pageUrl: "retreatpage-portugal-may-2027.html",
    officialName: "Inner Landscape Retreat",
    locationId: "portugal",
    teacherIds: ["dawn-thie"],
    startDate: "2027-05-16",
    endDate: "2027-05-23",
    status: "available",
    image: "images/retreat-cards/portugal-may-2027.webp",
    image2x: "images/retreat-cards/portugal-may-2027@2x.webp",
    collection: "signature-collection",
    url: "/retreats/portugal-may-2027/"
  },
  {
    id: "montenegro-may-2027-cordover",
    slug: "montenegro-may-2027-cordover",
    pageUrl: "retreatpage-montenegro-may-2027-cordover.html",
    officialName: "Come As You Are: A Yoga & Nature Retreat",
    locationId: "montenegro",
    teacherIds: ["melanie-cordover"],
    startDate: "2027-05-22",
    endDate: "2027-05-28",
    status: "spaces_left",
    spacesLeft: 1,
    image: "images/retreat-cards/montenegro-may-2027-cordover.webp",
    image2x: "images/retreat-cards/montenegro-may-2027-cordover@2x.webp",
    collection: "signature-collection",
    url: "/retreats/montenegro-may-2027-cordover/"
  },
  {
    id: "santorini-may-2027",
    slug: "santorini-may-2027",
    pageUrl: "retreatpage-santorini-may-2027.html",
    officialName: "Goddess Off Duty",
    locationId: "santorini",
    teacherIds: ["yessica-puebla"],
    startDate: "2027-05-23",
    endDate: "2027-05-29",
    status: "available",
    image: "images/retreat-cards/santorini-may-2027.webp",
    image2x: "images/retreat-cards/santorini-may-2027@2x.webp",
    collection: "signature-collection",
    url: "/retreats/santorini-may-2027/"
  },
  {
    id: "santorini-sep-2027-erickson",
    slug: "santorini-sep-2027-erickson",
    pageUrl: "retreatpage-santorini-sep-2027-erickson.html",
    officialName: "Yoga Retreat Greece",
    locationId: "santorini",
    teacherIds: ["bonnie-erickson"],
    startDate: "2027-09-12",
    endDate: "2027-09-18",
    status: "available",
    collection: "signature-collection",
    url: "/retreats/santorini-sep-2027-erickson/"
  },
  {
    id: "camino-de-santiago-sep-2027",
    slug: "camino-de-santiago-sep-2027",
    pageUrl: "retreatpage-camino-de-santiago-sep-2027.html",
    officialName: "The Pace of Your Own Footsteps",
    locationId: "camino-de-santiago",
    teacherIds: ["genevieve-morin"],
    startDate: "2027-09-19",
    endDate: "2027-09-28",
    status: "available",
    image: "images/retreat-cards/camino-de-santiago-sep-2027.webp",
    image2x: "images/retreat-cards/camino-de-santiago-sep-2027@2x.webp",
    collection: "signature-collection",
    url: "/retreats/camino-de-santiago-sep-2027/"
  },
  {
    // Same official name as santorini-sep-2027-erickson; the slug keeps
    // the two editions apart (and will keep the two WP posts apart).
    id: "santorini-sep-2027-d26",
    slug: "santorini-sep-2027-d26",
    pageUrl: "retreatpage-santorini-sep-2027-d26.html",
    officialName: "Yoga Retreat Greece",
    locationId: "santorini",
    teacherIds: ["tbd"],
    startDate: "2027-09-26",
    endDate: "2027-10-02",
    status: "available",
    collection: "signature-collection",
    url: "/retreats/santorini-sep-2027-d26/"
  },
  {
    id: "oaxaca-oct-2027",
    slug: "oaxaca-oct-2027",
    pageUrl: "retreatpage-oaxaca-oct-2027.html",
    officialName: "Day of the Dead Yoga + Reverence Retreat Oaxaca",
    locationId: "oaxaca",
    teacherIds: ["tbd"],
    startDate: "2027-10-28",
    endDate: "2027-11-04",
    status: "new",
    collection: "signature-collection",
    url: "/retreats/oaxaca-oct-2027/"
  }
];
