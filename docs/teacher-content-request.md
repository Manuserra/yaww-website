# What to ask each teacher for

Every teacher page now has slots that appear only when we have the content (`data/teachers.js`). Nothing shows as empty to guests. To see what a full page looks like, open any teacher page with `?slots=demo` (for example `teacher-alicia-delaney.html?slots=demo`).

Send this list to each teacher. Short answers are fine; we edit for length.

## 1. Photos (most important)
- **One vertical portrait**, at least 1600 px on the long side, face clearly visible, natural light. No sunglasses, no heavy filters. This is the main photo on her page and her card.
- **Two or three teaching photos**: leading a class, adjusting a student, outdoors. Landscape or vertical, at least 1600 px.
- Tell us who took them, so we can credit the photographer if needed.
- Urgent: Rachel Gray (no portrait at all), and Bonnie, Jamie, Yessica, Amy Sennett-Starner, Carie, Anj and Amy Appel (we only have small crops from the Canva flyers, 230 to 630 px wide).

## 2. One line in her own words (`quote`)
- A sentence she would say about how she teaches or what a class with her feels like. Under 20 words.
- Already have one: Alison, Dawn, Jamie, Yessica, Carie, Amy Appel (taken from their own bios or flyers; ask them to confirm).

## 3. Specialties (`specialties`)
- Three to five styles or focuses, one or two words each (for example: Vinyasa, Yin, Yoga nidra, Breathwork, Restorative).
- Certifications only as she wants them shown (E-RYT 200, RYT-500, YACEP...). We never add one she has not given us.
- Current lists were taken from each bio; ask each teacher to confirm or correct.

## 4. A class video (`video`)
- 1 to 3 minutes of her teaching, filmed horizontally. A real class is better than a polished promo.
- Best: she uploads it to YouTube or Vimeo (unlisted is fine) and sends us the link. We embed it; we do not host the file.

## 5. A Spotify playlist (`spotify`)
- The link to a public playlist she uses in class (Share > Copy link to playlist).
- Optional, but it is the quickest way for a guest to get a feel for her classes.

## 6. Bio check
- Her current bio as it appears on the site, for her to correct. Ask for **facts only**: years teaching, where she teaches, trainings and certifications, what a retreat day with her looks like.
- Website and Instagram handle, if she wants them linked.
- For Carie, Anj and Amy Appel the bio was written from their retreat flyers; they should see it before it goes live.

## Where it goes
| Field | data/teachers.js | Shown as |
|---|---|---|
| Portrait | `photo`, `avatar`, `photoPosition` | Card and page photo |
| Line | `quote` | Italic quote under her name |
| Specialties | `specialties: [...]` | Tags under the bio |
| Video | `video: "https://www.youtube.com/embed/..."` | "Practice with [Name]" band |
| Playlist | `spotify: "https://open.spotify.com/playlist/..."` | Next to the video |
| Bio | `bio: [...]`, `shortBio` | Page and card (keep `shortBio` at 140 to 165 characters) |
