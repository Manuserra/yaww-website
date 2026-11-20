"""Additional info: the guest-facing questions in each retreat page's
"Additional info" accordion.

Source
    Every fact comes from the retreat's own data.json (about_body, includes,
    not_included, itinerary, travel_tips, faqs, rooms, accommodations,
    pricing_notes), from page_content.TRAVEL_TIPS, or from the live retreat
    page at the data.json source_url (read 2026-11-20). Weather lines add
    only general, modest climate knowledge for the destination and month.
    Anything a guest needs that the sources do not give is left as a visible
    "[missing data: ...]" placeholder rather than guessed.

What this covers, and what it leaves out
    How active the days are, the weather at that time of year, getting
    there (short, pointing to the page's Travel Tips where the page has
    them), what to pack, coming on your own, and retreat-specific questions
    (Camino luggage, Iceland northern lights, Thailand's two venues and
    internal flight, the El Cuyo boudoir edition, and so on). Booking terms
    (deposit, balance, cancellation, payment methods, roommate rules) are
    generated separately from data the page already holds, so they are not
    written here.

Copy rules
    English, second person, plain and warm, short sentences. No em dashes.
    No hype words. No hotel or venue names except Utopia (ours, El Cuyo).
    4 to 6 questions per retreat, each answer 1 to 3 short paragraphs and
    25 to 90 words in total. Plain strings, no HTML.

Shape
    ADDITIONAL_INFO = {retreat_id: [(question, [paragraph, ...]), ...]}
    One key per retreat directory in retreats/, plus "morocco" for the
    dateless Morocco page.
"""


# ------------------------------------------------------------------ El Cuyo

_CUYO_WEATHER = {
    "December": [
        "Days are usually warm and sunny, around 27 to 29°C, and evenings "
        "are mild. Now and then a northerly wind blows through for a day or "
        "two, which cools things down and makes the sea choppier. A light "
        "layer for the evenings is all you need.",
    ],
    "New Year": [
        "Late December into early January is usually warm and sunny, around "
        "26 to 28°C in the day, with fresher evenings. Now and then a "
        "northerly wind blows for a day or two and makes the sea choppier. "
        "Bring a light sweater for after sunset.",
    ],
    "January": [
        "January is one of the cooler months on the Yucatán coast. Days are "
        "usually warm, around 26 to 28°C, and evenings can feel fresh, so "
        "bring a light sweater. A northerly wind sometimes blows for a day "
        "or two and makes the sea choppier.",
    ],
    "February": [
        "Days are usually sunny and warm, around 27 to 29°C, with a steady "
        "sea breeze. Evenings are mild, though a light layer is nice after "
        "sunset. Northerly winds still blow through now and then and can "
        "cool things down for a day.",
    ],
}

_CUYO_PACK = (
    "What should I pack?",
    [
        "Light, casual clothes, a swimsuit, sun protection and a hat. The "
        "studio has mats, blankets, straps and blocks, so you only need your "
        "own mat if you prefer it.",
        "Bring Mexican pesos. There are no ATMs in El Cuyo and most places "
        "prefer cash. Pack any medication you need too: for anything "
        "specific, the nearest option is Tizimín, about an hour and a half "
        "away.",
    ],
)

_CUYO_SOLO = (
    "Can I come on my own?",
    [
        "Yes. Utopia is one beach house with eight bedrooms, so the group "
        "shares meals, porches and the studio, and it is easy to settle in. "
        "Shared rooms have two twin beds if you'd like to room with another "
        "guest.",
        "Good to know: every room shares a bathroom. The house has three full "
        "bathrooms, a double-sink wash area upstairs and an outdoor shower on "
        "the beach.",
    ],
)

_CUYO_GETTING_THERE = (
    "How do I get there?",
    [
        "Fly into Cancún (CUN). El Cuyo is about 2.5 hours away by car, and "
        "we can arrange a transfer and pair you with guests on similar "
        "flights. Cancún is one hour ahead of El Cuyo all year, so check the "
        "times when you book. The Travel Tips section has the bus option "
        "and more.",
    ],
)

_CUYO_WIFI = (
    "Will I have wifi?",
    [
        "Some, but don't count on it. The wifi and the electricity can cut "
        "out at times, though they usually come back quickly. If you need a "
        "reliable connection for work, tell us before you book so we can "
        "talk it through.",
    ],
)


