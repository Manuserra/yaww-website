/* ============================================================
   YAWW — CONTACT FORMS

   Three forms behind one tab bar, which is how the source builds it:
   a single page with #general-inquiry, #teach-with-us and
   #build-a-custom-retreat as anchors, not three pages.

   Maps to WordPress as three Contact Form 7 forms (or ACF-defined
   fields) on one page:
     id    -> the anchor and the tab's data-tab
     label -> the tab's text
     fields[] -> name / label / type / required

   Field names are semantic here. The source reuses one form template
   for all three, so "social-media" carries "Retreat location(s)" on
   two of the tabs and "Retreat dates" lives under "lead-retreats" —
   harmless in their markup, confusing in a submission payload.
   ============================================================ */
window.YAWW_CONTACT_FORMS = [
  {
    id: "general-inquiry",
    label: "General Inquiry",
    title: "General Inquiry",
    fields: [
      { name: "first-name",        label: "First name",          type: "text",     required: true,  autocomplete: "given-name" },
      { name: "last-name",         label: "Last name",           type: "text",     required: true,  autocomplete: "family-name" },
      { name: "contact-info",      label: "Email & phone",       type: "text",     required: true,  autocomplete: "email" },
      { name: "retreat-locations", label: "Retreat location(s)", type: "text" },
      { name: "retreat-dates",     label: "Retreat dates",       type: "text" },
      { name: "room-preference",   label: "Room preference",     type: "text" },
      { name: "more-info",         label: "Tell us more",        type: "textarea" }
    ]
  },
  {
    id: "teach-with-us",
    label: "Teach with us",
    title: "Teach with us",
    fields: [
      { name: "first-name",     label: "First name",                      type: "text", required: true, autocomplete: "given-name" },
      { name: "last-name",      label: "Last name",                       type: "text", required: true, autocomplete: "family-name" },
      { name: "contact-info",   label: "Email & phone",                   type: "text", required: true, autocomplete: "email" },
      // The source writes these three in caps and the other tabs' in
      // sentence case. With labels visible that inconsistency shows, so
      // they are set in sentence case like every other label here.
      { name: "social-media",   label: "Social media accounts",           type: "text" },
      { name: "leads-retreats", label: "Do you currently lead retreats?", type: "text" },
      { name: "locations",      label: "What locations interest you?",    type: "text" },
      { name: "more-info",      label: "Tell us more",                    type: "textarea" }
    ]
  },
  {
    id: "build-a-custom-retreat",
    label: "Build a Custom Retreat",
    title: "Build a Custom Retreat",
    fields: [
      { name: "first-name",        label: "First name",            type: "text", required: true, autocomplete: "given-name" },
      { name: "last-name",         label: "Last name",             type: "text", required: true, autocomplete: "family-name" },
      { name: "contact-info",      label: "Email & phone",         type: "text", required: true, autocomplete: "email" },
      { name: "retreat-locations", label: "Retreat location(s)",   type: "text" },
      { name: "retreat-dates",     label: "Retreat dates",         type: "text" },
      { name: "group-size",        label: "How many in your group", type: "text" },
      { name: "more-info",         label: "Tell us more",          type: "textarea" }
    ]
  }
];
