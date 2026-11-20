# Locations: live-site copy, collected per location

Source: https://yogaadventuresworldwide.com/yoga/ and each retreat page it links to (scraped 2026-11-20 with curl, text extracted). Excerpts below are verbatim from the live site unless marked otherwise, with the source URL beside each. Hotel names removed; the one hotel named in live copy (Utopia, El Cuyo) is shown as [hotel]. Live pages hold no standalone location pages: all location copy sits on the retreat pages, so "intro" below means the hero blurb and the About section of the retreat page.

## Cross-site register (what C likes, from the /yoga/ landing and retreat pages)

Source: https://yogaadventuresworldwide.com/yoga/

> With more than a decade of epic yoga retreat planning and facilitating experience, our hand-crafted retreats offer the traveling yogi a chance to take a back seat on planning to arrive and just be.

> You can leave the planning to us! All the details have been taken care of for you so all you need to do is arrive and focus on you, your yoga practice, and your vacation. Our hand-picked destinations are based on their unique cultural expression, incredible landscapes, local food and drink, and of course the heart of any place, the people. We feel a very special connection to each and every location that we have on offer and are excited to share them with you!

Recurring wording across all pages: "hand-crafted", "hand-picked", "curated", "leave the planning to us", "soulful yoga", "anchor each day" / "round out your days", "unique cultural expression, incredible landscapes, local food and drink, and the people" (the four pillars), "we are SO excited to share", "all levels", "solo travelers join us all the time". Newer pages (Iceland, Portugal, Morocco, France, Camino) lean quieter: "intention", "unhurried", "linger", "contrast", "a shift", short fragment sentences ("Less structure. More space."), "yoga is the thread, not the focus". Older pages (Santorini, Basque, Croatia, Sardinia) are chattier: "EPIC", "fabulous", "good vibes", exclamation marks. For location pages, borrow the older pages for place facts and the newer pages for register.

## Iceland location page: subtitle in the repo