def _el_cuyo(month):
    return [
        ("How active are the days?", [
            "Gently active. There is yoga every morning and on most evenings, "
            "and the rest of the day is yours: the beach, a bike ride into "
            "town, a kayak or paddleboard when the sea is calm.",
            "The longest day is the optional trip to Valladolid, the Ek Balam "
            "ruins and a cenote, which runs from 9:30am to 9pm.",
        ]),
        (f"What will the weather be like in {month}?" if month != "New Year"
         else "What will the weather be like over New Year?",
         _CUYO_WEATHER[month]),
        _CUYO_PACK,
        _CUYO_SOLO,
        _CUYO_GETTING_THERE,
        _CUYO_WIFI,
    ]


_EL_CUYO_MINI = [
    ("How is the mini retreat different from the full week?", [
        "It is shorter and simpler: 4 nights over a long weekend, with one "
        "75-minute yoga class each morning and fewer set plans. Breakfast is "
        "daily, plus 2 lunches and 2 dinners together; the other meals you "
        "find in the village.",
        "The mud ceremony on the beach is included, and there is an optional "
        "flamingo excursion and a bike ride around town.",
    ]),
    ("What will the weather be like in late January?", [
        "Late January is one of the cooler times on the Yucatán coast. Days "
        "are usually warm, around 26 to 28°C, and evenings can feel fresh, "
        "so bring a light sweater. A northerly wind sometimes blows for a "
        "day or two and makes the sea choppier.",
    ]),
    _CUYO_PACK,
    _CUYO_SOLO,
    _CUYO_GETTING_THERE,
    _CUYO_WIFI,
]

_EL_CUYO_BOUDOIR = [
    ("Do I need experience in front of a camera?", [
        "No. On day 2 there is a playful group photo session on the beach to "
        "get comfortable with the camera. Your own 90-minute session with "
        "Rachel comes later in the week, after a few days of yoga have "
        "helped you settle in.",
    ]),
    ("Do I have to be nude?", [
        "No. Nudity is never required. The only optional form on this "
        "retreat is toplessness, and you choose your level of comfort for "
        "every image.",
        "You can be photographed on your own or with a partner.",
    ]),
    ("What do I take home?", [
        "After your session you sit down with Rachel to choose your favorite "
        "images. Five professionally retouched images are delivered "
        "digitally 10 to 12 weeks after the retreat. Additional prints are "
        "available at extra cost.",
    ]),
    ("How active are the days?", [
        "Gentle. There is yoga every morning, an evening practice on some "
        "days, and plenty of free time for the beach, massages and rest. "
        "You'll also join a sunset bike ride around town and a temazcal and "
        "cacao ceremony.",
    ]),
    ("What will the weather be like in February?", _CUYO_WEATHER["February"]),
    ("How do I get there?", [
        "Fly into Cancún (CUN). El Cuyo is about 2.5 hours away by car, and "
        "we can arrange a transfer and pair you with guests on similar "
        "flights. Cancún is one hour ahead of El Cuyo, so check the times "
        "when you book.",
        "Bring Mexican pesos: there are no ATMs in El Cuyo and most places "
        "prefer cash.",
    ]),
]


# ------------------------------------------------------------------ Camino

