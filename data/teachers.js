/* ============================================================
   YAWW — TEACHERS CATALOG
   Single source of truth for teacher names. The teacher pages that
   come later read from here too, which is why each entry has its own
   id rather than the name being repeated on every retreat.

   Maps to WordPress as a `teacher` CPT:
     id   -> post slug
     name -> post title

   The roster in the Teacher filter is built from THIS list, not from
   the retreats, so a teacher with no announced dates still appears
   with the muted "No dates yet" count (and the notify-me path).

   `tbd` is the stand-in for a retreat whose teacher is not announced:
   it carries no real name, and the card prints `placeholderLabel`
   instead ("with a YAWW instructor"). It is skipped in the filter
   roster — it is not a name anyone would filter by.
   ============================================================ */
window.YAWW_TEACHERS = [
  { id: "alicia-delaney",       name: "Alicia Delaney" },
  { id: "alison-buchanan",      name: "Alison Buchanan" },
  { id: "amy-sennett-starner",  name: "Amy Sennett-Starner" },
  { id: "bonnie-erickson",      name: "Bonnie Erickson" },
  { id: "dawn-thie",            name: "Dawn Thie" },
  { id: "genevieve-morin",      name: "Genevieve Morin" },
  { id: "jamie-coffey",         name: "Jamie Coffey" },
  { id: "jenniferlyn-chiemingo", name: "Jenniferlyn Chiemingo" },
  { id: "melanie-cordover",     name: "Melanie Cordover" },
  { id: "rachel-gray",          name: "Rachel Gray" },
  { id: "sue-berger",           name: "Sue Berger" },
  { id: "yessica-puebla",       name: "Yessica Puebla" },

  { id: "tbd", name: null, placeholderLabel: "a YAWW instructor", unannounced: true }
];
