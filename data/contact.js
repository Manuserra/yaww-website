/* ============================================================
   YAWW · CONTACT DATA
   Ways to reach us and where each form posts. One place to edit,
   read by js/contact.js on contact.html and teach-with-us.html.

   Endpoints are null until the backend exists (Zapier hook, WP REST
   route or the CRM). While null, a valid submit logs the payload to
   the console and shows the success state, so the flow can be tested.
   Field names in the forms are snake_case and stable: map them 1:1.

   Why: docs/contact-strategy.md
   ============================================================ */
window.YAWW_CONTACT = {
  endpoints: {
    contact: null, // contact.html: question, private group, anything else
    teach: null    // teach-with-us.html: teacher application
  },

  // Shown on contact.html. Phone is the one in the live site footer.
  // The live contact page only gives a personal inbox; the address to
  // publish is still to be decided, so it shows as a visible placeholder.
  email: null,
  phone: { label: "+52 1 984 202 6072", href: "tel:+5219842026072" },
  instagram: null
};