def _camino(month):
    weather = {
        "April": [
            "April in Galicia is green, fresh and changeable, usually around "
            "12 to 19°C (54 to 66°F). Expect rain at some point, so a proper "
            "waterproof jacket is worth its space. Mornings are cool and "
            "afternoons can be warm, so layers do most of the work.",
        ],
        "September": [
            "September is usually warmer and more settled, around 17 to 24°C "
            "(63 to 75°F), with the grape harvest underway. Rain is still "
            "possible in Galicia, so pack a proper waterproof jacket. "
            "Mornings are cool and afternoons can be warm, so dress in "
            "layers.",
        ],
    }[month]
    return [
        ("How long are the walking days?", [
            "There are six walking days, roughly 110 km from Vigo to "
            "Santiago. They range from 8 km (about 2 hours) to 25 km (about "
            "7 hours) on the last day into Santiago; most are between 14 and "
            "22 km. Day 6 is a full rest day in Caldas de Reis.",
            "The trail is gentle. If a stretch is too much, a taxi or Uber "
            "can take you ahead to the next hotel, at your own cost.",
        ]),
        ("What happens to my luggage?", [
            "Your suitcase goes to reception by 8am and is taken on to the "
            "next hotel, so you only carry a day pack. In it you'll want "
            "water, a rain layer, sun protection, snacks and blister care.",
        ]),
        (f"What will the weather be like in {month}?", weather),
        ("What should I pack?", [
            "Walking shoes you have already broken in, ideally two pairs, "
            "and more socks than seems reasonable. Trail runners or light "
            "hiking shoes are enough; heavy boots aren't needed.",
            "Add a swimsuit for the pools and thermal baths, comfortable "
            "slides for the evenings and one nicer outfit for the farewell "
            "dinner. We send a full packing list before the retreat.",
        ]),
        ("Can I come on my own?", [
            "Yes, most people do. Shared rooms have two separate beds, and on "
            "a few nights you'll have a private room anyway. The group is "
            "12 guests at most, and dinner together most nights makes it "
            "easy to settle in.",
        ]),
        ("How do I get there?", [
            "The walk starts in Vigo and ends in Santiago de Compostela, so "
            "book into one and out of the other. Vigo airport is about 20 "
            "minutes from the first hotel; from Porto it is 2 to 2.5 hours "
            "by train or bus. Arrive a day or more early if you can. The "
            "Travel Tips section has the details.",
        ]),
    ]


# --------------------------------------------------------------- Santorini

def _santorini(weather_q, weather):
    return [
        ("How active are the days?", [
            "Easy to moderate. Yoga is every morning at the villa. The active "
            "parts are beach hopping on the southern island tour, swimming "
            "from the catamaran and an optional hike from Fira to Oia on day "
            "6, which fills the late morning and afternoon.",
            "The rest of the time is free for the pool, a massage or the "
            "village.",
        ]),
        (weather_q, weather),
        ("What should I pack?", [
            "Swimwear, sun protection and a hat, plus comfortable shoes with "
            "good grip for the hike and the island's steps and paths. Add "
            "something a little nicer for the dinner out in town, and a "
            "light layer for the evening on the boat.",
        ]),
        ("Can I come on my own?", [
            "Yes. The property is four smaller villas with several bedrooms "
            "each, and breakfast is served together every day. If you'd like "
            "to share, the shared double room has two twin beds.",
            "Good to know: many rooms share a bathroom with one other room. "
            "Each room's listing says which.",
        ]),
        ("How do I get there?", [
            "Fly into Santorini (JTR). A shared shuttle collects the group at "
            "the airport at 3pm on arrival day and leaves the villa for the "
            "airport at 9am on the last morning. If your flights don't fit "
            "those times, we can arrange a private transfer at extra cost.",
        ]),
    ]


_SANTORINI_MAY = _santorini("What will the weather be like in May?", [
    "Late May is usually warm and sunny, around 22 to 25°C, with cooler, "
    "breezy evenings. The sea is still on the fresh side for swimming. "
    "Bring a light layer for the sunset cruise and the evenings.",
])

_SANTORINI_SEP = _santorini("What will the weather be like in September?", [
    "September is usually hot and dry, around 25 to 28°C, and the sea is at "
    "its warmest after the summer. Evenings stay mild, though it can be "
    "windy on the water, so bring a light layer for the sunset cruise.",
])

_SANTORINI_LATE_SEP = _santorini(
    "What will the weather be like in late September?", [
        "Late September into early October is usually warm and dry, around "
        "23 to 26°C, and the sea is still warm from the summer. Evenings "
        "cool off a little and it can be windy on the water, so bring a "
        "light layer for the sunset cruise.",
    ])


# ------------------------------------------------------------- Montenegro

