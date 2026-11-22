# Lead form ("I want more info")

The modal that opens from "I want more info" on every retreat page (hero, rooms aside) and from "Ask for info" in the sticky booking bar. Its job is to catch the people who are interested but not ready to pay a deposit, which is most visitors. Built by `js/lead-modal.js`, styled by `css/lead-modal.css`, loaded by every `retreatpage-*.html` through the template (`retreatpage-elcuyo.html`) and `retreats/_tools/build_page.py`.

## Principle

Two required fields, everything else optional and one tap. Every extra required field costs completions; every optional field that is a tap (not typing) costs almost nothing and makes the first reply much better. The retreat is never asked for: the form already knows which page it was opened on.

## Fields

| Name (sent as) | Shown as | Required | Why it is there |
|---|---|---|---|
| `first_name` | First name | yes | The reply is personal ("Hi Ana"). First name only: a surname adds friction and nothing to the first email. |
| `email` | Email | yes | The one way we are sure to reach them. Validated for shape only (`x@y.zz`). |
| `travel_party` | Who is coming? Just me / Two of us / A group | no | Changes the answer more than anything else: a solo traveller needs to hear about shared rooms and roommate matching; two people about shared-bed rates; a group is a different (bigger) conversation. One tap. |
| `topics[]` | What would you like to know? Rooms and prices / Is it right for my level? / Getting there / Something else | no | Lets the team answer the real question in the first reply instead of sending the full brochure. These four are the questions the old site's FAQs and the team's inbox keep coming back to. Pre-ticked "Rooms and prices" when the form was opened from a room. |
| `message` | Your question | no | For anyone who already knows what to ask. Optional, so it never blocks a send. |
| `contact_method` | How should we reach you? Email / WhatsApp / Phone call | no (defaults to Email) | Many guests prefer WhatsApp; offering it raises reply rates. Choosing WhatsApp or Phone reveals `phone`. |
| `phone` | Phone number, with country code | only if WhatsApp or Phone is chosen | Asked only when it will be used. |
| `marketing_opt_in` | Also send me news of future retreats | no, unticked | Explicit consent for the newsletter. Unticked by default (GDPR: consent must be opt-in). A lead who does not tick it is still answered. |

Hidden fields (filled by the script, never typed):

| Name | Content |
|---|---|
| `retreat_id` | The page's edition id, e.g. `el-cuyo-dec-2026-delaney` (from `.retreat-hero[data-retreat-id]`). Same id as `data/retreats.js`. |
| `retreat_name` | "Inner Glow Retreat" |
| `retreat_dates` | "December 1 – 6, 2026" (or "Dates to be announced" on a waitlist page) |
| `room` | The room the reader picked, when the form was opened from a room card or the room modal. Empty otherwise. |
| `intent` | `info` (asking) or `reserve` (pressed Reserve while online booking is not wired: same form, worded "Reserve your spot") |
| `source` | Which button opened it: `hero`, `sticky-bar`, `rooms`, `room-card`, `room-modal`, `waitlist`, `hosts` ("Ask about these dates" when the teacher is not announced yet) |
| `page_url` | The page, without the hash |
| `utm_source` … `landing_page`, `first_*` | Attribution, added on submit by `js/attribution.js`. See "Attribution" below. (Replaces the old single `utm` field.) |

The visible context on the left of the modal (photo, "You are asking about", name, dates, room) is the same information, so the reader can see what the team will receive.

## States

- **Errors** appear under the field, in words, after the first submit attempt and then live as the field is corrected. The field gets `aria-invalid` and the message is linked with `aria-describedby`; focus moves to the first field with a problem.
- **Sending**: the button is disabled and reads "Sending…".
- **Success**: the form is replaced in place (same height) by "Thank you, Ana." and "Jenniferlyn will write back to ana@example.com within 24 hours.", the same words as on contact.html. Name and email are set as text, never as HTML. One button back to the page. A `yaww:lead` event is dispatched on `document` with the payload, for analytics.
- **Failure** (only with a real endpoint): a message above the button; nothing typed is lost.

## Submission and WeTravel

