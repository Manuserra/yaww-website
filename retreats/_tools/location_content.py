"""Location pages: the editorial layer.

One entry per location in data/locations.js. build_location.py reads this
and writes locationpage-<id>.html plus the grid cards on locations.html.

Sources
    docs/locations-content.md (live-site copy, verbatim, per location) and
    retreats/_tools/additional_info.py (season, getting there, pace and
    travelling on your own, already checked against each data.json).
    Live copy is reused wherever it exists: trimmed, the old pages' em
    dashes turned into commas or colons, typos fixed. New lines are written
    only where nothing exists, in the same register.

Rules (BRIEF section 0 and 5)
    No hotel or venue names (Utopia is ours, but it is not needed here).
    No tier labels. No invented business data: anything missing is a
    visible "[missing data: ...]". No em dashes.

Shape
    eyebrow   small line over the name in the hero (where it is)
    title     the H1
    tagline   the line under it, from the live site where there is one
    blurb     the caption on the locations.html grid: 3 lines at desktop
    hero      photo key (see below)
    facts     three (label, value) pairs in the hero
    intro_*   "Why we go": a heading and two paragraphs, plus a photo
    guide     four entries, one per pillar: landscape, table, culture,
              movement. img = photo key, or ph = a placeholder label
    know      four (label, text) pairs for "Good to know"
    quote     (text, name, where)

Photo keys are the end of a file name in images/retreat-photos/<id>/,
e.g. "altar-with-candles-and-marigolds". The builder finds the file, and
writes the alt text from the key and the place.
"""