_MONTENEGRO = [
    ("How active are the days?", [
        "Moderately active, mostly on the water. There is an early "
        "paddleboard or kayak trip to the island church, a full boat day "
        "with snorkeling and a nature walk in Lovćen National Park.",
        "In Kotor you can climb the 1,350 steps to the fortress if you like. "
        "The villa's sauna, jacuzzi and pool balance it out.",
    ]),
    ("What will the weather be like in May?", [
        "May is mild spring weather in the Bay of Kotor. Days are usually "
        "around 20 to 24°C, mornings and evenings are cooler, and the sea is "
        "still fresh. Bring a light jacket or sweater for the evenings and "
        "the boat.",
    ]),
    ("What should I pack?", [
        "Swimwear for paddling, snorkeling and the boat day, and hiking shoes "
        "or sturdy sneakers for the trails and viewpoints. Add sun "
        "protection, a reusable water bottle and a day pack. Outlets are "
        "European, type C or F.",
    ]),
    ("Can I come on my own?", [
        "Yes, and you'll have a room to yourself. Every room has one king bed "
        "that can't be separated, so sharing is only for guests who book "
        "together.",
        "Once the villa is full, the group spills over into a nearby hotel, "
        "so ask about availability when you're ready to reserve.",
    ]),
    ("How do I get there?", [
        "Tivat (TIV) is the closest airport, about 20 minutes from Perast. "
        "Podgorica and Dubrovnik are around 1.5 to 2 hours away, and "
        "Dubrovnik means a border crossing. Transfers aren't included; send "
        "us your flights and we'll pair you with others arriving at similar "
        "times. See Travel Tips for more.",
    ]),
]


# ---------------------------------------------------------------- Thailand

_THAILAND = [
    ("Why are there two places to stay?", [
        "The retreat moves halfway through. You spend 5 nights in Chiang "
        "Mai's Old City, then 4 nights on the island of Koh Yao Noi in the "
        "south. On day 6 you fly from Chiang Mai to Phuket, and a private "
        "transfer takes the group on to the island.",
    ]),
    ("Do I book the internal flight myself?", [
        "Yes, it isn't included. For this retreat, book Thai AirAsia FD 3162 "
        "on December 6, 2026, leaving Chiang Mai at 12:40pm and arriving in "
        "Phuket at 2:50pm.",
        "For your flight home, leave from Phuket or Krabi after 1pm to allow "
        "for the boat from the island. Ferries depend on the weather but "
        "usually run on time.",
    ]),
    ("How active are the days?", [
        "The Chiang Mai days are full: a guided temple hike, a day with "
        "rescued elephants, a cooking class on an organic farm and evening "
        "walks to the temples. [missing data: length and difficulty of the "
        "temple hike]",
        "On the island the pace slows to beach yoga, kayaks, bikes and a "
        "half-day longboat trip.",
    ]),
    ("What will the weather be like in December?", [
        "December is the cool, dry season in the north. Chiang Mai days are "
        "usually warm, around 28 to 30°C, and early mornings and evenings "
        "can be cool, so bring a light layer. Koh Yao Noi in the south is "
        "hotter and more humid, usually around 30 to 32°C.",
    ]),
    ("What should I pack?", [
        "Light, breathable clothes, a hat, sunscreen and comfortable shoes "
        "for temples, markets and the trail. At temples you need shoulders "
        "and knees covered and you take your shoes off, so easy slip-ons "
        "help.",
        "Check the baggage allowance on your internal flight, as bag fees "
        "aren't included.",
    ]),
    ("Can I come on my own?", [
        "Yes. The group is 10 guests at most and most dinners are shared. "
        "Shared rooms are available at both hotels; on Koh Yao Noi the "
        "shared room has one queen and one twin bed. Arrive a few days early "
        "if you can, as the time difference from the US is close to 12 "
        "hours.",
    ]),
]


# ----------------------------------------------------------------- Morocco

def _morocco(weather_q, weather):
    return [
        ("How active are the days?", [
            "Gently active. Yoga is daily, and the outings are on foot "
            "through the medinas, souks and gardens, so expect plenty of "
            "walking on uneven streets. The camel ride on the beach is slow "
            "and easy, and there is a spa day and a free day in Essaouira.",
        ]),
        (weather_q, weather),
        ("What should I wear and pack?", [
            "Clothes that reach the knee and cover the shoulders help you "
            "avoid unwanted attention. Pack long pants for the camel ride, a "
            "swimsuit for the spa day and pool, good walking shoes, "
            "sunglasses and a sun hat.",
            "Bring a universal adaptor for the European two-prong outlets. "
            "Mats and props are provided.",
        ]),
        ("Can I come on my own?", [
            "Yes. You're with the group for the guided outings and most "
            "dinners, and the shared twin rooms have two beds if you'd like "
            "to room with another guest. Every room has its own ensuite "
            "bathroom.",
        ]),
        ("Will there be time to shop?", [
            "Plenty. The guided day in Marrakech ends in the souks with free "
            "time, and there are free afternoons in both medinas. A "
            "collapsible duffle bag in your suitcase is a good idea, or you "
            "can buy one in the souks.",
        ]),
        ("How do I get there?", [
            "Fly into Marrakech (RAK). Shared airport transfers are included "
            "both ways. The return shuttle leaves Essaouira at 9am on the "
            "last day and you should allow about 5.5 hours to your flight, "
            "so book one leaving no earlier than about 2:30pm. See Travel "
            "Tips for more.",
        ]),
    ]