Current `locationpage-iceland.html` subtitle (`.retreat-hero__location`): "Where the road runs out and the country starts." H1: "Iceland, Reykjavik & Vik".
Does it come from the live site? No. The phrase appears nowhere on the live site (checked every retreat page). It is invented copy, which is the line C rejected (BRIEF section 5). Live Iceland wording to use instead:
- Tagline (hero): "Designed to move you." / "From Reykjavik to the wild." (https://yogaadventuresworldwide.com/retreat/yoga-retreat-in-iceland/)
- Lead: "Iceland invites you into something vast." (same URL)
- Page title on /yoga/: "The Iceland Experience : From Reykjavik to the wild"

## Locations on the live site not in data/locations.js

Croatia (Istria, Bale): https://yogaadventuresworldwide.com/retreat/croatia-yoga-food-wine-retreat/ , Napa Valley: https://yogaadventuresworldwide.com/retreat/napa-valley-wine-yoga-retreats/ . Not on the locations grid; copy available there if wanted. "Embodied: The Art of Being Seen" (El Cuyo, boudoir photography with Rachel Gray) is a themed retreat, not a place; skip for the locations page.

Blurb length target 150 to 170 characters. Lengths are noted per blurb.

---

## El Cuyo, Mexico  (`el-cuyo`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/beach-yoga-retreat-mexico/ ("Yucatan Yoga & Spa Beach Retreat in El Cuyo, Mexico")
- https://yogaadventuresworldwide.com/retreat/mini-yoga-retreats-in-paradise/ ("Mini Yoga Retreats: Beach Escapes in El Cuyo")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/beach-yoga-retreat-mexico/

> Yucatán Yoga & Spa Beach Retreat in El Cuyo, Mexico
> Yucatán Yoga & Spa Beach Retreat
> In El Cuyo, Mexico
> El Cuyo is a beach lovers dream. The tourists haven’t heard about it yet so you’ll have the beach and the town to yourself for the most part (shhhhhhhh) and the calm waters in the morning make it perfect for heading out on the flat waters to glide on your Standup Paddleboard or kayak or to just go for a beautiful morning stroll along the edge of the clear, emerald waters. We will practice yoga daily in our beachfront studio and our adorable guesthouse is steps from the sea making it the perfect escape to rest, reset, and rejuvenate! The perfect balance of doing and restoring. We’ve included all the highlights of the area plus a pampering massage with our local healer plus a mud ceremony in front of the sea.
> Wild virgin beaches at your front door; open-air yoga practices with a view; delicious local cuisine to nourish the body; connection with nature to nourish the soul. Round out your days with an adventure into these Mayan lands. Explore the amazing pink lagoons of Las Coloradas, visit flamingos in their natural habitat, check out the town by bike, hop on a SUP or a kayak early morning when the sea is still to glimpse those first morning rays of sunshine peaking over the horizon. Learn how to make tamales, sip margaritas, bask in the sunshine and swim in the Emerald Coast. Our optional day trip into the Yucatan Peninsula will take us to the colonial city of Valladolid, the ruins of Ek Balam and a breathtaking cenote to cool off after a day of exploring. We have our incredible massage therapist with us to offer a healing massage in our open air studio and we wrap it all up with a mud ceremony to cleanse and refresh your body and soul.
> El Cuyo has our hearts and the world is starting to tune in. Come experience this unspoiled paradise before the droves hit!
> Yoga Retreat Mexico : Yucatán Yoga & Spa Beach Retreat
> in El Cuyo, Mexico
> If you’re looking for a secluded beach yoga retreat in Mexico, look no further than this incredible offering in El Cuyo. This tiny fishing village located on the edge of the Yucatan peninsula is home to virgin beaches, flamingos, turtles, dolphins, countless birds and tons of sea life. Calling it paradise is not enough…the wild palm trees and dunes reach up to the edge of the sea while the gentle waves crash at the white sandy shore.  A plethora of stunning seashells line the beach and you can walk for miles without any disruptions but the kite boarders gliding by on the breeze.
> We are trying our best to keep this special spot under the radar and enjoy it as long as possible before the tourist droves hear about its charms and come to find out what all the fuss is about. El Cuyo is believed to be the last hidden treasure on the Gulf of Mexico. Please keep your lips sealed as much as you can but come join us to live this amazing paradise while it lasts.
> El Cuyo is located in the Río Lagartos nature reserve, between Holbox Island and the famous pink lagoons of Las Coloradas. It’s a quick and easy drive from Cancun airport but far enough away to really relax and take in the beach life. Your days will be spent lounging by the sea, experiencing nature at its finest and reconnecting with your inner self on your yoga mat by the sea. We can’t think of a better way to spend your retreat.

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/mini-yoga-retreats-in-paradise/

> Beach Escapes in El Cuyo
> MINI Yoga Retreats :
> Beach Escapes in El Cuyo, Mexico
> El Cuyo : A Hidden Paradise to Reset and Rejuvenate. Escape to El Cuyo, an untouched gem where the beaches are pristine, the waters are calm, and the pace of life invites you to truly unwind. Our 4-night retreat is designed to nourish your body, mind, and soul in this tranquil seaside haven.
> Start your mornings with yoga in our beachfront studio, followed by beach walks, paddleboarding, kayaking, or a serene bike ride through town. Enjoy 3 delicious meals daily, crafted with love and inspired by local flavors. Stay at the stunning [hotel], where the ocean is just steps away, creating the perfect backdrop for restoration and reconnection.
> Round out your days with delicious meals, sunshine, and a little adventure—or simply soak in the calm beauty of this untouched paradise.
> El Cuyo is calling—experience its magic before the world catches on!
> Mini Yoga Retreats in El Cuyo
> Beach Escapes in El Cuyo, Mexico
> This perfect mini-retreat escape consists of 4 nights over a long weekend to reset and rejuvenate the body, mind and spirit. Each day guests will enjoy a 75 minute yoga practice, beach walks, bike rides, standup paddle boarding and sea kayaks as well as delicious meals made with fresh, local ingredients. Accommodations at the gorgeous [hotel] will be our home base as we take a few days off to rest and reconnect.
> If you’re looking for a secluded beach yoga retreat in Mexico, look no further than this incredible offering in El Cuyo. This tiny fishing village located on the edge of the Yucatan peninsula is home to virgin beaches, flamingos, turtles, dolphins, countless birds and tons of sea life. Calling it paradise is not enough…the wild palm trees and dunes reach up to the edge of the sea while the gentle waves crash at the white sandy shore.  A plethora of stunning seashells line the beach and you can walk for miles without any disruptions but the kite boarders gliding by on the breeze.
> We are trying our best to keep this special spot under the radar and enjoy it as long as possible before the tourist droves hear about its charms and come to find out what all the fuss is about. El Cuyo is believed to be the last hidden treasure on the Gulf of Mexico. Please keep your lips sealed as much as you can but come join us to live this amazing paradise while it lasts.
> El Cuyo is located in the Río Lagartos nature reserve, between Holbox Island and the famous pink lagoons of Las Coloradas. It’s a quick and easy drive from Cancun airport but far enough away to really relax and take in the beach life. Your days will be spent lounging by the sea, experiencing nature at its finest and reconnecting with your inner self on your yoga mat by the sea. We can’t think of a better way to spend your retreat.

### Practical, season and extras

- Practical (live): "Fly into Cancun International airport (CUN)." El Cuyo is in the state of Yucatan, one hour behind Cancun (beach-yoga-retreat-mexico, Travel Tips).
- Season: not stated on live pages. Dates run Dec to Feb.

### Wording C likes here

- "beach lovers dream"
- "wild virgin beaches at your front door"
- "open-air yoga practices with a view"
- "unspoiled paradise"
- "the perfect balance of doing and restoring"
- "rest, reset, and rejuvenate"

### Grid blurb candidate (150 chars)

A tiny fishing village on the edge of the Yucatan peninsula. Virgin beaches at your front door, open-air yoga with a view and delicious local cuisine.

---

## Oaxaca, Mexico  (`oaxaca`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-retreat-day-of-the-dead-oaxaca/ ("Day of the Dead Yoga + Reverence Retreat Oaxaca")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-retreat-day-of-the-dead-oaxaca/

> Day of the Dead Yoga + Reverence Retreat Oaxaca
> Day of the Dead : Yoga + Reverence Retreat
> in Oaxaca, Mexico
> Join us for the perfectly curated bespoke yoga retreat in Oaxaca, Mexico to celebrate the soulful tradition of Dia de los Muertos.
> This unique yoga retreat in Oaxaca has been created to celebrate Mexico; its rich culture, delectable cuisine, stunning nature, artistic expression and its most reverent of holidays, the Day of the Dead. Soulful yoga practices will anchor each day along with fabulous adventures including traditional Mexican cuisine cooking class, artisanal mezcal distillery tours and tastings, excursions to visit local artisans like candle makers, rug weavers, and alebrije artists, healing massages, markets and shopping time will round out the days. Our lovely accommodations are nestled just far enough outside the city to have a reprieve from the bustle of Oaxaca but close enough to experience all it has to offer. Join us to experience the rich tradition and culture in the heart of Mexico’s culinary gem. Your souls will thank you.
> Yoga + Reverence Retreat
> Day of the Dead in Oaxaca, Mexico
> World-renowned for its bright flavors from the kitchen and for putting mezcal on the hipster mixology scene, Oaxaca City is one of our most sought-after destinations to date for a plethora of reasons. Firstly, of course, for the incredible Mexican cuisine lovingly crafted for generations over open flames and harvested fresh from the abundant fields nestled in the mountains and valleys of Oaxaca. Next, for the rich tradition of their local spirit, mezcal, made from the abundant agave plants of the region.
> Of course, for the unique artisans and craftsmanship that is richly steeped in the culture from candle makers to rug weavers and the incredibly talented artists who create the adorable alebrijes creatures. And finally, to celebrate the Day of the Dead, a time when all souls gather to remember, honor and celebrate the memories and favorites of their loved ones lost. Altars are decorated with strongly scented marigold flowers believed to lure the dead back to this realm, with cut paper flags, beautifully decorated sugar skulls, plenty of fruits and local vegetables and spices, and even their favorite libations for the spirits to enjoy!
> We honor our loved ones lost by bringing their photos and some of their favorite things to place on the altar we create together for this very sacred holiday. It’s such a different perspective on death, and we think that is so very special and important to revere in the opportunity to view death in a different way. It reminds us just how precious our time together on this plane is and to celebrate with fondness and a common bond those who have touched our lives so deeply.
> Join us for the most unique yoga retreat to celebrate Mexico, its culture, cuisine, wonders of nature, and artistic expression in Oaxaca. Soulful yoga practices will anchor each day along with fabulous adventures including traditional Mexican cuisine cooking class, artisanal mezcal distillery tours and tastings, excursions to visit local artisans like candle makers, rug weavers, and alebrije artists, healing massages, markets and shopping time will round out the days. Our lovely accommodations are nestled just far enough outside the city to have a reprieve from the bustle of Oaxaca but close enough to experience all it has to offer. Join us to experience the rich tradition and culture in the heart of Mexico’s culinary gem. Your souls will thank you.

### Practical, season and extras

- Practical (live): closest airport is Oaxaca International (OAX), also known as Xoxocotlan. Retreat is timed to Day of the Dead (late Oct to early Nov).

### Wording C likes here

- "Mexico's culinary gem"
- "soulful yoga practices will anchor each day"
- "Your souls will thank you."
- "rich tradition and culture"
- "just far enough outside the city to have a reprieve from the bustle"

### Grid blurb candidate (154 chars)

Mexico's culinary gem. Mezcal, artisans and the tradition of Day of the Dead, with soulful yoga anchoring each day just outside the bustle of Oaxaca City.

---

## Argentina (Buenos Aires & Patagonia)  (`argentina`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-retreat-argentina/ ("Argentina Yoga Retreat : Buenos Aires & Patagonia")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-retreat-argentina/

> Argentina Yoga Retreat : Buenos Aires & Patagonia
> Join us for an unforgettable 7 night adventure that begins in the vibrant and historic city of Buenos Aires and continues in the breathtaking beauty of Patagonia’s Villa La Angostura. Your journey starts with three nights in Buenos Aires, where you’ll experience the energy of this iconic city with daily yoga sessions, exquisite food and wine, and immersive city tours that capture the heart and soul of Argentine culture. Enjoy a unique retreat adventure and immerse yourself in the rich history and vibrant spirit of Buenos Aires.
> From the bustling city, we’ll fly you to the serene landscapes of Villa La Angostura in Patagonia for the next four nights. Nestled among pristine lakes and majestic mountains, this tranquil haven offers daily yoga amidst nature, stunning hikes, and opportunities to unwind and connect with the peaceful surroundings. Indulge in fresh, locally sourced cuisine and exceptional Argentine wines as you relax by the lake and explore the breathtaking trails.
> This retreat combines the best of both worlds—urban exploration and natural beauty—providing a rejuvenating journey that nourishes the body, mind, and spirit. Whether you’re seeking adventure, relaxation, or a deep connection with your yoga practice, this retreat is designed to leave you inspired and revitalized. Don’t miss this unique opportunity to experience the diverse wonders of Argentina with us!
> Argentina yoga retreat : Buenos Aires & Patagonia
> Our extraordinary 7-night yoga and adventure retreat takes you from the lively streets of Buenos Aires to the serene landscapes of Villa La Angostura in Patagonia. Our adventure begins with four days in Buenos Aires, a city bursting with life and history. Here, you’ll enjoy daily yoga practices, savor delicious Argentine cuisine paired with fine wines, and explore the city’s rich cultural heritage through guided tours, tango performances and secret speakeasys. Experience the thrill of playing polo, one of Argentina’s most cherished sports, and immerse yourself in the vibrant atmosphere of this remarkable city.
> After soaking in the dynamic energy of Buenos Aires, we’ll whisk you away to the peaceful surroundings of Villa La Angostura in Patagonia for the next four nights. This stunning retreat, set amid crystal-clear lakes and towering mountains, offers daily yoga sessions in nature’s embrace, scenic hikes, unique excursions and ample time to relax and recharge in a tranquil setting. Fresh, locally sourced meals and the best of Argentine wines nourish you daily as you unwind by the water.
> Literally the best of both worlds…we are so excited to share this amazing country with you!

### Practical, season and extras

- Practical (live): fly into Buenos Aires (EZE); domestic flight AEP to Bariloche (BRC) for Patagonia; transfer to Villa La Angostura included. March dates.

### Wording C likes here

- "Literally the best of both worlds"
- "urban exploration and natural beauty"
- "fine wines"
- "crystal-clear lakes and towering mountains"
- "we are so excited to share this amazing country with you!"

### Grid blurb candidate (154 chars)

Buenos Aires and Patagonia, the best of both worlds. Tango, polo and fine wines in the city, then crystal-clear lakes and towering mountains to unwind by.

---

## Santorini, Greece  (`santorini`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-retreat-greece/ ("Santorini Yoga Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-retreat-greece/

> Santorini Yoga Retreat
> THE BEST YOGA RETREAT
> IN SANTORINI, GREECE
> Looking for the best yoga retreat in Greece?! Then join us in Santorini for an EPIC adventure filled with soulful yoga, delicious Greek food and wines, breathtaking scenery and beaches, a stunning hike and tons of time to relax by the pool or catch up on some reading in this Grecian paradise.  Santorini is known for its breathtaking sunsets and ocean views but we show you a side of the island that is so much more than that. It’s not all honeymoons and romantic getaways!
> We stay in the best village on the island for a taste of the local culture, know all the best live music venues and nightlife spots, have the IN for reservations in the island’s best restos, and have been leading yoga retreats here since 2012. Come see what it’s all about Grecian style with us in Santorini!
> Yoga Retreat Greece
> The Best Yoga Retreat
> in Santorini
> We have been leading yoga retreats in Santorini for many years and leaving with tons of amazing memories and new friends! Come experience this amazing Greek Island adventure to the most picturesque island in the beautiful Cyclades of the Aegean archipelago.
> Of course Santorini is known for its breathtaking sunsets and ocean views but our retreats includes so much more. Incredible yoga practices with spectacular views from our private luxury villa will start off your days, followed by a bit of hiking, amazing food, delicious local wines, and beach hopping round out your days. We venture out in a fabulous catamaran out for an afternoon into evening sunset cruise finding the best hidden spots along the beautiful edges of Santorini with a bit of local wine, some music, and the ocean breeze to move us along. Add on to your week with a pampering healing massage, a cooking demonstration, or simply relax around the villa by one of the pools or explore the village of Pyrgos by foot just outside your door.
> However you plan to enjoy your days on retreat we are thrilled to have you along for the ride!

### Practical, season and extras

- Practical (live): fly into JTR (Thira Airport). Dates May and September. Hosting since 2012 ("have been leading yoga retreats here since 2012").

### Wording C likes here

- "EPIC adventure"
- "soulful yoga, delicious Greek food and wines"
- "It's not all honeymoons and romantic getaways!"
- "the best village on the island for a taste of the local culture"
- "the IN for reservations in the island's best restos"
- "Grecian paradise"

### Grid blurb candidate (156 chars)

Not all honeymoons and romantic getaways. The best village on the island, soulful yoga, Greek food and wines, and a catamaran sunset cruise with local wine.

---

## Camino de Santiago, Spain  (`camino-de-santiago`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/camino-de-santiago/ ("Camino de Santiago : A Yoga & Reflections Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/camino-de-santiago/

> Camino de Santiago : A Yoga & Reflections Retreat
> Camino de Santiago :
> A Yoga & Reflections Retreat
> For more than a thousand years, people have walked the Camino seeking perspective, gratitude, courage, or simply the experience of slowing down.
> This retreat follows the final 110 kilometers of the Portuguese Way through Galicia, Spain, weaving together daily yoga, mindful walking, restorative practices, thoughtfully chosen boutique hotels, memorable meals, and a community that shares every step of the journey. You’ll wander through ancient forests, vineyard-covered hills, and timeless stone villages, carrying only what you need while your luggage awaits at each day’s destination.
> Along the way, you’ll find space for conversation, space for silence, and space to remember what often gets lost in everyday life. It isn’t simply about reaching the destination, it’s about discovering who you become along the way.
> More than a walk, more than a retreat, this is an invitation to slow down, reconnect, and discover what unfolds when you simply keep putting one foot in front of the other.
> About this Retreat
> For more than a thousand years, people have walked across Galicia toward Santiago de Compostela. They came for all kinds of reasons — grief, gratitude, a decision they couldn’t make sitting still. What they had in common was the willingness to put one foot in front of the other and let the road do its work.
> We walk the final stretch of the Camino Portugués: roughly 110 kilometers from Vigo to Santiago, over six days of walking. Eucalyptus forests and vineyards, tidal rivers and stone villages, old bridges, hórreos, the smell of rain on granite. Then the last long day, and the city at the end of it.
> Days begin with movement — yoga on a terrace, a stretch by the water, meditation before the first kilometer. Each day closes with something restorative: gentle practice, journaling, a long dinner with the people you’ve walked beside. In between, there’s the trail. Some days we walk together and talk. One day we walk entirely in silence. Most days you’ll find your own pace, and perhaps be surprised by what shows up in it.
> Your bags travel ahead of you. Each night there’s a real bed, a good meal, and more often than not a garden, a pool, or a thermal spring waiting for you to close the day. This is a pilgrimage with soft landings — the walking is honest, and so is the rest.
> What a pilgrimage means
> A pilgrimage is a walk with intention. That’s all it has ever needed to be.
> The road doesn’t ask you to believe anything. It asks you to keep going. To carry only what you actually need. To accept the weather. To greet strangers walking in the same direction. To let the day be simple: wake, move, walk, eat, rest, repeat.
> Ten days of that changes something. Not dramatically, not all at once. but you tend to notice it on the way home.
> The rhythm of the walk
> Days in silence
> Some days we walk without speaking. No conversation, no headphones, no phone — just breath and footfall. It’s the part people are most nervous about, and the part they remember.
> Days to share
> Other days the road is social. Conversations at walking pace, shoulder to shoulder, when nobody has to hold eye contact. Long lunches, wine in a village square, circles at the beginning and the end.
> Yoga, Journaling & Meditation
> The practice here is practical and kind to a walking body: hips, hamstrings, feet, shoulders. Mornings are for warming up and waking up. Evenings are slower — restorative shapes, breath, long holds. Each day comes with a journaling prompt, and time to sit with it. Meditation is offered and always optional.
> The walking itself

### Practical, season and extras

- Season (live): "April: green, fresh and changeable. Expect 54-66°F, wildflowers, and a decent chance of rain. September: warmer and more settled, around 63–75°F, with the harvest underway and long evenings."
- Practical (live): "Vigo (VGO) is the closest airport to our starting hotel, around 20 minutes away." 6 walking days, roughly 110 km / 68 miles, luggage transferred hotel to hotel.

### Wording C likes here

- "a pilgrimage with soft landings"
- "the walking is honest, and so is the rest"
- "space for conversation, space for silence"
- "slow down, reconnect"
- "one foot in front of the other"

### Grid blurb candidate (152 chars)

The final 110 kilometers of the Portuguese Way through Galicia. Forests, vineyards and stone villages, with yoga, silence and a soft landing each night.

---

## Montenegro  (`montenegro`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-retreat-montenegro/ ("Montenegro Magic : A Yoga & Nature Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-retreat-montenegro/

> Montenegro Magic : A Yoga & Nature Retreat
> Montenegro Magic :
> A Yoga & Nature Retreat
> Escape to the stunning Bay of Kotor, Montenegro, for a transformative yoga and wellness retreat. Set in the idyllic village of Perast, this experience blends daily yoga practices with immersive adventures, creating the perfect balance of relaxation and exploration.
> Start each day with revitalizing yoga sessions overlooking the tranquil bay, then dive into a range of exciting activities. Paddle across crystal-clear waters with kayaking and stand-up paddleboarding, embark on a full-day boat trip to explore hidden gems, and discover the rich history of the medieval town of Kotor. Hike to breathtaking viewpoints for unforgettable panoramas, savor local flavors with a visit to an olive oil farm, and enjoy a unique wine-tasting experience at an underwater winery.
> Stay in the picturesque village of Perast, where cobblestone streets, historic charm, and stunning bay views provide a serene backdrop to your journey. This Montenegro retreat is designed to nourish your mind, body, and spirit while immersing you in the natural beauty and culture of this enchanting destination.
> Don’t miss the chance to experience this one-of-a-kind yoga retreat on the Bay of Kotor. Book your spot today and rediscover your sense of adventure and inner peace.
> Montenegro is a tiny country nestled into the Balkans between Croatia and Albania. Its majestic landscapes lend to epic vistas, incredible hiking, serene water activities and offer the traveler a truly unique experience. From the moment you arrive into the Bay of Kotor you’ll be transported by a medieval village overlooking the most magical waters and mystical mountain range.
> Our retreat to this magical little land highlights life on the water and all that entails. We will paddle to fantastical islands, spend a day boating around the nooks and crannies visiting caves, seaside dining spots, underwater wineries and enjoying the freshest seafood available. Our historical private villa overlooks the Bay and is nestled into the most charming village of Perast, a UNESCO heritage site. Explore the town of Kotor on our walking food tour, visit a working olive oil farm at the top of the ridge, climb 1,350 steps to the top of the city to admire the bird’s eye views.
> Yoga and wellness will anchor each day. Practices will be guided by our incredible instructors, pamper yourself in the hot tub or sauna overlooking the Bay and opt for nature bathing as you hike to the top of Kotor on the most stunning hike in the area.
> We are SO excited to share this incredible experience with you. Space is limited and this retreat will sell out quickly! Reserve your space below.

### Practical, season and extras

- Practical (live): "Tivat Airport (TIV): The closest airport, just 20 minutes from Perast."
- Season (live): "May in Montenegro offers pleasant spring weather with mild temperatures." Dates in May.

### Wording C likes here

- "Montenegro Magic"
- "stunning Bay of Kotor"
- "medieval village overlooking the most magical waters and mystical mountain range"
- "Space is limited and this retreat will sell out quickly!"
- "We are SO excited to share this incredible experience with you."

### Grid blurb candidate (154 chars)

A tiny country between Croatia and Albania. Perast on the Bay of Kotor, kayaking, an underwater winery and yoga each morning overlooking the tranquil bay.

---

## Portugal  (`portugal`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-retreat-portugal/ ("Portugal : Inner Landscape Yoga Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-retreat-portugal/

> Portugal : Inner Landscape Yoga Retreat
> Portugal : Inner Landscape
> Yoga Retreat
> A yoga retreat through Portugal’s captivating contrasts—beginning in Lisbon and unfolding along the wild, sunlit coastline of the Costa Vicentina.
> Your first days are spent in the city. Morning light over tiled rooftops. Coffee in quiet corners before the streets come alive. Yoga that grounds you before stepping out into Lisbon’s layered neighborhoods, where history, design, and culture meet at every turn. Evenings stretch over wine, shared tables, and the soft hum of a city that knows how to linger.
> A day in Sintra—misty, cinematic, and otherworldly—where castles rise from the hills and the air feels touched by something older. Then, the shift. South, where the land opens wide to the Atlantic. Salt on your skin. Wind off the cliffs. Beautiful coastal trails, hidden beaches, and optional surf. Yoga, time to unwind, and the freedom to explore at your own pace. Meals that begin at golden hour and carry on well into the night.
> Time expands here. Less structure. More space. To move, to breathe, to rest, to reconnect.
> This is Portugal, experienced in contrast—refined and wild, vibrant and deeply still—designed to stay with you long after you leave.
> Lisbon sets the tone—textured, luminous, and quietly alive with culture. Your first days unfold inside the rhythm of the city, where mornings begin with yoga and strong coffee, and afternoons slip easily into neighborhoods rich with history, design, and local life. It’s an immersion that feels both expansive and intimate.
> Then, a shift. South, toward the coast—where the landscape opens and the pace softens without effort. Cliffs meet the Atlantic. Light lingers longer. Days are shaped by salt air, coastal walks, unhurried meals, and time that feels distinctly your own.
> The transition is intentional. From city energy to open space. From stimulation to stillness. From outward experience to inward connection.
> A retreat designed not just to take you somewhere beautiful—but to move you, gradually and completely, into a different way of being.

### Practical, season and extras

- Practical (live): fly into Lisbon (LIS); 3 nights in Lisbon, 4 in Costa Vicentina (about 2.5 hours drive from Lisbon airport). May dates.

### Wording C likes here

- "captivating contrasts"
- "Time expands here. Less structure. More space."
- "refined and wild, vibrant and deeply still"
- "designed to stay with you long after you leave"
- "Meals that begin at golden hour and carry on well into the night."

### Grid blurb candidate (150 chars)

Lisbon, then the wild Costa Vicentina. Layered neighborhoods, Sintra and Fado, then salt air, hidden beaches and Atlantic cliffs, with yoga all along.

---

## Iceland (Reykjavik & Vik)  (`iceland`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-retreat-in-iceland/ ("The Iceland Experience : From Reykjavik to the wild")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-retreat-in-iceland/

> The Iceland Experience : From Reykjavík to the wild
> The Iceland Experience
> Designed to move you.
> From Reykjavík to the wild.
> Iceland invites you into something vast.
> This journey moves between Reykjavík and the wild—from geothermal waters and quiet city moments to glaciers, open landscapes, and the raw beauty of the south.
> We travel along the coast, staying in places that reflect the character of the land—simple, thoughtful, and deeply connected to their surroundings.Each day unfolds with intention, balancing movement and stillness, exploration and space to take it all in.
> Yoga becomes part of the rhythm, not the focus—a way to ground, to connect, and to deepen your experience of where you are.
> There is a quiet power here. In the scale of the landscapes. In the light. In the way time feels different.
> Shared meals, shifting skies, long drives through untouched terrain—and, if conditions align, the possibility of the Northern Lights overhead.
> This is not about doing more. It’s about experiencing it fully.
> And carrying it with you long after you leave.
> About The Iceland Experience
> This is a moving journey—designed to experience the country as it unfolds, not from a single place, but through its shifting landscapes, textures, and rhythms. From Reykjavík to the south coast, each stop reveals a different side of Iceland—raw, quiet, powerful.
> We begin with moments of ease—geothermal waters, time in the city, space to arrive. From there, the landscape opens: waterfalls, black sand beaches, glacial terrain, and the vastness that defines this place. We follow the natural flow of the land, traveling with intention and without rush.
> Along the way, you’ll experience Iceland’s culture in subtle, meaningful ways—shared meals, local flavors, and time to explore Reykjavík at your own pace.
> Yoga is woven into the experience—morning practices, evening resets, and moments to ground into where you are. It’s not the focus, but the thread that connects it all.
> This retreat is designed to move—gently, intentionally—allowing you to see more, feel more, and experience Iceland in its full depth. We travel light, stay in places that reflect the spirit of the land, and create space for both connection and independence.
> In the landscapes. In the way it stays with you.
> Who This Retreat Is For…
> This retreat is for those who want more than a place to go.
> For those drawn to landscapes that shift your perspective—and experiences that stay with you.
> You value thoughtful travel.
> You notice the details.

### Practical, season and extras

- Season (live, Travel Tips): "Iceland's weather changes literally from minute to minute." "From end of August to mid-April there is night time in the country and the lights could make their appearance." October dates.
- Practical (live): Keflavik airport is about 1 hour from the Reykjavik hotel; 8 nights; private transport; "Iceland is EXPENSIVE."
- Experiences listed live: Blue Lagoon, Golden Circle (Geysir, Gullfoss), guided Reykjavik food and culture walk, black sand beaches of Vik, Vatnajokull glacier hike, Icelandic horse farm.

### Wording C likes here

- "Designed to move you."
- "Iceland invites you into something vast."
- "There is a quiet power here."
- "This is not about doing more. It's about experiencing it fully."
- "Yoga becomes part of the rhythm, not the focus"

### Grid blurb candidate (155 chars)

From Reykjavik to the wild. Geothermal waters, glaciers, black sand beaches and the possibility of Northern Lights, with yoga as the thread through it all.

---

## France (Paris & Champagne)  (`france`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/the-paris-champagne-experience/ ("The Paris-Champagne Experience")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/the-paris-champagne-experience/

> The Paris-Champagne Experience
> A week of movement, culture, and celebration
> There’s a certain rhythm to France. Mornings that begin slowly. Afternoons shaped by movement, conversation, and curiosity. Evenings that stretch—over wine, over meals, over moments you don’t rush.
> This retreat moves through two distinct worlds.
> In Paris, days unfold with intention—morning practice, long brunches, time to wander, to shop, to sit at a café a little longer than planned. Food becomes an experience. Style becomes a language. The city holds you in its energy without asking anything in return.
> Then, a shift. Champagne softens everything.
> The pace slows. The air changes. Vineyards replace city streets. You settle into a quieter rhythm—one that invites you to exhale, to savor, to be present in a different way. Thoughtful tastings, unhurried walks, spa afternoons, and dinners that feel intimate and celebratory all at once.
> This isn’t about doing more. It’s about experiencing both sides fully—the movement and the pause,the energy and the quiet,the city and the countryside. And somewhere in between, something recalibrates.
> In Paris, you’re based on the Left Bank—where mornings begin just outside your door with cafés, bookstores, and the quiet rhythm of Saint-Germain. The hotel itself carries that same understated elegance: classic architecture, thoughtful interiors, and the kind of atmosphere that makes it feel like you’ve stepped into the city rather than just arrived in it.
> From here, the days unfold naturally—morning practice, long brunches, then out into the neighborhood or beyond. Evenings bring a different layer of Paris to life, from a walking food experience —tasting your way through its history and flavors—to a hands-on look at French cuisine that deepens your appreciation for what’s on the plate.
> There’s also a curated fashion walk through the city’s most stylish corners, offering a more intimate look at the details that define Parisian style.
> Then, in Champagne, everything softens. You settle into a modern, design-forward retreat surrounded by vineyards and forest—floor-to-ceiling windows, open views, and a sense of quiet that immediately shifts your pace.
> Days here are shaped by contrast: movement in the morning, followed by spa time—sauna, steam, and space to unwind—then out into the region for curated tastings that feel personal and unhurried. This is Champagne experienced with intention, from vineyard walks to small producer visits, each one selected to feel less like a stop and more like an invitation.

### Practical, season and extras

- Practical/season: not stated in the excerpted copy.

### Wording C likes here

- "A week of movement, culture, and celebration"
- "There's a certain rhythm to France."
- "Then, a shift. Champagne softens everything."
- "the movement and the pause"
- "unhurried walks"

### Grid blurb candidate (158 chars)

Paris, then Champagne. Long brunches and a fashion walk on the Left Bank, then vineyards, small producer tastings and spa afternoons where everything softens.

---

## Sardinia, Italy  (`sardinia`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-food-wine-adventure-sardinia/ ("Majestic Sardinia : A Yoga & Nature Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-food-wine-adventure-sardinia/

> Majestic Sardinia : A Yoga & Nature Retreat
> Majestic Sardinia :
> A Yoga & Nature Retreat
> in Sardinia, Italy
> One of the loveliest places we have visited, Sardinia kind of has it all! Its spectacular beaches with crystal clear waters, stunning vistas and dreamy nature baths, ancient ruins steeped with history, rich agriculture and undeniably incredible food, olive oils and wines make Sardinia a place that we are SO thrilled to share with you!
> We honestly had a hard time narrowing this one down but we believe we have curated a fantastic glimpse into this charming island that will wet your whistle and get you excited to learn more. Our retreat begins in the northwest corner near the Maddalena Islands, the star child of Sardinia boasting otherworldly rock formations, pristine waters and arguably the best beaches on the island. We spend three nights and three days exploring this area, enjoying the beaches, taking to the waters for a luxury boating excursion, checking out the nooks and crannies in kayaks or paddle boats and finally a day hike in the breathtaking Moon Valley at that golden hour when the light makes everything just magical.
> We feel any trip to Sardinia wouldn’t be complete without a perspective shift to the majestic mountain landscapes in the middle of the island. We fell in LOVE with the central east coast and are so thrilled to take you to our favorite farmhouse and art hotel to enjoy their authentic experiences, mouth watering cuisine, and hiking and excursions to round out the experience.
> For six nights and seven days we will have the opportunity to enjoy Sardinia as our playground for exploring, introspection, and reconnecting. This retreat is one for the record books. We do hope you’ll come along!
> We honestly had a hard time narrowing this one down but we believe we have curated a fantastic glimpse into this charming island that will wet your whistle and get you excited to learn more. Our retreat begins in the northwest corner near the Maddalena Islands, the star child of Sardinia boasting otherworldly rock formations, pristine waters and arguably the best beaches on the island. We spend three nights and three days exploring this area, enjoying the beaches, taking to the waters for a luxury boating excursion, checking out the nooks and crannies in kayaks or paddle boats and finally a day hike in the breath taking Moon Valley at that golden hour when the light makes everything just magical.

### Practical, season and extras

- Practical/season: not stated in the excerpted copy. Retreat is 6 nights, 7 days.

### Wording C likes here

- "One of the loveliest places we have visited"
- "Sardinia kind of has it all!"
- "we fell in LOVE with"
- "otherworldly rock formations, pristine waters"
- "This retreat is one for the record books."
- "that golden hour when the light makes everything just magical"

### Grid blurb candidate (153 chars)

One of the loveliest places we have visited. Crystal clear beaches near the Maddalena Islands, ancient ruins, then mountain farmhouse cuisine and hiking.

---

## Basque Country, Spain  (`spain-basque-country`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/basque-country-spain-yoga-gastronomy-retreat/ ("Basque Country Spain : Yoga & Gastronomy Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/basque-country-spain-yoga-gastronomy-retreat/

> Basque Country Spain : Yoga & Gastronomy Retreat
> Basque Country Spain :
> A Yoga & Gastronomy Retreat
> This is the best yoga retreat in Spain, so get ready to unroll your yoga mats and uncork your wine bottles because we’ve got something incredibly special curated just for you! Imagine finding yourself amidst the lush hills and coastal cliffs of the Basque Country in Spain, then savoring the world-renowned wines of Rioja—yes, it’s all happening in one extraordinary retreat. Our upcoming yoga and culinary adventure in Spain is designed to engage all your senses.
> Wake up to morning yoga sessions led by fabulous instructors, their flows as smooth as the Rioja wine you’ll sip later in the day. Picture yourself on unique excursions—whether it’s exploring the charming streets of San Sebastián or hiking through the scenic vineyards. We’ve curated the finest culinary experiences, from pintxos bar crawls to private vineyard tours, because what’s an adventure without amazing food and wine? We’re talking a complete blend of mind, body, and palate enlightenment!
> This retreat is more than just a trip; it’s a magical moment for spontaneous community filled with good vibes and fabulous times. We can’t wait for you to join us on this unforgettable adventure. Trust us, you won’t want to miss this!
> Basque Country Spain : A Yoga & Gastronomy Retreat
> Imagine waking up to the scenic vistas of Spain’s Basque Country, where rugged cliffs meet the azure Atlantic. Your mornings begin with soulful yoga sessions led by our fabulous instructors, prepping your body and mind for the adventures ahead. As the sun climbs, you’re off to explore—from San Sebastián’s picturesque beaches to the rolling vineyards of Rioja and an optional experience in Bilbao’s iconic Guggenheim Museum. But wait, there’s more!
> You’ll get to experience Spanish wine country like never before—amidst sprawling vineyards and historic wineries. Savor exclusive winery tours and, of course, tastings of some of the finest wines Spain has to offer. It’s a holistic journey for your senses, connecting your mind, body, and palate. And don’t even get us started on the food. Expect culinary delights like pintxos crawls and intimate chef-led cooking classes and super special insider connections. We even visit French Basque Country to explore the food markets, fabulous shopping and charming seaside towns.
> There’s something so fabulous about the warmth of a community brought together by a shared love for travel, food, and yoga. This is more than a retreat; it’s an adventure of a lifetime. Ready for some good vibes and unforgettable experiences? Let’s make it happen!
> Please note: the retreat is officially 6 nights long (3 nights in San Sebastian and 3 nights in wine country).
> There is an optional additional night added to the end of each retreat (making it 7 nights if you choose to add the Guggenheim experience in Bilbao).
> Dates listed on the website INCLUDE the extra night.
> For example:
> Please email us with questions or to clarify.

### Practical, season and extras

- Season (live): "WEATHER : San Sebastian can be rainy... May temps range from the high 60s during the day to high 50s at night, while October is in the low 70s during the day and the high 50s at night."
- Practical (live): "The cheapest international airport to fly into is Bilbao (BIO)... approximately one hour from our hotel in San Sebastian. Alternatively you can fly directly into San Sebastian (EAS)." 6 nights: 3 San Sebastian, 3 wine country; optional extra night in Bilbao for the Guggenheim.

### Wording C likes here

- "get ready to unroll your yoga mats and uncork your wine bottles"
- "engage all your senses"
- "mind, body, and palate"
- "good vibes"
- "super special insider connections"

### Grid blurb candidate (165 chars)

Rugged cliffs, San Sebastian and the vineyards of Rioja. Morning yoga, pintxos crawls, private vineyard tours and chef-led cooking classes for mind, body and palate.

---

## Morocco (Marrakech & Essaouira)  (`morocco`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/yoga-retreat-morocco/ ("Mesmerized in Morocco : A Yoga & Culture Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/yoga-retreat-morocco/

> Mesmerized in Morocco : A Yoga & Culture Retreat
> mesmerized in Morocco :
> a yoga & culture retreat
> Mesmerized in Morocco is a carefully composed journey through two of the country’s most compelling worlds—where design, tradition, and atmosphere take precedence over spectacle. Days are anchored by yoga and unfold through select cultural experiences: a traditional hammam and massage, privately guided medina exploration, intimate belly-dancing instruction, camel rides along the Atlantic, and a hands-on tagine cooking experience. Each moment is intentional, allowing Morocco’s texture, color, and cadence to reveal themselves naturally.
> The pace is elegant and unforced. Long meals linger, accommodations are chosen for beauty and soul, and time is left open to wander, observe, and rest. There is luxury here, but it’s understated—found in space, rhythm, and access rather than excess. Mesmerized in Morocco is designed for travelers who value depth, discernment, and experiences that feel both transporting and quietly unforgettable.
> This retreat moves between two distinct worlds—each with its own rhythm, texture, and allure. In Marrakech, the energy is layered and cinematic: lantern-lit evenings, intricate architecture, and a hum that invites you to look closer. Days are grounded by yoga in a traditional riad, with time unfolding into guided exploration, sensory rituals, and long, memorable meals. Marrakech offers contrast and intensity, balanced by moments of pause and restoration.
> In Essaouira, everything softens. The Atlantic air clears space, the pace slows, and life feels expansive and unhurried. Mornings open with practice, followed by wandering the medina, time by the sea, hammam rituals, cooking experiences, and simple pleasures that don’t ask to be scheduled. Camel rides along the beach, henna, music, and shared tables become part of the rhythm rather than highlights.
> Together, Marrakech and Essaouira create a retreat experience that feels considered and complete—energizing, restorative, and deeply atmospheric. This is Morocco experienced through access, beauty, and intention, designed for travelers who value culture, ease, and moments that linger long after the journey ends.

### Practical, season and extras

- Season (live, Travel Tips): "In Essaouira, by the coast, the days are typically breezy and perfect. You can expect weather in the 70s/20C most days. It is possible that it rains during the Spring... Fall weather is usually relatively dry. Marrakech is considerably warmer throughout the year."
- Practical (live): flights via Madrid or another European hub suggested; fly into Marrakech.

### Wording C likes here

- "carefully composed journey"
- "design, tradition, and atmosphere take precedence over spectacle"
- "There is luxury here, but it's understated"
- "considered and complete"
- "moments that linger long after the journey ends"

### Grid blurb candidate (147 chars)

Marrakech and Essaouira, two worlds with their own rhythm. Yoga in a traditional riad, a hammam, tagine cooking and camel rides along the Atlantic.

---

## Thailand  (`thailand`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/treasures-of-thailand-a-yoga-wellness-retreat/ ("Treasures of Thailand : A Yoga + Wellness Retreat")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/treasures-of-thailand-a-yoga-wellness-retreat/

> Treasures of Thailand : A Yoga + Wellness Retreat
> Treasures of Thailand :
> A Yoga + Wellness Retreat
> Uncover the treasures of Thailand through yoga, mindfulness, and cultural exploration in a retreat designed to rejuvenate your body, mind, and soul through yoga and wellness in the heart of Siam.
> Our 9 night adventure begins in the enchanting city of Chiang Mai, where ancient temples and charming streets weave together a rich tapestry of spiritual heritage. Immerse yourself in the serene beauty of northern Thailand as we venture into the countryside, exploring traditional villages and lush, verdant landscapes.
> Indulge in authentic experiences like hands-on Thai cooking classes, rejuvenating spa treatments, and ethical elephant interactions. Embark on a sacred monk’s hike to a magnificent mountaintop temple, where tranquility and breathtaking views await. This retreat offers a harmonious blend of wellness, cultural discovery, and unforgettable moments in one of Thailand’s most captivating regions.
> After days filled with cultural wonders and meaningful connections, we’ll journey south to a secluded idyllic beach front paradise on Koh Yao Noi. Here, you’ll unwind for three glorious days, basking in crystal-clear waters, white sandy beaches, and vibrant sunsets. This retreat promises an incredible blend of adventure, relaxation, and a deep dive into Thailand’s natural and cultural beauty.
> Our daily yoga practices will be thoughtfully designed to complement and deepen your connection to Thailand’s rich Buddhist culture. As we explore sacred temples and partake in the spiritual traditions, our yoga sessions will focus on mindfulness, reflection, and inner peace. Incorporating meditation and pranayama, we’ll honor the principles of Buddhism—cultivating compassion, presence, and gratitude. Each practice will be a serene space to integrate the day’s experiences, inviting you to connect with the country’s spiritual essence on a profound level.

### Practical, season and extras

- Season (live): "November is the start of the cool, dry season in Thailand, so pack light, breathable clothing, a hat, sunscreen, and comfortable walking shoes." December dates.
- Practical (live): "plan to fly into Chiang Mai CNX airport and out of Phuket HKT or Krabi KBV." 9 nights: Chiang Mai, then 3 days on Koh Yao Noi.

### Wording C likes here

- "Uncover the treasures of Thailand"
- "rejuvenate your body, mind, and soul"
- "harmonious blend of wellness, cultural discovery, and unforgettable moments"
- "in the heart of Siam"
- "secluded idyllic beach front paradise"

### Grid blurb candidate (159 chars)

Chiang Mai's ancient temples and northern villages, then a secluded beachfront on Koh Yao Noi. Thai cooking, a monk's hike and yoga shaped by Buddhist culture.

---

## Turkey (Istanbul & Cappadocia)  (`turkey`)

Live pages:
- https://yogaadventuresworldwide.com/retreat/istanbul-yoga-retreat-art/ ("Yoga & Culture Retreat Turkey : Istanbul & Cappadocia")

### Verbatim intro, https://yogaadventuresworldwide.com/retreat/istanbul-yoga-retreat-art/

> Yoga & Culture Retreat Turkey : Istanbul & Cappadocia
> Yoga & Culture Retreat turkey
> Istanbul & cappadocia
> The Beauty Between Retreat
> Istanbul is our muse. A city straddling continents and conversations—European elegance living its coolest life on the Asian shore. Think luminous mosques and modern galleries, hip coffee houses, boutique studios, the hush of a hammam, and the electricity of the Grand Bazaar. Days begin grounded with deep, soulful yoga; move into art, design, and slow wandering; and end at tables dressed in spice, meze, and excellent wine. It’s inspiration you can feel.
> Then: Cappadocia. Two nights of otherworldly calm—cave suites, sculpted valleys, and a sunrise that lifts you by hot-air balloon over rippled stone. We’ll taste local wines, hike the Red Valley, and let the silence do what only silence can. This is a retreat for seekers of beauty, culture, and connection—crafted to move you and designed to bring you home.
> What to expect
> Morning yoga that steadies the nervous system and opens the body
> Curated art + design moments: galleries, ateliers, and hidden studios
> Time for the ritual of hammam and the joy of long, leisurely meals
> Cappadocia immersion: cave dwellings, local wine, Red Valley hike, sunrise balloons
> Small group, high-touch hosting, boutique stays

### Practical, season and extras

- Practical/season: not stated in the excerpted copy; live copy covers flights landing at IST and adjusting to the time change. Two nights in Cappadocia.

### Wording C likes here

- "Istanbul is our muse."
- "The Beauty Between Retreat"
- "inspiration you can feel"
- "crafted to move you and designed to bring you home"
- "Small group, high-touch hosting, boutique stays"

### Grid blurb candidate (159 chars)

Istanbul is our muse: galleries, meze, the hush of a hammam. Then two nights in Cappadocia, a Red Valley hike and a sunrise hot-air balloon over rippled stone.

---
