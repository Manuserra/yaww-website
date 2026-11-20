# Homepage hero video: script and specs

The loop behind "Travel that moves you" on `index.html`. It is background, not a film: it sits under a scrim with the title, the three proof numbers and the scroll cue on top, it has no sound, and most people see the first 5 to 10 seconds only. So it has to work in its first shot, stay calm in the centre of the frame (where the copy is), and loop without a visible jump.

## Where we are today

- Current file: `hero-home.mp4`, **22.0 s**, 1920x1080, 30 fps, H.264 High, **18.6 MB (6.8 Mbps)**. Poster: `images/hero-home-poster.jpg` (guests swimming beside two anchored sailboats).
- 18.6 MB is roughly 3x what a hero loop should weigh, and the same file is served to phones. The spec below fixes both.
- The hero box is wider than 16:9 on desktop (at 1440x900 it shows about 1392x714, close to 2:1), so the top and bottom of every 16:9 shot are cropped. On a phone it is a tall portrait box (about 358x740 on a 390x844 phone), so only a narrow centre strip of a 16:9 shot survives. Frame for both.

## The idea

**One retreat day, from first light to golden hour, in 24 seconds.** Water, practice, the place, the table, the people, water again. It shows what the brand sells (good yoga, beautiful places, real food, a small group that gets on) without a word on screen, and it ends where it started so the loop closes on itself.

Mood: slow, warm, unhurried. Nobody performs for the camera. Think of the feeling of the first morning after you arrive, not an action reel.

## Shot list

Total **24 s** at 24 fps. Eight shots of 2.5 to 3.5 s. Times are where each shot is fully on screen; dissolves overlap the shots either side.