_MOROCCO_NOV = _morocco("What will the weather be like in November?", [
    "In November, Essaouira on the coast is usually breezy and mild, around "
    "20°C. Marrakech is warmer in the day. Autumn is usually dry, but "
    "evenings cool down in both cities, so bring a warm layer.",
])

_MOROCCO_DATELESS = _morocco("What will the weather be like?", [
    "Essaouira on the coast is usually breezy, around 20°C most days, and "
    "Marrakech is considerably warmer. Autumn is usually dry; spring can "
    "bring some rain, so a light rain jacket helps. Check the forecast once "
    "your dates are set.",
])


# ------------------------------------------------------------------ Oaxaca

_OAXACA = [
    ("How active are the days?", [
        "Not very. There is no strenuous walking on this retreat. You'll be "
        "on your feet for the food tour and the artisan visits, and the city "
        "streets are cobbled and uneven, so sandals or sneakers with good "
        "grip are best. Yoga is most mornings at the villa.",
    ]),
    ("What will the weather be like in late October?", [
        "Days are usually pleasant, in the 70s °F (low 20s °C). Evenings cool "
        "off once the sun goes down, so bring a sweater and jeans.",
        "Oaxaca City sits at about 5,000 feet. Drink plenty of water before "
        "you arrive and in the first days if you're sensitive to altitude.",
    ]),
    ("What should I pack?", [
        "Bring your own yoga mat and any props you use. For the cemetery "
        "night, pack something festive, a flower crown or a big hat at "
        "least, and a warm layer for later. Face painting is done for you. "
        "There's a rooftop pool, so bring a swimsuit.",
    ]),
    ("What happens on the Day of the Dead?", [
        "Over the week the group builds a traditional altar together. If "
        "you'd like, bring a photo or a small keepsake of a loved one who has "
        "passed, and you'll have private time to add it.",
        "On November 1 there is Catrina face painting and dinner at the "
        "villa, then a visit to the cemeteries to adorn graves left "
        "unattended.",
    ]),
    ("Can I come on my own?", [
        "Yes. Space in the villa is limited, so most rooms are shared: "
        "triples and doubles with twin beds, some with a private bathroom "
        "and some sharing one. Please be open to sharing. Breakfast and five "
        "dinners are at the villa, so the group eats together most nights.",
    ]),
    ("How do I get there?", [
        "Fly into Oaxaca (OAX). Airport transfers aren't included, but send "
        "us your flight details and we can arrange a ride. See Travel Tips "
        "for more.",
    ]),
]


# ---------------------------------------------------------------- Portugal

_PORTUGAL = [
    ("How active are the days?", [
        "Moderate, and mostly up to you. Lisbon days include a walking food "
        "tour and a day trip to Sintra. On the coast there is an optional "
        "intermediate cliffside hike of 8 km, optional surf lessons and free "
        "time for trails and beaches. Yoga is daily, often twice.",
    ]),
    ("What will the weather be like in May?", [
        "May is usually mild and sunny. Lisbon days are often around 20 to "
        "24°C, and the Costa Vicentina is breezier, with cool mornings and "
        "evenings. The Atlantic stays cold for swimming. Pack layers and a "
        "windproof jacket.",
    ]),
    ("What should I pack?", [
        "Comfortable walking shoes for Lisbon, and trail shoes if you plan "
        "to do the cliffside hike. Add a swimsuit for the beach and pool, "
        "layers for the coastal evenings and one nicer outfit for dinners "
        "out.",
    ]),
    ("Can I come on my own?", [
        "Yes. Shared rooms have one king bed or two twin beds, all with an "
        "ensuite bathroom, and we'll try to pair you with another guest. Six "
        "of the seven dinners are shared, so you won't often eat alone.",
    ]),
    ("How do I get there?", [
        "Fly into Lisbon (LIS). A taxi to the hotel costs about 15 to 20 "
        "euro and isn't included; the drive to the coast is. On the last day "
        "the group leaves for Lisbon airport at 7am, about 2.5 hours away, "
        "so allow for that when you book your flight home. See Travel Tips "
        "for more.",
    ]),
]


