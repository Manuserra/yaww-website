# -*- coding: utf-8 -*-
"""Editorial layer for the retreat pages: what build_page.py cannot read
straight out of a data.json.

Everything here is either copied from retreatpage-elcuyo.html or condensed
from the old site's own text (retreats/<slug>/data.json). Nothing is
invented: where a group below has no source line behind it, it is not here.

TRAVEL_TIPS   one entry per location, in the template's shape — a short
              heading and two to four short lines, the way El Cuyo's are
              written. The old site publishes these as one long paragraph
              per topic; they are cut down, not rewritten in substance.
              Santorini and Portugal publish no Travel Tips section at all,
              so theirs are assembled only from facts their own pages state
              elsewhere (includes, itinerary, pricing notes).
VENUE_NAMES   a tab name for properties the old site never names.
FACILITATORS  the YAWW team member who travels with a location's groups,
              where the team page ties one to it. Placeholder until
              confirmed per date — the page says so in a comment.
HOST_META     the small line under a host's name.
"""

TRAVEL_TIPS = {
    # Verbatim from retreatpage-elcuyo.html.
    "el-cuyo": [
        ("Arrival & Time Zone", [
            "Fly into Cancun International Airport (CUN).",
            "Cancun is one hour ahead of El Cuyo — double-check time zones when booking flights.",
        ]),
        ("Getting to El Cuyo", [
            "Private Transfer: Direct ride from the airport (2.5 hours). We can arrange this for you.",
            "Bus + Taxi (Budget Option): Take an ADO bus to Tizimin, then a taxi to El Cuyo (~$50 USD).",
            "Local Bus: The slowest option but available if needed.",
        ]),
        ("Money & Payments", [
            "No ATMs in El Cuyo – bring Mexican Pesos with you.",
            "Some places accept credit cards, but cash is preferred.",
        ]),
        ("Internet & Connectivity", [
            "WiFi is unreliable and may cut out at times.",
            "If you need a strong connection, let us know in advance.",
        ]),
        ("Shops & Essentials", [
            "No large stores in El Cuyo — bring everything you might need.",
            "Few pharmacies available, but for specific medications, you’d need to travel 1.5 hours to Tizimín.",
        ]),
    ],

    "argentina": [
        ("Arrival & Airports", [
            "Fly into Buenos Aires Ezeiza (EZE), about 45 minutes from our hotel. A metered taxi from the terminal is the easiest way in.",
            "Domestic flights use Aeroparque (AEP), about 15 minutes from the hotel.",
            "The AEP – Bariloche flights are not included. We send the flight details about 4 months before the retreat.",
        ]),
        ("Flying Home", [
            "The retreat ends with a flight back to AEP. International flights leave from EZE, 1 – 1.5 hours across town.",
            "Booking early? Plan to fly out of EZE late on closing day, or stay a night in Buenos Aires and leave the next morning.",
            "Once we have everyone’s flights, we pair you up to share taxis.",
        ]),
        ("Weather & Packing", [
            "Seasons run opposite to North America’s, so pack layers for every kind of weather.",
            "Jeans and a light sweater or jacket for cool evenings, a rain jacket just in case.",
            "Sturdy shoes for the nature walks. Think twice about heels on the cobblestones.",
            "Both hotels have pools. The lake is cold all year, if a cold plunge is calling.",
        ]),
        ("Yoga Props", [
            "In Buenos Aires we practise at a studio with mats and props provided.",
            "In Patagonia a yoga mat is provided for you.",
        ]),
        ("Food & Dietary Needs", [
            "Argentine cooking is meat-heavy, but vegetarian (and often vegan) options are offered.",
            "Gluten-free and allergies can be considered. With a serious allergy, bring two epi-pens and tell us where they are.",
        ]),
        ("Insurance", [
            "Buy travel and trip cancellation insurance as soon as you pay your deposit.",
        ]),
    ],

    "camino-de-santiago": [
        ("Arrival & Airports", [
            "We begin in Vigo and finish in Santiago de Compostela — book into one and out of the other.",
            "Vigo (VGO) is the closest airport to our first hotel, around 20 minutes away.",
            "Santiago (SCQ), Lisbon (LIS) and Porto (OPO) are often better value. From Porto, trains and buses reach Vigo in 2 – 2.5 hours.",
            "Arrive a day or three early if you can: starting a walking week jet-lagged is hard.",
        ]),
        ("Preparing Your Body", [
            "Not a technical trail, but six days of consecutive walking. Come with kilometres in your legs.",
            "Build up to 15 – 20 km comfortably, ideally on back-to-back days.",
            "Nothing new on day one: break in your shoes, socks and pack properly.",
        ]),
        ("Packing", [
            "Your suitcase travels between hotels each day; you carry only a day pack.",
            "A proper waterproof jacket, layers, two pairs of walking shoes and plenty of socks.",
            "A swimsuit for pools and thermal baths, and one nicer outfit for the farewell dinner.",
            "We send a full packing list before the retreat.",
        ]),
        ("Weather", [
            "April: green, fresh and changeable, around 54 – 66°F, with a fair chance of rain.",
            "September: warmer and more settled, around 63 – 75°F.",
            "Mornings are cool and afternoons can be warm: layers do most of the work.",
        ]),
        ("Yoga & Props", [
            "Mats and props are provided at the hotels where we practise.",
            "Practices are designed around a walking body — accessible, restorative and always optional.",
        ]),
        ("Food & Water", [
            "Galician cooking is seafood-forward: pulpo, oysters at Arcade, Padrón peppers, Albariño.",
            "Vegetarian and vegan meals can be arranged with advance notice. With a serious allergy, bring two epi-pens.",
            "Tap water is safe to drink. Refill from fountains marked “agua potable”.",
        ]),
        ("Your Credential", [
            "Every pilgrim carries a credential, stamped at hotels, cafés and churches along the way.",
            "We walk more than the final 100 km, so you can collect your certificate in Santiago.",
        ]),
        ("Insurance", [
            "Buy travel and trip cancellation insurance as soon as you book.",
            "Proof of medical insurance is required.",
        ]),
    ],

    "montenegro": [
        ("Getting to Perast", [
            "Tivat (TIV) is the closest airport, 20 minutes away by taxi or transfer.",
            "Podgorica (TGD), the capital’s airport, is about 1.5 hours away.",
            "Dubrovnik (DBV) in Croatia is 1.5 – 2 hours away; the border can add time in peak season.",
        ]),
        ("Transfers", [
            "Pre-arranging a transfer makes arrival easy, especially late at night or with heavy bags.",
            "Send us your flights and we’ll pair you with others arriving at similar times.",
            "Flying via Dubrovnik or Podgorica? Check visa requirements for the border.",
        ]),
        ("Money", [
            "Montenegro uses the Euro (EUR).",
            "Cards are widely accepted; carry some cash for small shops and cafés in Perast.",
        ]),
        ("What to Pack", [
            "May is mild spring weather: bring layers for cool mornings and evenings.",
            "Swimwear for kayaking, paddleboarding and the boat day; sturdy shoes for trails.",
            "Sun protection, a reusable water bottle and a day pack.",
            "Plugs are European, type C or F.",
        ]),
    ],

    "oaxaca": [
        ("Arrival", [
            "Fly into Oaxaca International Airport (OAX).",
            "Airport transfers are not included. Send us your flight details and we’ll arrange a ride.",
        ]),
        ("Weather & Altitude", [
            "Days are pleasant, in the 70s °F. Evenings cool off: bring a sweater and jeans.",
            "Oaxaca City sits at about 5,000 feet. Hydrate well before you arrive and through the first days.",
            "The villa has a rooftop pool, so pack a swimsuit.",
        ]),
        ("What to Bring", [
            "Your yoga mat and any props you use.",
            "Something festive for the cemetery night — a flower crown or a big hat at least. Face painting is done for you.",
            "A photo or small keepsake of loved ones who have passed, for our shared Día de Muertos altar.",
            "Sandals or sneakers for the uneven cobblestones. There is no strenuous walking.",
        ]),
        ("Tipping", [
            "Tips are not included. We can collect them and present them to the staff at the final dinner.",
            "Suggested: 1,000 – 1,500 Mexican pesos for the 7 nights, and 100 – 200 pesos per massage, given directly.",
        ]),
    ],

    # The old site has no Travel Tips section for Portugal; every line below
    # restates what its own Includes, Not Included and Itinerary say.
    "portugal": [
        ("Arrival", [
            "Fly into Lisbon (LIS).",
            "The ride to our hotel in São Bento is not included. Taxis wait outside the airport, about 15 – 20 euro.",
        ]),
        ("Getting Around", [
            "Transport from Lisbon to the Costa Vicentina is included, about a 2.5 hour drive.",
        ]),
        ("Flying Home", [
            "The group leaves for Lisbon airport at 7am on the last day, about 2.5 hours without traffic.",
            "Need to leave at another time? Private rides are about 250 euro and not included.",
        ]),
        ("Insurance", [
            "Travel and trip cancellation insurance is highly recommended.",
            "Proof of international medical insurance is required to attend.",
        ]),
    ],

    # Same for Santorini: assembled from its Includes, Itinerary and pricing notes.
    "santorini": [
        ("Arrival", [
            "Fly into Santorini’s Thira airport (JTR).",
            "A shared shuttle collects the group at JTR at 3pm on arrival day.",
        ]),
        ("Departure", [
            "The shuttle back to JTR leaves the villa at 9am on the final morning.",
            "Need a ride at another time? Send us your flights and we’ll arrange a private transfer at extra cost.",
        ]),
        ("Food & Drink", [
            "Breakfast is served daily at the villa.",
            "Included dinners come with two glasses of wine. Lunches include water for the table.",
        ]),
        ("Good to Know", [
            "The property is four smaller villas; some bathrooms are shared, as marked on each room.",
            "Space is limited to 15 guests.",
        ]),
    ],

    "thailand": [
        ("Flights", [
            "Fly into Chiang Mai (CNX), where the retreat begins.",
            "The mid-retreat flight from Chiang Mai to Phuket is not included. We tell you which one to book when you register.",
            "Fly home from Phuket (HKT) or Krabi (KBV) after 1pm, to allow for the boat from the island.",
        ]),
        ("Jet Lag", [
            "Arrive a few days early to adjust — the time difference from the US is close to 12 hours.",
            "Shift your sleep before you travel, hydrate on the flight, and get daylight when you land.",
        ]),
        ("Culture & Temples", [
            "Greet with the wai, palms pressed together.",
            "At temples, dress modestly — shoulders and knees covered — and take your shoes off.",
            "Never touch anyone’s head or point your feet at people or sacred objects.",
        ]),
        ("Money & Getting Around", [
            "The currency is the Thai Baht (THB). ATMs are everywhere; markets and small shops want cash.",
            "Agree the fare before a tuk-tuk ride. Grab works for longer trips.",
            "Drink bottled water, not tap.",
        ]),
        ("Weather & Packing", [
            "Late in the year is the cool, dry season: light breathable clothes, a hat and sunscreen.",
            "Comfortable walking shoes for temples, markets and the trail.",
        ]),
    ],

    # Condensed from the old site's own Morocco travel notes.
    "morocco": [
        ("Arrival", [
            "Fly into Marrakech Menara (RAK), where the retreat begins.",
            "Airport transfers to our riad are shared or private, depending on everyone’s flights. Arriving early? Meet the shuttle at the airport or come to the riad on your own.",
        ]),
        ("What to Wear", [
            "Morocco is a progressive Muslim country. Outfits to the knee that cover the shoulders avoid unwanted attention.",
            "Long pants for the camel ride on the beach, and a swimsuit for the spa day and the pool.",
        ]),
        ("Weather", [
            "Essaouira is breezy, around 70°F / 20°C most days. Marrakech is considerably warmer.",
            "Fall is usually dry; spring can bring rain, so pack a light rain jacket.",
        ]),
        ("What to Bring", [
            "Mats and props are provided in both Marrakech and Essaouira.",
            "Sunglasses, a sun hat, good walking shoes and a camera.",
            "A universal adaptor: outlets are European, 2-prong.",
            "A collapsible duffle for everything you’ll find in the souks.",
            "Sunscreen, probiotics and any medication that’s hard to get abroad.",
        ]),
        ("Money", [
            "The currency is the Moroccan dirham, roughly 10 to 1 USD.",
            "Use your ATM card at local ATMs, at the airport or in the main square of either city.",
        ]),
    ],
}

