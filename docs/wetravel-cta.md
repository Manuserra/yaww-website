# Where "Reserve your spot" lives (WeTravel)

## Recommendation: both, with different jobs

1. **One primary CTA for the section**, "Reserve your spot", in the sticky "How to book" rail beside the rooms (and "Book now" in the hero and the sticky bar, which scroll to the rooms). This is the CTA that always works: it opens the retreat's WeTravel trip page, where the guest picks the room in WeTravel's own checkout.
2. **A quiet "Reserve" link on every room card**, and "Reserve this room" in the room modal, carrying the room the guest was looking at. This is the shortest path for someone who has already decided, and it is where the intent is strongest (they chose a specific room and price).

Not "only per card": six identical buttons in a list add noise, and the section needs one obvious next step for the reader who has not compared rooms yet. Not "only in the section": the reader who opened a room's photos and decided would have to scroll back and pick the same room again in WeTravel.

## What depends on WeTravel

| Behaviour | Needs from WeTravel | Status |
|---|---|---|
| Section CTA opens the trip's checkout | A public trip URL per edition (`https://www.wetravel.com/trips/...`) | Not known yet. Set `window.YAWW_WETRAVEL = { url: "…" }` on the page (or have build_page.py write it from data). |
| Card / modal CTA opens checkout **with that room already selected** | A per-package (per room type) deep link or URL parameter. WeTravel trips are sold as "packages" or "options"; whether a link can preselect one depends on the account's checkout and embed settings. | **To confirm with WeTravel.** If it exists, add `rooms: { "Room name": "url" }` to `YAWW_WETRAVEL` and each card links straight to it. |
| Checkout inside the page (embedded widget / modal) instead of a new tab | WeTravel's embed / button script | Possible later; the links already open in a new tab, so nothing else on the page changes. |
| Fallback when the room cannot be preselected | Nothing | Card CTAs open the trip page (same as the section CTA). The guest picks the room again. |

## What is implemented now

There is no WeTravel URL in the project yet, so every Reserve CTA (section, card, room modal) opens the lead form in its "Reserve your spot" wording, with the room filled in when there is one (`intent = reserve`, `room = …`, see `docs/lead-form.md`). The booking intent is captured instead of being sent to a contact page.

The moment `window.YAWW_WETRAVEL = { url: "…" }` is set (before `js/retreat-rooms.js` loads), `js/retreat-rooms.js` switches the section CTA (`[data-wetravel]`), every card's "Reserve" and the modal's "Reserve this room" to real links to WeTravel, opening in a new tab; "I want more info" and "Ask about this room" keep opening the lead form.

Alternatives considered for the rooms layout itself (two-up cards, a comparison table) are on the board: `proposals/rooms/`.