# --------------------------------------------------------------- Argentina

_ARGENTINA = [
    ("How active are the days?", [
        "Active but not demanding. In Buenos Aires you walk the city, take a "
        "tango class and try a polo lesson. In Patagonia there is a guided "
        "kayak trip, an easy to moderate walk through the Arrayanes Forest "
        "and two optional nature walks. Yoga is most days, with plenty of "
        "free time.",
    ]),
    ("What will the weather be like in March?", [
        "Seasons are reversed in Argentina, so March is late summer in "
        "Buenos Aires and early autumn in Patagonia. Buenos Aires is usually "
        "warm, often in the mid to high 20s °C. Villa La Angostura is cooler, "
        "with mild days and cold evenings, and rain is possible in both.",
    ]),
    ("What should I pack?", [
        "Layers for every kind of weather: jeans and a light sweater or "
        "jacket for the evenings, a rain jacket, and sturdy shoes for the "
        "walks. Think twice about heels on the cobblestones.",
        "Both hotels have pools, so bring a swimsuit. The lake is cold all "
        "year. Mats are provided in both places.",
    ]),
    ("Do I need to book flights during the retreat?", [
        "Yes. The round-trip flights between Buenos Aires (AEP) and Bariloche "
        "aren't included. We tell you which ones to book about 4 months "
        "before the retreat. Transfers between Bariloche airport and Villa "
        "La Angostura are included.",
    ]),
    ("Can I come on my own?", [
        "Yes. Rooms have a king bed, and some can have two twin beds if "
        "you'd like to share with another guest; ask us about availability. "
        "Six dinners are shared, and we pair guests to share taxis between "
        "airports.",
    ]),
    ("How do I get there?", [
        "Fly into Buenos Aires Ezeiza (EZE), about 45 minutes from the first "
        "hotel by metered taxi. The retreat ends with a flight back to the "
        "domestic airport (AEP), and international flights leave from EZE, "
        "1 to 1.5 hours across town. Plan to fly home late on closing day or "
        "the next morning. See Travel Tips for more.",
    ]),
]


# ----------------------------------------------------------------- Iceland

_ICELAND = [
    ("How active are the days?", [
        "Gently active, with some long drives. The guided glacier hike is for "
        "all levels: crampons and an ice axe are provided, and you need "
        "hiking boots, which you can rent for around $10. There is also an "
        "easy canyon hike and a horse ride, weather permitting.",
        "The longest drive is 4.5 hours along the south coast, broken up with "
        "stops.",
    ]),
    ("What will the weather be like?", [
        "Our last retreat here ran in October. Expect weather that changes "
        "by the minute, sun, rain, fog and wind, often in one day, with "
        "temperatures usually just above freezing to around 7°C.",
        "Dress in layers: a thin layer underneath, a warm waterproof coat, "
        "thick socks, gloves and a hat that covers your ears.",
    ]),
    ("Will we see the northern lights?", [
        "Possibly, but nobody can promise it. The lights need strong solar "
        "activity, clear skies and real darkness at the same time. The "
        "retreat is timed for the dark season, and the hotels will alert us "
        "if the lights appear, so you can look out the window or step "
        "outside.",
    ]),
    ("What should I pack?", [
        "A medium suitcase only, so everyone's bags fit in the vans. Bring "
        "your own yoga mat, and blocks if you use them; straps are provided. "
        "Add a swimsuit for the geothermal lagoon, where towels are "
        "provided, and a reusable water bottle.",
        "Iceland is expensive: a glass of wine is around $20.",
    ]),
    ("Can I come on my own?", [
        "Yes. Shared rooms have two individual beds and an ensuite bathroom. "
        "One night, at the turf house on a horse farm, the whole group sleeps "
        "in one shared space in individual beds, Icelandic style. There is "
        "no private option that night.",
    ]),
    ("How do I get there?", [
        "Fly into Keflavík International Airport, about an hour from the "
        "Reykjavík hotel. Airport transfers aren't included, but we can pair "
        "you with guests arriving at similar times, or you can take the "
        "airport bus, which is simple and affordable. The Reykjavík hotel "
        "location is shared closer to the dates.",
    ]),
]


# ------------------------------------------------------------------ France