# Tab names for properties the old site describes but never names. Each is
# taken from the property's own description.
VENUE_NAMES = {
    "camino-de-santiago": "Hotels, pazos & a parador",
    "montenegro": "16th-century villa in Perast",
    "oaxaca": "Private villa near Oaxaca City",
    "santorini": "Private villas in Pyrgos",
}

# Team members the team page ties to a destination. PLACEHOLDER per date.
FACILITATORS = {
    "el-cuyo": "renata-centi",      # "our retreats in El Cuyo and Sardinia"
    "thailand": "fleur-optekamp",   # "our retreats in Asia"
}

HOST_META = {
    "alicia-delaney": "E-RYT 200 · YACEP · Restorative & yoga nidra",
    "rachel-gray": "Boudoir & portrait photographer",
    "renata-centi": "YAWW team · El Cuyo & Sardinia",
    "fleur-optekamp": "YAWW team · Asia",
}

# What used to be the small line under a host's name (HOST_META), said in
# the bio instead, where the brief wants it. Only facts the old line held.
BIO_ADD = {
    "alicia-delaney": "She is an E-RYT 200 and a YACEP, specialising in restorative yoga and yoga nidra.",
}

# How a person is labelled in the hosts tabs and over their name.
HOST_ROLE = {
    "rachel-gray": ("Photographer", "Your photographer"),
}