LOCATIONS = {

# ------------------------------------------------------------------ El Cuyo
"el-cuyo": dict(
    eyebrow="Yucatán, Mexico",
    title="El Cuyo",
    tagline="A beach lover's dream.",
    blurb="A tiny fishing village on the Yucatán peninsula. Virgin beaches at your front door, open-air yoga with a view and delicious local cuisine.",
    hero="palm-over-the-shore-at-dusk",
    facts=[("When we go", "December to February"), ("Fly into", "Cancún (CUN)"), ("Pace", "Gently active")],
    intro_title="The last hidden treasure on the Gulf of Mexico",
    intro=[
        "This tiny fishing village on the edge of the Yucatán peninsula is home to virgin beaches, flamingos, turtles, dolphins, countless birds and tons of sea life. The wild palm trees and dunes reach up to the edge of the sea, and you can walk for miles with nothing but the kite boarders gliding by on the breeze.",
        "El Cuyo sits in the Río Lagartos nature reserve, between Holbox Island and the pink lagoons of Las Coloradas. It's an easy drive from Cancún but far enough away to really relax and take in the beach life. El Cuyo has our hearts, and the world is starting to tune in.",
    ],
    intro_img="walking-the-seafront-at-dusk",
    guide=[
        dict(kicker="Landscape", title="Pink lagoons and flamingos",
             text="Explore the pink lagoons of Las Coloradas and visit flamingos in their natural habitat. Early in the morning the sea is flat and still, perfect for a paddleboard or kayak as the first rays come over the horizon.",
             img="flamingos-in-the-lagoon"),
        dict(kicker="The table", title="Tamales and margaritas",
             text="Meals are made with fresh, local ingredients, from shrimp tostadas to the tamales you learn to make yourself in a hands-on workshop. Margaritas by the sea are part of the plan.",
             img="tamale-making-workshop"),
        dict(kicker="Culture", title="Into the Mayan lands",
             text="Round out your days with an adventure into these Mayan lands: the colonial city of Valladolid, the ruins of Ek Balam and a cenote to cool off after a day of exploring. Closer to home, the town is best seen by bike.",
             img="guests-at-the-ek-balam-ruins"),
        dict(kicker="Movement", title="Open-air yoga with a view",
             text="We practice every day in our beachfront studio, with the sea in front of us. Between classes there are beach walks, bikes, paddleboards and kayaks: the perfect balance of doing and restoring.",
             img="mats-set-out-in-the-beachfront-shala"),
    ],
    know=[
        ("When we go", "December to February. Days are usually warm, around 26 to 29°C, and evenings can feel fresh, so bring a light sweater."),
        ("Getting there", "Fly into Cancún (CUN). El Cuyo is about 2.5 hours away by car, and we can arrange a transfer. Cancún is one hour ahead of El Cuyo all year."),
        ("Pace", "Gently active. Yoga every morning and most evenings, and the rest of the day is yours: the beach, a bike ride into town, the sea when it is calm."),
        ("Coming on your own", "Solo travelers join us all the time. The group shares meals, porches and the studio of one beach house, so it is easy to settle in."),
    ],
    quote=("I can't recommend Yoga Adventures Worldwide highly enough. I went into our El Cuyo, Mexico retreat with sky-high expectations, and somehow every single one was surpassed.", "Lisa Hazen", "Google review"),
),

# ------------------------------------------------------------------ Oaxaca
"oaxaca": dict(
    eyebrow="Oaxaca, Mexico",
    title="Oaxaca",
    tagline="Mexico's culinary gem.",
    blurb="Mexico's culinary gem. Mezcal, artisans and the tradition of Day of the Dead, with soulful yoga just outside the bustle of the city.",
    hero="guests-in-catrina-make-up",
    facts=[("When we go", "Late October, for Día de los Muertos"), ("Fly into", "Oaxaca (OAX)"), ("Pace", "Gentle")],
    intro_title="One of our most sought-after destinations",
    intro=[
        "World-renowned for its bright flavors from the kitchen and for putting mezcal on the hipster mixology scene, Oaxaca City is one of our most sought-after destinations to date. Its cuisine has been lovingly crafted for generations over open flames and harvested fresh from the fields in the mountains and valleys of Oaxaca.",
        "We come for the artisans too: the candle makers, the rug weavers and the artists who create the alebrijes. And we come for the Day of the Dead, a time when all souls gather to remember, honor and celebrate their loved ones lost.",
    ],
    intro_img="marigold-garlands-on-a-doorway",
    guide=[
        dict(kicker="Landscape", title="Agave country",
             text="Mezcal is made from the agave that grows across the valleys around the city. We visit an artisanal distillery for a tour and a tasting, out where the plants grow.",
             img="agave-field-under-a-wide-sky"),
        dict(kicker="The table", title="Cooked over open flames",
             text="A traditional cooking class over the coals, tortillas from the comal and markets full of chiles, chocolate and spices. This is the heart of Mexico's culinary gem.",
             img="making-tortillas-on-the-comal"),
        dict(kicker="Culture", title="Day of the Dead",
             text="Altars are decorated with marigolds believed to lure the dead back to this realm, with cut paper flags, sugar skulls and the favorite things of those being remembered. We build one together, and you are welcome to bring photos of your own loved ones.",
             img="lighting-candles-at-the-altar"),
        dict(kicker="Movement", title="Soulful yoga, just outside the city",
             text="Soulful yoga practices anchor each day. We stay just far enough outside the city to have a reprieve from the bustle of Oaxaca, and close enough to experience all it has to offer.",
             img="practice-under-the-beamed-roof"),
    ],
    know=[
        ("When we go", "Late October into early November, timed to Día de los Muertos. Days are usually in the 70s °F, and evenings cool off once the sun goes down."),
        ("Getting there", "Fly into Oaxaca International (OAX). Send us your flight details and we can arrange your ride from the airport."),
        ("Pace", "Gentle. There is no strenuous walking, but the city streets are cobbled and uneven, so bring shoes with good grip."),
        ("Altitude", "Oaxaca City sits at about 5,000 feet. Drink plenty of water in the first days if you are sensitive to altitude."),
    ],
    quote=("Christina and her crew are magic travel masters. I would think of something and it would appear. Kind, organized and creative they offer great yoga, great food, great friends and great adventures.", "Jessica Smyser", "Google review"),
),

# ------------------------------------------------------------------ Argentina
"argentina": dict(
    eyebrow="Buenos Aires & Patagonia",
    title="Argentina",
    tagline="Literally the best of both worlds.",
    blurb="Buenos Aires and Patagonia, the best of both worlds. Tango, polo and fine wines, then crystal-clear lakes and towering mountains.",
    hero="lake-and-snowy-peaks-in-patagonia",
    facts=[("When we go", "March"), ("Fly into", "Buenos Aires (EZE)"), ("Pace", "Active, never demanding")],
    intro_title="Urban exploration and natural beauty",
    intro=[
        "Buenos Aires is a city bursting with life and history. We walk its neighborhoods, savor Argentine cuisine paired with fine wines, and find our way to tango performances and secret speakeasys.",
        "Then we fly to Villa La Angostura in Patagonia, nestled among pristine lakes and majestic mountains. Daily yoga amidst nature, scenic hikes and ample time to relax by the water before you head home.",
    ],
    intro_img="tango-dancers-in-the-street",
    guide=[
        dict(kicker="Landscape", title="Lakes and towering mountains",
             text="Villa La Angostura sits on the shore of a Patagonian lake with the Andes behind it. We kayak on the lake and walk through the old Arrayanes Forest.",
             img="clear-lake-below-the-andes"),
        dict(kicker="The table", title="Asado and fine wines",
             text="Dinner in Argentina means the asado, cooked slowly over the embers, with the best of Argentine wines poured alongside. In Patagonia the meals are fresh and locally sourced.",
             img="asado-cooking-over-the-fire"),
        dict(kicker="Culture", title="Tango and polo",
             text="In Buenos Aires we take a tango class and try polo, one of Argentina's most cherished sports, and explore the city's cultural heritage on guided tours, from the painted houses of La Boca to the Casa Rosada.",
             img="polo-players-on-horseback"),
        dict(kicker="Movement", title="Yoga in nature's embrace",
             text="Daily yoga in the city, then sessions by the lake in Patagonia, with scenic hikes and time to recharge in a tranquil setting.",
             img="triangle-pose-by-the-lake"),
    ],
    know=[
        ("When we go", "March: late summer in Buenos Aires, often in the mid to high 20s °C, and early autumn in Patagonia, with mild days and cold evenings."),
        ("Getting there", "Fly into Buenos Aires Ezeiza (EZE). We fly together to Bariloche for Patagonia, and the transfer to Villa La Angostura is included."),
        ("Pace", "Active but not demanding: city walks, a tango class, a polo lesson, a guided kayak trip and easy to moderate forest walks."),
        ("Coming on your own", "Solo travelers join us all the time. Six dinners are shared, and we pair guests to share taxis between airports."),
    ],
    quote=("YAWW always hits it out of the park. The yoga component is also great, but the curated experience includes incredible food and wine experiences, top-notch accommodations, and the opportunity to make meaningful connections with like-minded people.", "Mallory Ruymann", "Google review"),
),

# ------------------------------------------------------------------ Santorini
"santorini": dict(
    eyebrow="Cyclades, Greece",
    title="Santorini",
    tagline="Not all honeymoons and romantic getaways.",
    blurb="Not all honeymoons and romantic getaways. The best village on the island, Greek food and wines, and a sunset cruise on a catamaran.",
    hero="sunset-meditation-on-the-terrace",
    facts=[("When we go", "May and September"), ("Fly into", "Santorini (JTR)"), ("Pace", "Easy to moderate")],
    intro_title="So much more than sunsets and ocean views",
    intro=[
        "Santorini is known for its breathtaking sunsets and ocean views, and we show you a side of the island that is so much more than that. We stay in the best village on the island for a taste of the local culture, know the best live music venues and have the IN for reservations in the island's best restos.",
        "We have been leading yoga retreats here since 2012. Beyond the caldera there are black sand beaches, the ruins at Akrotiri and the trail from Fira to Oia.",
    ],
    intro_img="four-guests-above-the-caldera",
    guide=[
        dict(kicker="Landscape", title="The caldera from the water",
             text="We venture out on a catamaran for an afternoon into evening sunset cruise, finding the best hidden spots along the edges of Santorini with a bit of local wine and the ocean breeze to move us along.",
             img="leaping-from-the-catamaran"),
        dict(kicker="The table", title="Greek food and local wines",
             text="Amazing food and delicious local wines round out the days, with dinners out above the lights of the caldera. Add a cooking demonstration to your week if you would like to take a few dishes home.",
             img="greek-salad-and-dips"),
        dict(kicker="Culture", title="Pyrgos and the old island",
             text="We stay in Pyrgos, a hilltop village you can explore on foot from the door. Beyond it, the Bronze Age ruins of Akrotiri and the quieter beaches of the south.",
             img="group-at-the-akrotiri-ruins"),
        dict(kicker="Movement", title="Yoga with spectacular views",
             text="Incredible yoga practices with spectacular views start off your days, on the terrace or the rooftop of our villa. For the walkers, the hike from Fira to Oia follows the rim of the caldera.",
             img="morning-practice-on-the-terrace"),
    ],
    know=[
        ("When we go", "Late May and September. In May days are usually around 22 to 25°C, with cooler, breezy evenings, and the sea is still fresh."),
        ("Getting there", "Fly into Santorini (JTR). A shared shuttle collects the group at the airport on arrival day."),
        ("Pace", "Easy to moderate: yoga every morning, beach hopping, swimming from the catamaran and an optional hike from Fira to Oia."),
        ("Coming on your own", "Solo travelers join us all the time. Breakfast is served together every day, and shared rooms have two twin beds."),
    ],
    quote=("I have participated in four retreats with Christina and her Team (Morocco, Santorini and Mexico), and each has been incredible. The outings are perfectly curated to get a real sense of nature or culture and the food provided is more than enough.", "Margaret Blade", "Google review"),
),

# ------------------------------------------------------------------ Camino
"camino-de-santiago": dict(
    eyebrow="Galicia, Spain",
    title="Camino de Santiago",
    tagline="A pilgrimage with soft landings.",
    blurb="The final 110 kilometers of the Portuguese Way through Galicia. Forests and stone villages, yoga, silence and a soft landing each night.",
    hero="mossy-path-through-the-woods",
    facts=[("When we go", "April and September"), ("Fly into", "Vigo (VGO)"), ("The walk", "About 110 km in six days")],
    intro_title="People have walked this road for more than a thousand years",
    intro=[
        "They came for all kinds of reasons: grief, gratitude, a decision they couldn't make sitting still. What they had in common was the willingness to put one foot in front of the other and let the road do its work.",
        "We walk the final stretch of the Camino Portugués, roughly 110 kilometers from Vigo to Santiago de Compostela. Eucalyptus forests and vineyards, tidal rivers and stone villages, old bridges, hórreos, the smell of rain on granite. Then the last long day, and the city at the end of it.",
    ],
    intro_img="yellow-arrow-painted-on-a-tree",
    guide=[
        dict(kicker="Landscape", title="Forests, rivers and stone",
             text="The way runs through ancient forests and vineyard-covered hills, along tidal rías and over old stone bridges. April is green and changeable; September is warmer, with the harvest underway.",
             img="river-running-through-the-ferns"),
        dict(kicker="The table", title="A long dinner at the end of the day",
             text="Each night there is a real bed and a good meal, usually a long dinner with the people you've walked beside. Wine in a village square at lunch is part of the rhythm.",
             img="toasting-around-the-dinner-table"),
        dict(kicker="Culture", title="Santiago at the end of the road",
             text="Hórreos, waymarkers and village churches mark the way into Santiago de Compostela, where the cathedral waits at the end of the walk.",
             img="stone-house-and-horreo"),
        dict(kicker="Movement", title="Days in silence, days to share",
             text="Mornings begin with yoga on a terrace or a stretch by the water. Some days we walk together and talk; one day we walk entirely in silence. Evenings are slower, with restorative shapes, breath and a journaling prompt.",
             img="stretching-above-the-green-valley"),
    ],
    know=[
        ("When we go", "April is green, fresh and changeable, around 12 to 19°C, with a decent chance of rain. September is warmer and more settled, with long evenings."),
        ("Getting there", "The walk starts in Vigo and ends in Santiago de Compostela, so fly into one and out of the other. Vigo airport (VGO) is about 20 minutes from the first hotel."),
        ("The walk", "Six walking days, roughly 110 km. Your luggage travels ahead of you from hotel to hotel, so you carry only what you need."),
        ("Coming on your own", "Most people come on their own. The group is 12 guests at most, and dinner together most nights makes it easy to settle in."),
    ],
    quote=("Travelling with Yoga Adventures Worldwide was the perfect balance between a fun vacation and an introspective yoga retreat.", "Paula M", "Tripadvisor review"),
),

# ------------------------------------------------------------------ Montenegro
"montenegro": dict(
    eyebrow="Bay of Kotor",
    title="Montenegro",
    tagline="Life on the water in the Bay of Kotor.",
    blurb="Perast on the Bay of Kotor, between Croatia and Albania. Kayaking, an underwater winery and yoga each morning overlooking the bay.",
    hero="red-rooftops-above-the-bay",
    facts=[("When we go", "May"), ("Fly into", "Tivat (TIV)"), ("Pace", "Moderate, mostly on the water")],
    intro_title="A tiny country with epic vistas",
    intro=[
        "Montenegro is a tiny country nestled into the Balkans between Croatia and Albania. Its majestic landscapes lend to epic vistas, incredible hiking and serene water activities. From the moment you arrive into the Bay of Kotor you'll be transported by a medieval village overlooking the most magical waters and mystical mountain range.",
        "We stay in Perast, a UNESCO heritage village of cobblestone streets, historic charm and stunning bay views, and spend our days on the water and in the hills above it.",
    ],
    intro_img="bell-tower-above-the-rooftops-of-perast",
    guide=[
        dict(kicker="Landscape", title="The bay and the mountains",
             text="Paddle to fantastical islands, spend a day boating around the nooks and crannies of the bay, and hike to bird's eye views. Climb the 1,350 steps above Kotor if you are up for it.",
             img="paddleboarding-toward-the-island"),
        dict(kicker="The table", title="Oysters and an underwater winery",
             text="The freshest seafood available, a working olive oil farm at the top of the ridge and a tasting of wines that age under the sea.",
             img="serving-oysters-at-the-table"),
        dict(kicker="Culture", title="Medieval Kotor",
             text="Explore the town of Kotor on our walking food tour and discover the rich history of its medieval streets. Off Perast, the island church of Our Lady of the Rocks sits just across the water.",
             img="our-lady-of-the-rocks-island"),
        dict(kicker="Movement", title="Yoga overlooking the bay",
             text="Revitalizing yoga sessions overlooking the tranquil bay start each day. Afterwards, pamper yourself in the hot tub or sauna above the water.",
             img="dancer-pose-above-the-water"),
    ],
    know=[
        ("When we go", "May, with mild spring weather. Days are usually around 20 to 24°C and the sea is still fresh, so bring a layer for the boat."),
        ("Getting there", "Tivat (TIV) is the closest airport, about 20 minutes from Perast. Podgorica and Dubrovnik are around 1.5 to 2 hours away."),
        ("Pace", "Moderately active, mostly on the water: a paddleboard or kayak trip, a full boat day with snorkeling and a nature walk in Lovćen National Park."),
        ("Coming on your own", "Solo travelers join us all the time, and here you have a room to yourself: every room has one king bed."),
    ],
    quote=("Attending this yoga retreat in Montenegro was transformational. With the stunning mountains and the serene Adriatic Sea, every moment felt like a reset for my mind, body, and soul.", "Diana Renee", "Google review"),
),

# ------------------------------------------------------------------ Portugal
"portugal": dict(
    eyebrow="Lisbon & Costa Vicentina",
    title="Portugal",
    tagline="Refined and wild, vibrant and deeply still.",
    blurb="Lisbon, then the wild Costa Vicentina. Layered neighborhoods, Sintra and Fado, then salt air, hidden beaches and Atlantic cliffs.",
    hero="wide-beach-below-the-cliffs",
    facts=[("When we go", "May"), ("Fly into", "Lisbon (LIS)"), ("Pace", "Moderate, mostly up to you")],
    intro_title="A country of captivating contrasts",
    intro=[
        "Lisbon sets the tone: textured, luminous and quietly alive with culture. Mornings begin with yoga and strong coffee, and afternoons slip easily into neighborhoods rich with history, design and local life. Evenings stretch over wine, shared tables and the soft hum of a city that knows how to linger.",
        "Then, the shift. South, where the land opens wide to the Atlantic. Salt on your skin, wind off the cliffs, coastal trails and hidden beaches. Time expands here. Less structure. More space.",
    ],
    intro_img="walking-tour-in-a-lisbon-lane",
    guide=[
        dict(kicker="Landscape", title="The Costa Vicentina",
             text="Cliffs meet the Atlantic and the light lingers longer. Beautiful coastal trails, hidden beaches and optional surf, with the freedom to explore at your own pace.",
             img="rocks-on-the-wild-beach"),
        dict(kicker="The table", title="Meals that begin at golden hour",
             text="A walking food tour through Lisbon, long lunches on the coast and vinho verde at the table. Meals that begin at golden hour and carry on well into the night.",
             img="toasting-with-vinho-verde"),
        dict(kicker="Culture", title="Sintra and Fado",
             text="A day in Sintra, misty, cinematic and otherworldly, where castles rise from the hills. Back in Lisbon, tiled rooftops, layered neighborhoods and Fado.",
             img="quinta-da-regaleira-in-sintra"),
        dict(kicker="Movement", title="Yoga, often twice a day",
             text="Yoga grounds you before stepping out into the city, and on the coast it frames the day, often twice. Add the optional cliffside hike or a surf lesson.",
             img="yoga-class-in-the-timber-studio"),
    ],
    know=[
        ("When we go", "May. Lisbon days are often around 20 to 24°C; the coast is breezier, with cool mornings and evenings. The Atlantic stays cold for swimming."),
        ("Getting there", "Fly into Lisbon (LIS). Three nights in the city, then about 2.5 hours south to the coast, a drive that is included."),
        ("Pace", "Moderate and mostly up to you: a walking food tour, a day in Sintra, an optional 8 km cliffside hike and optional surf lessons."),
        ("Coming on your own", "Solo travelers join us all the time. Six of the seven dinners are shared, so you won't often eat alone."),
    ],
    quote=("I never had to think about the logistics but enjoy and be present in the essence of Portugal. Everyday unfolded another amazing experience.", "Navigate44707", "Tripadvisor review"),
),

# ------------------------------------------------------------------ Iceland
"iceland": dict(
    eyebrow="Reykjavík & the south coast",
    title="Iceland",
    tagline="Designed to move you. From Reykjavík to the wild.",
    blurb="From Reykjavík to the wild. Geothermal waters, glaciers, black sand beaches and the chance of Northern Lights, with yoga as the thread.",
    hero="basalt-columns-on-the-black-beach",
    facts=[("When we go", "October"), ("Fly into", "Keflavík (KEF)"), ("Pace", "Gently active, some long drives")],
    intro_title="Iceland invites you into something vast",
    intro=[
        "This journey moves between Reykjavík and the wild, from geothermal waters and quiet city moments to glaciers, open landscapes and the raw beauty of the south. Each stop reveals a different side of Iceland: raw, quiet, powerful.",
        "There is a quiet power here. In the scale of the landscapes. In the light. In the way time feels different. Shared meals, shifting skies, long drives through untouched terrain and, if conditions align, the possibility of the Northern Lights overhead.",
    ],
    intro_img="river-through-a-mossy-canyon",
    guide=[
        dict(kicker="Landscape", title="Glaciers and black sand",
             text="Waterfalls, black sand beaches, glacial terrain and the vastness that defines this place. We hike on the Vatnajökull glacier with a guide, crampons on.",
             img="hiking-across-the-glacier"),
        dict(kicker="The table", title="Local flavors, one long table",
             text="Icelandic breakfasts, a guided food and culture walk through Reykjavík, hot dogs included, and long dinners together after a day outside.",
             img="group-dinner-at-the-long-table"),
        dict(kicker="Culture", title="Horses and turf houses",
             text="A night on a horse farm, sleeping Icelandic style in a turf house, and a ride across the moor on Icelandic horses. Back in Reykjavík, time to explore the city at your own pace.",
             img="turf-house-on-the-horse-farm"),
        dict(kicker="Movement", title="Yoga as the thread",
             text="Morning practices, evening resets and moments to ground into where you are. Yoga becomes part of the rhythm here, and the geothermal waters of the Blue Lagoon do the rest.",
             img="arms-raised-among-the-ice"),
    ],
    know=[
        ("When we go", "Our last retreat ran in October, usually just above freezing to around 7°C, with weather that changes by the minute. From the end of August to mid-April the nights are dark enough for the Northern Lights."),
        ("Getting there", "Fly into Keflavík (KEF), about an hour from Reykjavík. The airport bus is simple and affordable, or we can pair you with guests on similar flights."),
        ("Pace", "Gently active, with some long drives. The guided glacier hike is for all levels; crampons and an ice axe are provided."),
        ("Budget", "Iceland is expensive. Plan a little more than usual for anything outside the retreat."),
    ],
    quote=("Iceland was incredible and I would definitely do it again! Every detail was taken care of which made for a relaxing and fun time! The yoga was great and tailored to what our group needed each day.", "Jessica Freeman", "Google review"),
),

# ------------------------------------------------------------------ France
"france": dict(
    eyebrow="Paris & Champagne",
    title="France",
    tagline="There's a certain rhythm to France.",
    blurb="Paris, then Champagne. Long brunches and a fashion walk on the Left Bank, then vineyards, small producer tastings and spa afternoons.",
    hero="pont-alexandre-iii-over-the-seine",
    facts=[("Arrive in", "Paris"), ("Then", "Champagne, by fast train"), ("Pace", "Easygoing")],
    intro_title="Two distinct worlds",
    intro=[
        "Mornings that begin slowly. Afternoons shaped by movement, conversation and curiosity. Evenings that stretch over wine, over meals, over moments you don't rush. In Paris, days unfold with intention: morning practice, long brunches, time to wander, to shop, to sit at a café a little longer than planned.",
        "Then, a shift. Champagne softens everything. The pace slows, vineyards replace city streets, and you settle into a quieter rhythm of thoughtful tastings, unhurried walks and spa afternoons.",
    ],
    intro_img="cafe-terrace-on-a-street-corner",
    guide=[
        dict(kicker="Landscape", title="Vineyards and forest",
             text="In Champagne you are surrounded by vineyards and forest, with open views and a sense of quiet that immediately shifts your pace.",
             ph="vineyard rows in Champagne"),
        dict(kicker="The table", title="Tasting your way through",
             text="A walking food experience through the history and flavors of Paris, a hands-on look at French cuisine, then small producer visits in Champagne, each one selected to feel less like a stop and more like an invitation.",
             img="bistro-chairs-and-tables"),
        dict(kicker="Culture", title="The Left Bank",
             text="We are based on the Left Bank, where mornings begin with cafés, bookstores and the quiet rhythm of Saint-Germain. A curated fashion walk takes you through the city's most stylish corners.",
             img="embroidered-dresses-on-display"),
        dict(kicker="Movement", title="The movement and the pause",
             text="Yoga every morning, in the city and in the countryside. In Champagne the afternoons belong to the spa: sauna, steam and space to unwind.",
             ph="morning practice in the Champagne countryside"),
    ],
    know=[
        ("When we go", "[missing data: month of the next dates] Whatever the season, layers and a light rain jacket are sensible in northern France."),
        ("Getting there", "Arrive in Paris; airport transfers aren't included. The fast train to Champagne is included, and so is the train to Paris CDG on the last morning."),
        ("Pace", "Easygoing: yoga every morning, walks through Paris and vineyard visits in Champagne, with a spa day to close."),
        ("Coming on your own", "It suits a solo traveler well. There is a private room made for one, and six dinners are shared with the group."),
    ],
    quote=("Don't even hesitate. These folks are pros and attract strangers who will become friends.", "Brooke Wilson", "Google review"),
),

# ------------------------------------------------------------------ Sardinia
"sardinia": dict(
    eyebrow="Italy",
    title="Sardinia",
    tagline="Sardinia kind of has it all!",
    blurb="One of the loveliest places we have visited. Crystal clear coves by the Maddalena Islands, then mountain farmhouse cuisine and hiking.",
    hero="granite-rocks-above-a-cove",
    facts=[("Fly into", "Olbia (OLB)"), ("The week", "The coast, then the mountains"), ("Pace", "Fairly active")],
    intro_title="One of the loveliest places we have visited",
    intro=[
        "Spectacular beaches with crystal clear waters, stunning vistas and dreamy nature baths, ancient ruins steeped with history, rich agriculture and undeniably incredible food, olive oils and wines. We honestly had a hard time narrowing this one down.",
        "We begin in the north, near the Maddalena Islands, the star child of Sardinia, with otherworldly rock formations, pristine waters and arguably the best beaches on the island. Then a perspective shift to the majestic mountains in the middle of the island and the central east coast, where we fell in LOVE.",
    ],
    intro_img="lawn-and-pines-by-the-sea",
    guide=[
        dict(kicker="Landscape", title="Granite, coves and the Moon Valley",
             text="Otherworldly rock formations and pristine water around the Maddalena Islands, and a hike in the Moon Valley at that golden hour when the light makes everything just magical.",
             img="moon-valley-granite-formations"),
        dict(kicker="The table", title="Farmhouse cooking",
             text="Bread from the wood oven, pasta made by hand and lunch spread out in the woods, with Sardinian olive oils and wines to go with it.",
             img="making-pasta-by-hand"),
        dict(kicker="Culture", title="Mountain villages",
             text="Ancient ruins, caves along a valley walk and a farmhouse in the mountains where we share its authentic experiences and mouth watering cuisine.",
             img="bench-under-hanging-ceramics"),
        dict(kicker="Movement", title="Sea, then mountains",
             text="Yoga on the beach and on terraces with mountain views, a luxury boating day, kayaks or paddle boats in the coves and two guided hikes.",
             img="yoga-pose-on-the-beach"),
    ],
    know=[
        ("When we go", "[missing data: month of the next dates] The second half is in the mountains, where evenings are usually cooler than by the sea."),
        ("Getting there", "Fly into Olbia (OLB). A shared shuttle leaves the airport on arrival day, and the first hotel is about an hour away."),
        ("Pace", "Fairly active: two guided hikes with easy and moderate options, and a boat day. Proper hiking boots are a must."),
        ("Coming on your own", "Solo travelers join us all the time. Most lunches and dinners are included, so you'll eat with the group most of the time."),
    ],
    quote=("This yoga retreat was a dream. We spent 3 days at a beach resort and then made our way into the Blue Zone mountains of Sardinia.", "Julie Eades", "Google review"),
),

# ------------------------------------------------------------------ Basque Country
"spain-basque-country": dict(
    eyebrow="San Sebastián & Rioja, Spain",
    title="Basque Country",
    tagline="Unroll your yoga mats and uncork your wine bottles.",
    blurb="Rugged cliffs on the Atlantic, then the vineyards of Rioja. Morning yoga, pintxos crawls in San Sebastián and private winery tours.",
    hero="beach-and-island-in-the-bay",
    facts=[("When we go", "May and October"), ("Fly into", "Bilbao (BIO)"), ("Pace", "Easygoing")],
    intro_title="Designed to engage all your senses",
    intro=[
        "Imagine waking up to the scenic vistas of Spain's Basque Country, where rugged cliffs meet the azure Atlantic. Mornings begin with soulful yoga, and then you're off to explore, from San Sebastián's picturesque beaches to the rolling vineyards of Rioja.",
        "It's a journey for mind, body and palate: pintxos crawls, intimate chef-led cooking classes and super special insider connections. We even cross into French Basque Country for the food markets and charming seaside towns.",
    ],
    intro_img="walking-a-street-of-basque-houses",
    guide=[
        dict(kicker="Landscape", title="Cliffs, then vineyards",
             text="Three nights by the sea in San Sebastián, three in wine country among the rolling vineyards of Rioja.",
             img="arriving-at-a-rioja-winery"),
        dict(kicker="The table", title="Pintxos and Rioja",
             text="A pintxos crawl through the Old Town, a cooking class in aprons and tastings among the barrels of historic wineries.",
             img="cooking-class-in-aprons"),
        dict(kicker="Culture", title="San Sebastián, Bilbao and beyond",
             text="A day tour of San Sebastián's Old Town, the hilltop town of Laguardia, and an optional extra night in Bilbao for the Guggenheim.",
             img="guests-under-a-flower-sculpture"),
        dict(kicker="Movement", title="Yoga in the vineyard",
             text="Daily yoga, mostly at a local studio in San Sebastián, and a practice among the vines in Rioja.",
             img="yoga-in-the-vineyard"),
    ],
    know=[
        ("When we go", "May and October. May runs from the high 60s °F by day to the high 50s at night; October is in the low 70s. San Sebastián can be rainy at any time."),
        ("Getting there", "Bilbao (BIO) is usually the cheapest airport, about an hour from San Sebastián. You can also fly into San Sebastián (EAS)."),
        ("Pace", "Easygoing. Yoga is daily and the rest is walking: the Old Town, a pintxos crawl, Laguardia and its vineyards."),
        ("The week", "Six nights, three in San Sebastián and three in wine country, with an optional extra night in Bilbao for the Guggenheim."),
    ],
    quote=("Our latest collaboration to Basque Country included a private tour of the Guggenheim Museum in Bilbao, a pintxo crawl in San Sebastian, wine-making in Rioja, a surprise flamenco class, and a spontaneous road trip to Biarritz.", "Genevieve Morin", "Retreat teacher"),
),

# ------------------------------------------------------------------ Morocco
"morocco": dict(
    eyebrow="Marrakech & Essaouira",
    title="Morocco",
    tagline="There is luxury here, but it's understated.",
    blurb="Marrakech and Essaouira, two worlds with their own rhythm. Yoga in a riad, a hammam, tagine cooking and camels along the Atlantic.",
    hero="essaouira-ramparts-above-the-sea",
    facts=[("When we go", "Autumn"), ("Fly into", "Marrakech (RAK)"), ("Pace", "Gently active")],
    intro_title="Two of the country's most compelling worlds",
    intro=[
        "In Marrakech the energy is layered and cinematic: lantern-lit evenings, intricate architecture and a hum that invites you to look closer. Days are grounded by yoga in a traditional riad, with time unfolding into guided exploration, sensory rituals and long, memorable meals.",
        "In Essaouira, everything softens. The Atlantic air clears space, the pace slows, and life feels expansive and unhurried. Camel rides along the beach, henna, music and shared tables become part of the rhythm rather than highlights.",
    ],
    intro_img="riad-courtyard-seating",
    guide=[
        dict(kicker="Landscape", title="The Atlantic at Essaouira",
             text="Ramparts above the sea, a breezy medina and camel rides along the beach, with time by the sea that doesn't ask to be scheduled.",
             img="camel-caravan-on-the-beach"),
        dict(kicker="The table", title="Tagine and long tables",
             text="A hands-on tagine cooking experience, grilled sardines by the sea and long meals that linger by candlelight.",
             img="couscous-cooking-class"),
        dict(kicker="Culture", title="The medina, privately guided",
             text="Privately guided medina exploration, rugs and spices in the souk, intimate belly-dancing instruction and henna.",
             img="walking-the-marrakech-souk"),
        dict(kicker="Movement", title="Yoga in a traditional riad",
             text="Days are anchored by yoga, then a traditional hammam and massage. There is a spa day, and a free day in Essaouira.",
             img="timber-ceilinged-yoga-studio"),
    ],
    know=[
        ("When we go", "Autumn is usually dry; spring can bring some rain. Essaouira is breezy, around 20°C most days, and Marrakech is considerably warmer."),
        ("Getting there", "Fly into Marrakech (RAK). Shared airport transfers are included both ways."),
        ("Pace", "Gently active. Plenty of walking on uneven medina streets; the camel ride on the beach is slow and easy."),
        ("Coming on your own", "Solo travelers join us all the time. Shared twin rooms have two beds, and every room has its own bathroom."),
    ],
    quote=("Essaouira was SO beautiful and exceeded EVERY expectation that I had. Christina and her team were so amazing! They truly went out of their way to make everyone happy and to make sure that we all made the most out of our trip.", "Amanda B", "Tripadvisor review"),
),

# ------------------------------------------------------------------ Thailand
"thailand": dict(
    eyebrow="Chiang Mai & Koh Yao Noi",
    title="Thailand",
    tagline="Uncover the treasures of Thailand.",
    blurb="Chiang Mai's ancient temples, then a secluded beach on Koh Yao Noi. Thai cooking, a monk's hike and yoga shaped by Buddhist culture.",
    hero="koh-yao-noi-bay-from-the-air",
    facts=[("When we go", "December"), ("Fly into", "Chiang Mai (CNX)"), ("Fly home from", "Phuket or Krabi")],
    intro_title="In the heart of Siam",
    intro=[
        "Our adventure begins in the enchanting city of Chiang Mai, where ancient temples and charming streets weave together a rich tapestry of spiritual heritage. From there we venture into the countryside, exploring traditional villages and lush, verdant landscapes.",
        "Then we journey south to a secluded, idyllic beach front paradise on Koh Yao Noi, for crystal-clear waters, white sandy beaches and vibrant sunsets.",
    ],
    intro_img="weathered-temple-facade",
    guide=[
        dict(kicker="Landscape", title="Limestone islands",
             text="Koh Yao Noi sits among the limestone islands of Phang Nga Bay. A longtail boat trip, kayaks and bikes, and hammocks between the palms.",
             img="limestone-island-and-a-longtail-boat"),
        dict(kicker="The table", title="Northern Thai cooking",
             text="A hands-on Thai cooking class on an organic farm outside Chiang Mai, and long dinners of northern Thai dishes.",
             img="northern-thai-dishes-on-a-table"),
        dict(kicker="Culture", title="Temples and elephants",
             text="A sacred monk's hike to a magnificent mountaintop temple, evening walks to the temples of the old city and a day with rescued elephants.",
             img="elephants-walking-the-track"),
        dict(kicker="Movement", title="Yoga shaped by Buddhist culture",
             text="Our daily practices focus on mindfulness, reflection and inner peace, with meditation and pranayama to integrate each day's experiences. On the island, yoga moves to the sea.",
             img="practice-on-the-lawn-by-the-sea"),
    ],
    know=[
        ("When we go", "December, the cool, dry season in the north. Chiang Mai days are around 28 to 30°C; Koh Yao Noi is hotter and more humid."),
        ("Getting there", "Fly into Chiang Mai (CNX) and out of Phuket (HKT) or Krabi (KBV)."),
        ("Pace", "Full days in Chiang Mai, then the pace slows on the island to beach yoga, kayaks, bikes and a half-day longboat trip."),
        ("Coming on your own", "The group is 10 guests at most and most dinners are shared. Arrive a few days early if you can: from the US the time difference is close to 12 hours."),
    ],
    quote=("The retreat I just went to in Thailand with Yoga Adventures Worldwide was top notch and hands down a 5+ star on all levels. The entire trip was so carefully curated and far exceeded my expectations.", "Sandra", "Google review"),
),

# ------------------------------------------------------------------ Turkey
"turkey": dict(
    eyebrow="Istanbul & Cappadocia",
    title="Turkey",
    tagline="Istanbul is our muse.",
    blurb="Istanbul is our muse: galleries, meze, the hush of a hammam. Then Cappadocia, a Red Valley hike and a hot-air balloon at sunrise.",
    hero="cappadocia-valley-at-sunset",
    facts=[("Fly into", "Istanbul (IST)"), ("Then", "Two nights in Cappadocia"), ("Pace", "Gently active")],
    intro_title="A city straddling continents and conversations",
    intro=[
        "European elegance living its coolest life on the Asian shore. Think luminous mosques and modern galleries, hip coffee houses, boutique studios, the hush of a hammam and the electricity of the Grand Bazaar.",
        "Then Cappadocia: two nights of otherworldly calm, with cave rooms, sculpted valleys and a sunrise that lifts you by hot-air balloon over rippled stone. We'll taste local wines, hike the Red Valley and let the silence do what only silence can.",
    ],
    intro_img="galata-tower-above-the-city",
    guide=[
        dict(kicker="Landscape", title="Cappadocia's valleys",
             text="Sculpted valleys and rock formations, a hike through the Red Valley and hot-air balloons rising at sunrise.",
             img="hot-air-balloons-at-sunrise"),
        dict(kicker="The table", title="Meze, spice and excellent wine",
             text="Days end at tables dressed in spice, meze and excellent wine. Turkish coffee from a copper pot, dough rolled thin by hand and local wines in a cave.",
             img="turkish-coffee-and-copper-pot"),
        dict(kicker="Culture", title="Art and design",
             text="Curated art and design moments: galleries, ateliers and hidden studios, from ebru marbled paper to the whirling dervishes.",
             img="ebru-marbled-paper"),
        dict(kicker="Movement", title="Deep, soulful yoga",
             text="Morning yoga that steadies the nervous system and opens the body, then slow wandering through the city.",
             img="assisted-yoga-stretch"),
    ],
    know=[
        ("When we go", "[missing data: month of the next dates] Sunrise in Cappadocia is cold whatever the month, so bring warm layers for the balloon."),
        ("Getting there", "Fly into Istanbul Airport (IST); airport transfers are included. The retreat ends in Cappadocia, so plan your flight home from there."),
        ("Pace", "Gently active: yoga most mornings, city walks, a Red Valley hike and a 4:30am start for the balloon."),
        ("Coming on your own", "Twin rooms are available in both Istanbul and Cappadocia, and we try to pair guests who would like to share."),
    ],
    quote=("Everything is carefully planned and organized making this trip the perfect vacation. The teachers and hosts are lovely and friendly.", "Paula M", "Tripadvisor review"),
),
}
