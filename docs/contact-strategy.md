# Contact: who arrives, what they want, what the page does

Brief §11. Implemented on `contact.html` (option A) and the new `teach-with-us.html`. Alternatives in `proposals/contact/`.

## What was there

`contact.html` was a port of the old site: three forms behind tabs, "General Inquiry", "Teach with us" and "Build a Custom Retreat", each with seven text fields and a "Submit" button. The live page today (`/contact-about-yoga-retreats/`) is a single sign-up form with every field required (location, dates, room preference, message) and a note to email a personal inbox if it errors. Both ask a curious person to fill in a booking.

## Who actually lands on Contact

| Visitor | What they want | Where it belongs now |
|---|---|---|
| Considering a specific retreat | One question before paying a deposit (rooms, level, solo, flights) | The retreat page's "I want more info" modal, which carries the retreat with it. Contact links there and still accepts the question. |
| Browsing, not sure where | A human opinion: "which one suits me?" | Contact form, topic "A retreat", retreat field optional. |
| Friends, family, a studio, a company | A private or custom trip | Contact form, topic "A trip for my group" (group size + rough timing). |
| Yoga teacher | To lead a retreat with YAWW | `teach-with-us.html`: a page that sells, then the application. |
| Press, partners, suppliers | An inbox | Contact, topic "Press & partnerships". |
| A booked guest | Logistics, a change | Phone line on the page (and their booking emails). |

The first two are the money. They are high-value, emotional purchases from people who want to know there is a person on the other side, so the page leads with that and asks for as little as possible.

## Recommendation (implemented): A · One human form plus doors