# Short tab labels. The panel heading keeps the full name; the tab only has
# to tell two or five options apart, and on a tablet a long label wraps the
# whole bar onto a second row. Keyed by the venue's label in data.json.
VENUE_TABS = {
    "Buenos Aires boutique hotel": "Buenos Aires",
    "Villa La Angostura lake front hotel": "Villa La Angostura",
    "3 nights in Lisbon : a boutique stay in São Bento": "Lisbon",
    "4 nights in Costa Vicentina : a secluded boutique stay": "Costa Vicentina",
    "5 nights in Chiang Mai Old City": "Chiang Mai",
    "4 nights beachfront on Koh Yao Noi": "Koh Yao Noi",
    "Riad in the Marrakech medina": "Marrakech",
    "Countryside domes near Essaouira": "Essaouira",
}

EXTRA_TABS = {
    "Excursion to Las Coloradas": "Las Coloradas",
    "Kayaking in Nature": "Kayaking",
    "Yucatan Peninsula day trip": "Yucatan day trip",
    "Temazcal Ceremony": "Temazcal",
    "Optional healing massage at the villa": "Healing massage",
}

# Where the face sits, as a fraction of the width, in the old site's
# landscape teacher photos. build_page.py cuts a 312:408 portrait (the
# host frame) around it for hosts with no portrait of their own.
PORTRAIT_FOCUS = {
    "amy-sennett-starner": 0.38,
    "bonnie-erickson": 0.34,
}