There is no real submission yet. `ENDPOINT` at the top of `js/lead-modal.js` (also written to the form as `data-endpoint`) is empty, so a send waits 700ms and shows the success state. When an endpoint exists, set it there: the form POSTs JSON with exactly the names above (`topics` as an array).

Mapping, when it is connected:

- **WeTravel** does not take leads from an outside form on its own; its trip page collects the booking. The practical route is a small webhook (Zapier/Make or a serverless function) that receives this JSON and (a) creates or updates the contact in the CRM / mailing tool the team uses, (b) emails the team with `retreat_name`, `room`, `travel_party`, `topics`, `message` and the reply channel. When the lead later books on WeTravel, the email address is the join key.
- Field names are WeTravel-friendly where WeTravel has an equivalent: `first_name`, `email`, `phone` match its participant fields; `retreat_id` should be mapped to the WeTravel trip id in the webhook (one lookup table, one row per edition).
- `marketing_opt_in = yes` is the only signal that may add the person to the newsletter list.

## Where it opens from

Any element with `data-lead-open` (a delegated click listener, so elements added later work too). On every `retreatpage-*.html` that is: "I want more info" in the hero and in the rooms aside, "Reserve your spot" while WeTravel is not wired, "Ask for info" in the sticky bar, "Questions? Talk to us" on waitlist pages, and "Ask about these dates" in the hosts block when the teacher is not announced (that one used to be a plain link to `contact.html#general-inquiry`; fixed in `build_page.py`, 2026-11-23). The template `retreatpage-elcuyo.html` carries the same openers. Optional `data-lead-source`, `data-lead-room`, `data-lead-intent="reserve"`. Every opener is a real link to `contact.html#general-inquiry`, so without JavaScript it still leads somewhere useful.

## Accessibility

Native `<dialog>` opened with `showModal()` (page behind is inert, Esc closes), plus: Tab and Shift+Tab are kept inside; the page does not scroll behind it (Lenis is stopped too); focus goes to First name on open and back to the opening button on close; the backdrop click closes; all choices are real radio buttons and checkboxes inside square pills; 16px inputs so iOS does not zoom; error colour #9A3B2E is 6.2:1 on the stone background and every error also has words and a "!" mark. Reduced motion: no slide or scale, it simply appears.

## Attribution (both forms)

`js/attribution.js` is loaded on every page (`retreats/_tools/chrome.py`). It records where the visit came from and adds it to the contact form and to this pop-up as hidden inputs at the moment of submit (any `<form data-attribution>`; capture-phase listener, so the fields exist before the form's own handler builds the payload). With no endpoint yet the fields show up in the logged / dispatched payload.

| Name | Content |
|---|---|
| `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content` | From the address of the first page of the session, if the link carried them |
| `referrer` | The other site that sent the visitor: origin + path, query string dropped. Empty for internal navigation and direct visits |
| `landing_page` | Path of the first page of the session (no query string), e.g. `/retreatpage-el-cuyo-dec-2026-delaney.html` |
| `first_utm_source` … `first_landing_page`, `first_seen` | The same, for the first visit ever on this browser, with its date (`YYYY-MM-DD`) |

Rules:

- **Session** (`sessionStorage["yaww_attr"]`): written on the first page of the session. A later page whose address has its own `utm_*` replaces it (a second campaign link counts); untagged pages leave it alone.
- **First touch** (`localStorage["yaww_attr_first"]`): written once. Worth keeping because a retreat is a slow decision; the booking often comes weeks after the first visit from a post or a newsletter.
- Every storage call is wrapped in `try/catch`: blocked storage (private mode, cookies off) means the fields are sent empty, never an error.
- Values are clipped to 200 characters. Nothing is ever written into a URL.
- Other forms (the waitlist form in `js/retreat-cards.js`) can read the same object with `YAWW_ATTRIBUTION.get()`, or get the hidden inputs by adding `data-attribution` to the `<form>` if they read their fields from the form.
- Consent: these are first-party values about the visit, stored in the browser and only sent when the person submits a form. If a cookie notice is added, list them there.
