/* One source of truth for every proposal and its options.
   Paths are relative to proposals/. The board links to view.html?t=<id>&o=<n>,
   which shows the option in a frame under one shared bar.
   chosen: true = what is live on the branch. external: true = opens in a new tab
   (third-party sites refuse to load inside a frame).
   To add a decision: add an entry here and an <article class="decision"> on the board. */
window.YAWW_PROPOSALS = [
  { id: "stone-color", title: "Stone colour", options: [
    { label: "A · Warm taupe", url: "stone-color/index.html#a" },
    { label: "B · Toasted oak", url: "stone-color/index.html#b" },
    { label: "C · Bronze", url: "stone-color/index.html#c", chosen: true },
    { label: "D · Walnut", url: "stone-color/index.html#d" }
  ]},
  { id: "card-hover", title: "Card hover", options: [
    { label: "Veil", url: "../proto-card-hover.html?v=1", chosen: true },
    { label: "Passe-partout", url: "../proto-card-hover.html?v=2" },
    { label: "Comes alive", url: "../proto-card-hover.html?v=3" },
    { label: "Second photo", url: "../proto-card-hover.html?v=4" }
  ]},
  { id: "retreats-search", title: "Retreats search", options: [
    { label: "A · Live page", url: "../retreats.html", chosen: true },
    { label: "B · Destination gallery", url: "retreats-search/option-b.html" },
    { label: "C · One search field", url: "retreats-search/option-c.html" },
    { label: "D · Four multi-selects", url: "retreats-search/option-d.html" }
  ]},
  { id: "retreat-hero", title: "Retreat hero", options: [
    { label: "A · Overlay", url: "hero/option-a.html", chosen: true },
    { label: "B · Split on stone", url: "hero/option-b.html" },
    { label: "C · Photo band below", url: "hero/option-c.html" },
    { label: "Live page", url: "../retreatpage-el-cuyo-dec-2026-delaney.html" }
  ]},
  { id: "retreat-rooms", title: "Room pricing", options: [
    { label: "A · Stacked cards", url: "../retreatpage-thailand-dec-2026.html#rooms", chosen: true },
    { label: "B · Two-up cards", url: "rooms/option-b.html" },
    { label: "C · Comparison table", url: "rooms/option-c.html" },
    { label: "D · Steps band, full-width list", url: "typography-expressive/retreatpage-el-cuyo-dec-2026-delaney-rooms-d.html#rooms" }
  ]},
  { id: "retreat-carbon", title: "Carbon offset", options: [
    { label: "A · B · C compared", url: "carbon/index.html", chosen: true }
  ]},
  { id: "teacher-page", title: "Teacher page", options: [
    { label: "A · Split profile", url: "teacher-page/option-a.html", chosen: true },
    { label: "B · Editorial hero", url: "teacher-page/option-b.html" },
    { label: "A live, all slots", url: "../teacher-alicia-delaney.html?slots=demo" }
  ]},
  { id: "review-badges", title: "Rating badges", options: [
    { label: "A · Our badges", url: "../about.html", chosen: true },
    { label: "B · Official widget ↗", url: "https://www.tripadvisor.com/Widgets", external: true }
  ]},
  { id: "facilitators", title: "Marina & Fleur", options: [
    { label: "A · Unlisted pages", url: "../facilitator-fleur-optekamp.html", chosen: true },
    { label: "B · Facilitators page", url: "../facilitators.html" }
  ]},
  { id: "contact", title: "Contact", options: [
    { label: "A · One human form", url: "../contact.html", chosen: true },
    { label: "B · Signposts", url: "contact/option-b.html" },
    { label: "C · Conversation first", url: "contact/option-c.html" },
    { label: "Teach with us", url: "../teach-with-us.html" }
  ]},
  { id: "location-template", title: "Location template", options: [
    { label: "A · Oaxaca", url: "../locationpage-oaxaca.html", chosen: true },
    { label: "A · Iceland", url: "../locationpage-iceland.html", chosen: true },
    { label: "B · Oaxaca", url: "location-template/option-b-oaxaca.html" },
    { label: "B · Iceland", url: "location-template/option-b-iceland.html" },
    { label: "C · Oaxaca", url: "location-template/option-c-oaxaca.html" },
    { label: "C · Iceland", url: "location-template/option-c-iceland.html" }
  ]},
  { id: "location-guide", title: "Location guide", options: [
    { label: "A · El Cuyo, as a guest sees it", url: "location-guide/locationpage-el-cuyo.html" },
    { label: "A · what we need", url: "location-guide/locationpage-el-cuyo.html?needs=1" },
    { label: "B · Destination guide", url: "location-guide/option-b.html" },
    { label: "B · what we need", url: "location-guide/option-b.html?needs=1" },
    { label: "Live page", url: "../locationpage-el-cuyo.html", chosen: true }
  ]},
  { id: "locations-grid", title: "Locations grid", options: [
    { label: "Fourteen places", url: "../locations.html", chosen: true }
  ]},
  { id: "typography-expressive", title: "Expressive type", options: [
    { label: "Specimen", url: "typography-expressive/index.html" },
    { label: "Compare", url: "typography-expressive/compare.html" },
    { label: "Home", url: "typography-expressive/home.html" },
    { label: "Retreat page", url: "typography-expressive/retreatpage-el-cuyo-dec-2026-delaney.html" },
    { label: "Location page", url: "typography-expressive/locationpage-oaxaca.html" },
    { label: "About", url: "typography-expressive/about.html" }
  ]},
  { id: "retreats-cards", title: "Retreat cards", options: [
    { label: "Retreats · Cards switch", url: "retreats-cards/retreats.html" }
  ]},
  { id: "hero-buttons", title: "Hero buttons", options: [
    { label: "El Cuyo · A / B / C switch", url: "hero-buttons/index.html" }
  ]},
  { id: "retreats-wide", title: "Retreats > 1440", options: [
    { label: "Retreats · Width switch", url: "retreats-wide/retreats.html" }
  ]},
  { id: "contact-alt", title: "Contact alternative", options: [
    { label: "Implemented · new contact", url: "../contact.html", chosen: true },
    { label: "Alternative layout", url: "contact-alt/index.html" }
  ]},
  { id: "renata-photo", title: "Renata photo", options: [
    { label: "New photo and alternates", url: "renata/index.html", chosen: true }
  ]},
  { id: "bullet-leading", title: "Bullet leading", options: [
    { label: "El Cuyo · bullets, rooms", url: "bullet-leading/retreatpage-el-cuyo-dec-2026-delaney.html", chosen: true },
    { label: "Camino · long days, photo test", url: "bullet-leading/retreatpage-camino-de-santiago-apr-2027.html" }
  ]}
];