_FRANCE = [
    ("How active are the days?", [
        "Easygoing. There is yoga every morning, and the rest is walking: a "
        "fashion and art walk and a food tour in Paris, then vineyard visits "
        "in Champagne. Day 6 is a spa day with a 60-minute massage, the "
        "pool, sauna and hammam.",
    ]),
    ("What will the weather be like?", [
        "The next dates aren't set yet. [missing data: month of the last "
        "edition and typical weather]",
        "Whatever the season, layers and a light rain jacket are sensible in "
        "northern France, and evenings in the Champagne countryside can be "
        "cool.",
    ]),
    ("What should I pack?", [
        "Comfortable shoes for long walks on city streets and vineyard "
        "paths, and a swimsuit for the spa. Paris rooms are compact, 12 to "
        "14 square meters, so a smaller suitcase makes life easier.",
    ]),
    ("Can I come on my own?", [
        "Yes, and it suits a solo traveler well. The Petit Room is a private "
        "room with an individual bed and ensuite bathroom, made for one. "
        "Rooms in Champagne are more spacious than in Paris. Six dinners are "
        "shared with the group.",
    ]),
    ("How do I get there?", [
        "Arrive in Paris; airport transfers aren't included. The fast train "
        "to Champagne is included, and so is the train to the airport on the "
        "last morning: it leaves Champagne at 8:58am and reaches Paris CDG "
        "Terminal 2 at 9:29am. You can change that train time if you prefer, "
        "and tickets are sent closer to the dates.",
    ]),
]


# ---------------------------------------------------------------- Sardinia

_SARDINIA = [
    ("How active are the days?", [
        "Fairly active. There are two guided hikes, the Moon Valley at golden "
        "hour and a valley walk with caves and ruins, with easy and moderate "
        "options. There is also a boat day around the Maddalena Islands and "
        "free time for kayaks, the pool or tennis.",
        "Proper hiking boots are required to protect your ankles.",
    ]),
    ("What will the weather be like?", [
        "The next dates aren't set yet. [missing data: month of the last "
        "edition and typical weather]",
        "The second half is in the mountains, where evenings are usually "
        "cooler than by the sea, so pack a warm layer.",
    ]),
    ("What should I pack?", [
        "Hiking boots, a swimsuit for the beach and the boat day, and an "
        "adaptor for European outlets. Yoga mats are provided. If you eat "
        "gluten free, Sardinia is very good about it.",
    ]),
    ("Can I come on my own?", [
        "Yes. The deluxe twin rooms have two separate beds and an ensuite "
        "bathroom if you'd like to share with another guest. All lunches and "
        "dinners but one of each are included, so you'll eat with the group "
        "most of the time.",
    ]),
    ("Can I have a sea view?", [
        "At the first hotel, by the sea, you can ask for a sea-view room. "
        "We'll check availability and send you the upgraded rate. The rooms "
        "in the package don't face the sea.",
    ]),
    ("How do I get there?", [
        "Fly into Olbia (OLB). A shared shuttle leaves the airport at 2:30pm "
        "on arrival day, and the first hotel is about an hour away. On the "
        "last morning the shuttle leaves for Olbia at 9am. Cagliari, in the "
        "south, is about three hours away and only worth it if you're "
        "extending your trip.",
    ]),
]


# ---------------------------------------------------------- Basque Country

_BASQUE = [
    ("How active are the days?", [
        "Easygoing. Yoga is daily, mostly at a local studio in San Sebastián, "
        "and the rest is walking: a day tour of the Old Town, a pintxos "
        "crawl, and Laguardia and its vineyards. There is also a walk in "
        "nature, so bring something sturdy on your feet.",
    ]),
    ("What will the weather be like?", [
        "Our last retreat here ran in October, when days are usually in the "
        "low 70s °F (around 22°C) and nights in the high 50s (around 14°C). "
        "San Sebastián can be rainy at any time, so bring a rain jacket and a "
        "small umbrella.",
    ]),
    ("Can you cater for vegetarians?", [
        "Not on this retreat. Basque cooking is built around meat and "
        "seafood, so vegetarian and vegan diets can't be accommodated. "
        "Pescatarian, gluten free and specific allergies are easier, but "
        "serious allergies are hard to manage in small kitchens. This one "
        "is best if you eat everything.",
    ]),
    ("What should I pack?", [
        "Layers for sunny days and cool evenings, a rain jacket, and shoes "
        "that handle cobblestones. A swimsuit for the beach. Mats are "
        "provided at the studio in San Sebastián, but bring your own mat and "
        "props for wine country.",
    ]),
    ("Can I come on my own?", [
        "Yes. Deluxe rooms can have two twin beds if you'd like to share with "
        "another guest, and every room has an ensuite bathroom. All dinners "
        "are included and shared, so you're never far from the group.",
    ]),
    ("How do I get there?", [
        "Bilbao (BIO) is usually the cheapest airport, about an hour from San "
        "Sebastián; you can also fly into San Sebastián (EAS). The ride to "
        "the first hotel isn't included: a taxi is about 155 to 200 euro, or "
        "take an ALSA or PESA bus. On the last day a group shuttle reaches "
        "Bilbao airport around 12:30pm.",
    ]),
]