- Headline "We would love to hear from you" (live FAQ register), a line saying a person replies, and the direct lines (email, phone) for people who will not fill in a form.
- One form. First question: **What is it about?** A retreat / A trip for my group / Something else. Only the fields that topic needs appear. Required: first name, email, message (and group size for groups, phone if they chose WhatsApp or a call). Everything else optional.
- "Send message", not "Submit". Errors under each field in plain words. The success state names the person, repeats the email we will answer, and offers the retreats.
- **Refinements (2026-11-23, approved):** tabs "Join a retreat" · "Plan a group trip" · "Press & partners" · "Teach with us" (topic values unchanged). "Prefer to talk? +52 1 984 202 6072 · email" sits under the lede. The retreat field is a native select: "Not sure yet, help me choose" (`not-sure`, default), the upcoming editions, "Something else" (`other`). The message prompt changes per tab. "Teach with us" shows the teacher application itself (a copy of the one on teach-with-us.html, `data-yform="teach"`, same endpoint and thank-you; the two copies must match) under "Lead a retreat with us. How it works, what we handle and how to apply →".
- **Current (2026-11-23, user's call: "one column, tabs, convert, not complicate"; model: Wild Terrains' contact).** One centred column: "Contact", "We would love to hear from you", the lede, one line "Since 2008 · ★ 4.9 on Google · 45 reviews". Then the site's tabs: "A retreat" · "My group" · "Press & partners" · "Teach with us". The first three set the hidden `topic` (retreat / private-group / press), so `data-show-when` and the payload work as before; "Teach with us" swaps the form for a panel ("For yoga teachers", "Lead a retreat with us", button to teach-with-us.html). ARIA tabs with arrow keys; deep links `#private-groups`/`#group`/`#build-a-custom-retreat`, `#press`, `#teach-with-us`, default A retreat. Form max 600px, full-width Send, "Jenniferlyn writes back within 24 hours. No pressure to book." under it, phone and email in one small line at the end. Earlier versions on the board: `proposals/contact-people/` (two columns with Jenniferlyn and Christina), `proposals/contact-prev/`, `proposals/contact-alt/`. The notes below describe those earlier versions.
- 2026-11-23, after a council review ("the page promises a person but shows none"): left column is welcome, then the people who answer (Jenniferlyn Chiemingo, Guest care, "Writes back within 24 hours."; Christina Thomas, Founder, smaller; photos cropped from the About ones into `images/contact/`), the direct lines ("Phone · Mexico"), and the proof ("Since 2008", "4.9 on Google · 45 reviews", Jessica S.'s Google review). On a phone the same order, then the form.
- Form: first topic "Help me choose a retreat" (value `retreat`, default) for the biggest group, people not sure which retreat suits them; then "A trip for my group" (size in one tap, "Roughly when?" beside "Anywhere in mind?", `destination`) and "Press & partnerships" (`press`). Message placeholder "First retreat? Travelling alone? Tell us a little about you." Reply preference as plain radios (lighter, it is optional). Newsletter checkbox unticked before the button (`newsletter = yes` only when ticked); under the button "Jenniferlyn writes back within 24 hours. No pressure to book."
- Teachers: a full-width band after both columns ("For yoga teachers", "Lead a retreat with us", button "Teach with us"): each teacher brings a whole group.
- Success: "Thank you, {name}. Jenniferlyn will write back to {email} within 24 hours." Same words in the retreat pop-up.
- Previous versions on the board: `proposals/contact-prev/` (trust column, teachers as one line) and `proposals/contact-alt/` (one column).
- Old anchors keep working: `#teach-with-us` redirects to the teacher page, `#build-a-custom-retreat` and `#private-groups` preselect the group topic, `#general-inquiry` the retreat topic.

Why this over the others: one form is one thing to build, route and answer, and the topic chips do the triage a human would otherwise do. It does not compete with the retreat-page modal (that one is attached to a retreat; this one is for everything else).

## Build a custom retreat: a topic, not a page

The old site's own history shows it is real (press: "helps small groups find a house", "customizable retreats"), but the live site no longer promotes it and there is no product page to send people to. A dedicated page would promise something we have not defined (minimum group, price floor, destinations). So: it lives as a topic in the form, named in the visitor's words ("A trip for my group"), with group size as the one qualifying field. If those leads turn out to be frequent and good, it earns a page.

## Teach with us: its own page

Teachers are a supply-side audience: each one brings a group, so one good teacher is worth many single bookings. They need to be sold before they are asked for anything. The page: why YAWW (live-site figures: since 2008, 550+ retreats, 4,500 yogis), how it works in five steps, who does what (the homepage module's four promises spelled out), two real teacher quotes (Genevieve Morin and Jenniferlyn Chiemingo, from their Google reviews), FAQ, and the application. The hero is the homepage "For teachers" module, so the click lands on the same picture and promise.

Application fields and why:
- First/last name, email (required), phone (optional): to reply and to book the call.
- Where you teach (required): city or studio is enough; tells us the market.
- Website or Instagram (optional): the fastest way to see someone teach.
- Led a retreat before? (required, 3 chips): sets the tone of the call.
- How many students might travel with you? (required, 5 chips incl. "Not sure yet"): the main business qualifier, asked as a range so nobody has to commit to a number.
- Where would you love to go? (optional, multi): starts the destination conversation.
- When? (optional): 2027 / 2028 / flexible.
- Your teaching and your students (required textarea): the one open question.

## Questions for Christina (visible on the pages as `[missing data: …]`)

1. Which email address goes on the contact page? The live page only shows a personal inbox.
2. Is +52 1 984 202 6072 (live footer) right for guests, and does it take WhatsApp?
3. Teacher FAQ: minimum students a teacher brings, how teachers are paid, typical lead time.
4. Do we want private group trips at all? If not, remove the topic and the door.

## Wiring

- Endpoints: `window.YAWW_CONTACT.endpoints.contact` / `.teach` in `data/contact.js`. While `null`, a valid submit logs the payload to the console and shows success.
- Payload: snake_case field names, plus `form` and `page`. Checkbox groups arrive as arrays; a lone checkbox (`newsletter`) as `"yes"`. Attribution fields (`utm_*`, `referrer`, `landing_page`, `first_*`) are added on submit: see `docs/lead-form.md`, "Attribution".
- Events: `yaww:track` with `form_submit` / `form_invalid` and the `topic`.
- Other pages' menus and footers still say "General Inquiry / Teach with us / Build a Custom Retreat" and link to `contact.html#…`. They work through the redirects; the labels should become "Ask a question / Private groups / Teach with us" (done on contact.html and teach-with-us.html) when their owners next touch them.