# Confidentiality: hotel names never appear on the site (Utopia, which is
# ours, is the one exception). The old site names a few properties, and a
# chef or restaurant can name one just as well. Applied by build_page.py to
# the finished page, so no field can carry a name past it: copy, alt text,
# room and venue data, itinerary lines.
SCRUB = [
    (r"Rehabilitated in 1988 by Jaime Gutiérrez and Mery González, pioneers in Spanish wine tourism, Mayor de Migueloa",
     "Restored in 1988 by pioneers of Spanish wine tourism, the posada"),
    (r"Mayor de Migueloa", "the posada"),
    (r"the Old Viana palace built in 1619", "a palace built in 1619"),
    (r"We offer you a proposal of haute cuisine in each service, created by the award-winning chef Paulo Airaudo\. "
     r"In addition, the hotel has in its facilities the Amelia by Paulo Airaudo restaurant, with two Michelin stars\.",
     "The hotel restaurant holds two Michelin stars, and every meal there is cooked to that standard."),
    (r"Opening dinner at Amelia by Paulo Airaudo, 2 Michelin starred dining experience",
     "Opening dinner at the hotel's two Michelin star restaurant"),
    (r"Amelia by Paulo Airaudo", "the hotel's restaurant"),
    (r"Paulo Airaudo", "the hotel's chef"),
    (r"Northern Light Inn", "Hotel near Reykjavík"),
    (r"Fosshotel Glacier Lagoon", "Hotel near the Vatnajökull glacier"),
    (r"Turf Guesthouse &(?:amp;)? Horse Farm", "Turf guesthouse on a horse farm"),
]