# ------------------------------------------------------------------ Turkey

_TURKEY = [
    ("How active are the days?", [
        "Gently active. Yoga is most mornings. In Istanbul you walk between "
        "sights, markets and neighborhoods, and in Cappadocia there is a "
        "hike in the Red Valley. The hot air balloon ride means a 4:30am "
        "start.",
    ]),
    ("What will the weather be like?", [
        "The next dates aren't set yet. [missing data: month of the last "
        "edition and typical weather]",
        "Whatever the month, sunrise in Cappadocia is cold, so bring a coat, "
        "a scarf and warm layers for the balloon ride. Hiking in the sun "
        "later in the day is comfortable and warm.",
    ]),
    ("What if the balloon can't fly?", [
        "Balloons only fly in good weather. If the morning is called off, "
        "we'll try to reschedule for the next morning, so book a later "
        "flight out of Cappadocia on the last day just in case. If a "
        "reschedule isn't possible, the balloon ride is refunded.",
    ]),
    ("What should I wear and pack?", [
        "In Istanbul you can dress as you would at home, but cover your "
        "shoulders and collarbones in the mosques and dress modestly for the "
        "whirling dervish ceremony. At the hammam towels are provided and "
        "most people wear a swimsuit bottom. Props are provided at the "
        "studio, so a mat is optional.",
    ]),
    ("Can I come on my own?", [
        "Yes. Twin rooms with two beds and an ensuite bathroom are available "
        "in both Istanbul and Cappadocia, and we try to pair guests who'd "
        "like to share. The workshops and tours are done as a group.",
    ]),
    ("How do I get there?", [
        "Fly into Istanbul Airport (IST). Airport transfers are included, "
        "and we group guests on similar flights. On day 7 the group takes a "
        "morning flight from IST to Cappadocia (NAV). [missing data: whether "
        "the Istanbul to Cappadocia flight is included] The retreat ends in "
        "Cappadocia, so plan your flight home from there.",
    ]),
]


# ---------------------------------------------------------------------- map

ADDITIONAL_INFO = {
    "argentina-mar-2027": _ARGENTINA,
    "camino-de-santiago-apr-2027": _camino("April"),
    "camino-de-santiago-sep-2027": _camino("September"),
    "el-cuyo-dec-2026-d29": _el_cuyo("New Year"),
    "el-cuyo-dec-2026-delaney": _el_cuyo("December"),
    "el-cuyo-feb-2027-delaney": _el_cuyo("February"),
    "el-cuyo-feb-2027-morin-gray": _EL_CUYO_BOUDOIR,
    "el-cuyo-jan-2027-berger": _el_cuyo("January"),
    "el-cuyo-jan-2027-coffey": _EL_CUYO_MINI,
    "france": _FRANCE,
    "iceland": _ICELAND,
    "montenegro-may-2027-cordover": _MONTENEGRO,
    "montenegro-may-2027-erickson": _MONTENEGRO,
    "morocco-nov-2026": _MOROCCO_NOV,
    "morocco": _MOROCCO_DATELESS,
    "oaxaca-oct-2027": _OAXACA,
    "portugal-may-2027": _PORTUGAL,
    "santorini-may-2027": _SANTORINI_MAY,
    "santorini-sep-2027-berger": _SANTORINI_SEP,
    "santorini-sep-2027-d26": _SANTORINI_LATE_SEP,
    "santorini-sep-2027-erickson": _SANTORINI_SEP,
    "sardinia": _SARDINIA,
    "spain-basque-country": _BASQUE,
    "thailand-dec-2026": _THAILAND,
    "turkey": _TURKEY,
}