| # | Time | Dur. | Shot | Camera | Into next |
|---|------|------|------|--------|-----------|
| 1 | 0:00 – 0:03.5 | 3.5 s | **First light on the water.** Wide: a still bay or sea at dawn, hills or horizon in the top third, nobody in frame or one small figure. This is also the poster frame. | Locked off, or a very slow push in (2 to 3 % scale over the shot). | Dissolve 0.5 s |
| 2 | 0:03.5 – 0:06.5 | 3 s | **Practice, wide.** The group on mats outdoors or in an open shala with a view, in one held pose (warrior, tree, a slow sun salutation). Teacher not singled out. | Slow lateral slide or gentle push. Slow motion if shot at 100 fps. | Cut on movement |
| 3 | 0:06.5 – 0:09 | 2.5 s | **Practice, close.** Detail: hands rising overhead, a back breathing in child's pose, feet on a mat with the floor and light. | Static or slight drift, 25 to 40 % speed. | Dissolve 0.5 s |
| 4 | 0:09 – 0:12 | 3 s | **Into the water.** Someone sliding or diving off a boat or a rock into clear water, or swimming away from camera. The one "energetic" beat. | Locked or gentle follow; slow motion (100 fps footage conformed to 24). | Cut |
| 5 | 0:12 – 0:15 | 3 s | **The place.** Walking through an old town lane, a hillside path, or a terrace view: guests small in frame, the place doing the talking. | Slow tilt up or lateral move revealing the view. | Dissolve 0.5 s |
| 6 | 0:15 – 0:18 | 3 s | **The table.** Long shared meal outdoors: hands passing a plate, pouring, a toast. Overhead or low along the table. | Slow slide along the table. | Cut on a gesture |
| 7 | 0:18 – 0:21 | 3 s | **People.** Two or three guests laughing or talking, medium shot, natural light, not looking at the lens. | Static or slow push. | Dissolve 0.75 s |
| 8 | 0:21 – 0:24 | 3 s | **Golden hour, back to the water.** Wide, warm light on the sea or a horizon, composed like shot 1 (horizon at the same height) so the loop closes. | Locked off or a slow pull back (the mirror of shot 1's push). | **Loop dissolve 1 s into shot 1** (baked in, see below) |

Rhythm: slow, slow, quicker (shots 3 and 4 are the peak), then slowing back down to the end. No shot under 2.5 s; anything shorter reads as an ad.

## Text overlays

None. The page already lays the title, intro and proof numbers over the video. Burned-in text would collide with them and can't be translated or edited. No logo, no captions.

## Loop point

The cut from 0:24 back to 0:00 is the one viewers notice most. Make it invisible:

1. Shot 8 and shot 1 are both wide water frames with the horizon at the same height and similar exposure (grade them as a pair).
2. Bake the dissolve into the file: in the edit, put a 1 s copy of the head of shot 1 after shot 8 and dissolve into it, then export so the **last frame of the file equals the first frame**. The browser's `loop` jump then lands on an identical picture.
3. No fade to black at either end.

## What to keep out of frame

- **Hotel names, signs, logos, branded towels or menus.** Hotel names never appear on the site (Utopia is the only exception). Check every shot for signage.
- Faces looking straight into the lens, phones in hands, strong logos on clothing.
- Shaky handheld phone footage. Stabilise anything handheld (Warp Stabilizer or Resolve's stabiliser) or leave it out.
- Busy movement in the centre third of the frame: the title sits there.

## Grade

One grade across all shots: warm white balance, gentle contrast, blacks slightly lifted, saturation a touch below natural. The page lays a dark scrim on top, so avoid very dark shots (they go muddy under it) and blown highlights in the centre (they fight the white title). Match skin tones and the blue of the water between shot 1 and shot 8.

## Footage that could cover each shot

Picked from file and folder names only, without opening the files. Check every candidate by eye before you cut. The bins are at `/Volumes/SwanDucky/02_Clients/01_YAWW/0_Material/0. Video Selects Bin/` (Action, Arrival, Food, Fun, Human, Landscape, Water, Yoga). The originals are in `0. By Video/`.

**Best source: the Montenegro May 2025 camera footage** (`0. By Video/MONTENEGRO/MONTENEGRO RETREAT MAY 2025/Camera videos/`, the `C0xxx` and `yawwNNN` files, copied into the bins with a `Montenegro_` prefix). The two files I checked (`C0344`, `yaww12`) are **1920x1080 at 100 fps**: horizontal, from a proper camera, and shot for slow motion (100 to 24 fps gives a clean 4x slow-down). The whole loop can be cut from this shoot alone, which also keeps the grade consistent. The current hero (swimming by the sailboats) probably comes from here too.

| # | First choice (Selects Bin) | Alternatives |
|---|----------------------------|--------------|
| 1 First light | `Water/` or `Landscape/`: `Montenegro_yaww19.MP4`, `Montenegro_yaww218.MP4`, `Montenegro_yaww230.MP4`, `Montenegro_C0369.MP4` (each is in both bins, so likely wide water views) | `Landscape/Santorini_12565795-uhd_3840_2160_24fps.mp4`, `Santorini_12565796-uhd…` (stock, 4K) |
| 2 Practice, wide | `Yoga/Montenegro_C0344.MP4`, `C0347.MP4`, `C0348.MP4` | El Cuyo: `Yoga/ElCuyo_IMG_0055.MOV` to `IMG_0060.MOV` (a sequence) or `ElCuyo_03cd8da3-….MP4`; also `0. By Video/El Cuyo Video/By topic/Circle/`. Check these are horizontal. |
| 3 Practice, close | `Yoga/Montenegro_yaww112.MP4`, `yaww115`, `yaww122`, `yaww126`, `yaww386` to `yaww394` | Sardinia yoga in `Yoga/` (15 clips, `Sardinia_…`) |
| 4 Into the water | `Water/Montenegro_C0373.MP4`, `yaww152`, `yaww153`, `yaww155`; also `Montenegro_C0366` / `yaww145` (tagged Water + Fun) | `Water/Sardinia_…` (43 clips), `Water/Thailand_…` (16) |
| 5 The place | `Landscape/Montenegro_yaww134.MP4`, `yaww136`, `yaww378` | `Landscape/Morocco_12891366_3840_2160_24fps.mp4`, `Morocco_12891763_…` (stock 4K); the Morocco and Montenegro clips in `Arrival/` for a walking-in shot |
| 6 The table | `Food/Montenegro_C0352.MP4`, `C0377`, `C0379`, `yaww130`, `yaww173`, `yaww177`, `yaww256` | Turkey (30) and El Cuyo (43) clips in `Food/`; `0. By Video/El Cuyo Video/By topic/Comida/` |
| 7 People | `Human/Montenegro_yaww12.MP4`, `yaww178`, `yaww185`, `yaww186`, `C0349`, `C0372` | `Human/` El Cuyo (110) and Morocco (49) clips |
| 8 Golden hour | `Landscape/Montenegro_C0366.MP4`, `yaww145`; any dusk take among the shot 1 candidates | `Landscape/Santorini_8955270-uhd_3840_2160_30fps.mp4` (stock); for an El Cuyo version, `Landscape/ElCuyo_5044626_Flock_Flamingo_1920x1080.mp4` |

Notes:
- Most `ElCuyo_IMG_xxxx.MOV` files are phone clips, likely vertical. They don't suit the desktop file but can serve the **mobile** version (below).
- `0. By Video/El Cuyo Video/By topic/Reels viejos/A day on a retreat.mov` is an older edit on the same idea; worth a look as a reference.
- Stock clips (the ones with `_3840_2160_` in the name) need their licence checked before use, and they will look different from our own footage. Use them for one shot at most.
- If a shot needs to show a destination other than Montenegro, keep the opener and closer from one place so the loop still matches.

## Technical specs for the web

### Desktop file

| | |
|---|---|
| Duration | 24 s (20 to 26 s is fine) |
| Resolution | 1920x1080 |
| Frame rate | 24 fps (conform 100 fps footage to 24 for slow motion; don't mix 25/30 fps clips without conforming) |
| Codec | H.264 High, yuv420p, `+faststart` (moov atom at the front so it starts before it finishes downloading) |
| Bitrate | about 2 Mbps average, 2-pass, max 3 Mbps |
| Target size | **6 MB or less** (today: 18.6 MB) |
| Audio | none: strip the track entirely (muted autoplay needs no sound, and an empty track still costs bytes) |
| Name | `hero-home.mp4` (replaces the current one; bump the `?v=` in `index.html`) |

Optional second encode for browsers that take it: AV1 or VP9 WebM at about 1.2 Mbps (around 3.5 MB), listed first as a `<source>`.

### Mobile file

| | |
|---|---|
| Resolution | 720x1280 (9:16 portrait) |
| Framing | reframe each shot for the centre strip, don't just centre-crop blind: keep the subject inside it. Vertical phone clips can replace shots that don't reframe well. |
| Bitrate / size | about 1.2 Mbps, **3.5 MB or less** |
| Everything else | same as desktop |
| Name | `hero-home-mobile.mp4` |

Served with `<source media="(max-width: 767px)" …>` before the desktop source (Chrome, Safari and Firefox all honour `media` on video sources again). If the mobile file is ever skipped, the desktop file still works, just heavier.

### Poster frames

- Taken from **frame 1** of each file, so there is no jump when playback starts.
- `images/hero-home-poster.jpg` 1920x1080 and `images/hero-home-poster-mobile.jpg` 720x1280, JPEG quality around 75, **150 KB or less** each.
- The poster is what reduced-motion visitors see for good (the page pauses the video for them), so shot 1's opening frame must work as a still on its own.

### Example encodes (ffmpeg)

```sh
# desktop, 2-pass ~2 Mbps, no audio
ffmpeg -y -i master.mov -an -vf "scale=1920:1080,fps=24,format=yuv420p" \
  -c:v libx264 -profile:v high -preset slow -b:v 2M -maxrate 3M -bufsize 6M -pass 1 -f mp4 /dev/null
ffmpeg -y -i master.mov -an -vf "scale=1920:1080,fps=24,format=yuv420p" \
  -c:v libx264 -profile:v high -preset slow -b:v 2M -maxrate 3M -bufsize 6M -pass 2 \
  -movflags +faststart hero-home.mp4

# mobile, from a 9:16 master exported separately
ffmpeg -y -i master-vertical.mov -an -vf "scale=720:1280,fps=24,format=yuv420p" \
  -c:v libx264 -profile:v high -preset slow -crf 26 -maxrate 1.5M -bufsize 3M \
  -movflags +faststart hero-home-mobile.mp4

# poster frames (frame 1)
ffmpeg -y -i hero-home.mp4 -frames:v 1 -q:v 4 images/hero-home-poster.jpg
ffmpeg -y -i hero-home-mobile.mp4 -frames:v 1 -q:v 4 images/hero-home-poster-mobile.jpg
```

Check the result: `ffprobe -v error -show_entries format=duration,size,bit_rate hero-home.mp4`, then watch the loop point three times in a row in the browser.
