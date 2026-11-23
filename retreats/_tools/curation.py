# -*- coding: utf-8 -*-
"""Hand-checked location-level content the automatic parse cannot get right:
accommodations, room pricing and optional extras. Every string below is
transcribed from the old site; nothing is invented. A null price means the
old site publishes no price for that room."""

UTOPIA = dict(
  name="Utopia Guesthouse & Yoga Studio: El Cuyo",
  instagram="@utopiayogaelcuyo",
  text=[
    "Utopia Guesthouse & Yoga Studio: El Cuyo is the 3rd incarnation of a series of retreat houses that we have created to host retreat groups over the years.",
    "The main living area shares a sitting room and a dining area indoors. There are porches both on the front side of the house and the beach side.",
    "Bedrooms are simple and cozy with neutral tones, natural textures from local sources and some with ocean views.",
    "The house has 8 bedrooms (5 privates with a queen bed each and 3 shared with 2 twin beds).",
    "Bathrooms are shared amongst the house. There are three full bathrooms plus a double sink wash area upstairs and an outdoor shower with doors on the beach.",
    "Our yoga studio has ocean views from the second story terrace of the house. Yoga mats, blankets, straps and blocks are available.",
  ])

def R(name, usd, label, unit=None, desc=None, bed=None, bath=None, view=None, hotel=None):
    return dict(name=name, price_usd=usd, price_label=label, price_unit=unit,
                description=desc, attrs=dict(bed=bed, bath=bath, view=view), hotel=hotel)

def X(name, usd, label, unit=None, desc=None):
    return dict(name=name, price_usd=usd, price_label=label, unit=unit, desc=desc)

ELCUYO_ROOM_DESC = {
 "shared_double": "Interior bedrooms with 2 twin beds and shared bathrooms within steps.",
 "tower": "Third floor bedroom with separate entrance. Can be a king bed or 2 twins. Separate dressing area and shared bathroom.",
 "interior": "Interior queen bedrooms with shared bathrooms within a few steps.",
 "ocean": "Oceanview queen bedrooms with shared bathrooms within a few steps.",
}

CUR = {

"beach-yoga-retreat-mexico": dict(
  hotels=[UTOPIA],
  rooms=[
    R("Shared Double rooms", 2295, "$2,295usd per person", "per person", ELCUYO_ROOM_DESC["shared_double"], bed="Twin", bath="Shared"),
    R("Shared Tower Room", 2365, "$2,365usd per person", "per person", ELCUYO_ROOM_DESC["tower"], bed="King or 2 twins", bath="Shared"),
    R("Private Interior rooms", 2435, "$2,435usd private room", "private room", ELCUYO_ROOM_DESC["interior"], bed="Queen", bath="Shared"),
    R("Private ocean view room — second floor", 2535, "$2,535usd SECOND FLOOR ROOMS", "private room", ELCUYO_ROOM_DESC["ocean"], bed="Queen", bath="Shared", view="Ocean"),
    R("Private ocean view room — beachfront", 2595, "$2,595usd BEACHFRONT ROOM", "private room", ELCUYO_ROOM_DESC["ocean"], bed="Queen", bath="Shared", view="Beachfront"),
  ],
  extras=[
    X("Excursion to Las Coloradas", 155, "$155usd per person", "per person",
      "Excursion to Las Coloradas to see flamingos and pink lagoons; dip in a secluded beach & lunch at a local fish joint."),
    X("Yucatan Peninsula day trip", 195, "$195usd per person", "per person",
      "Yucatan Peninsula day trip: Ruins of Ek Balam, colonial city of Valladolid & cenote; lunch included."),
    X("Kayaking in Nature", 85, "$85usd per person", "per person",
      "Sunset kayaking amongst the lilypads & mangroves. Guide and transportation included."),
    X("Temazcal Ceremony", 95, "$95usd per person", "per person",
      "A powerful ancestral practice of release, transformation, and rebirth."),
    X("Additional 75 minute massages", 140, "$140usd each", "each", "Additional 75 minute massages."),
  ],
  extras_note="Minimum 4 participants for all excursions. Availability subject to water levels."),

"mini-yoga-retreats-in-paradise": dict(
  hotels=[UTOPIA],
  rooms=[
    R("Shared Double rooms", 1495, "1,495usd per person", "per person", ELCUYO_ROOM_DESC["shared_double"], bed="Twin", bath="Shared"),
    R("Shared Tower Room", 1695, "1,695usd per person", "per person", ELCUYO_ROOM_DESC["tower"], bed="King or 2 twins", bath="Shared"),
    R("Private Interior rooms", 1595, "1,595usd Private", "private room", ELCUYO_ROOM_DESC["interior"], bed="Queen", bath="Shared"),
    R("Private ocean view room — oceanview queen", 1695, "1,695usd Private Oceanview Queen", "private room", ELCUYO_ROOM_DESC["ocean"], bed="Queen", bath="Shared", view="Ocean"),
    R("Private ocean view room — beachfront queen", 1795, "1,795usd Private Beach front Queen", "private room", ELCUYO_ROOM_DESC["ocean"], bed="Queen", bath="Shared", view="Beachfront"),
  ],
  extras=[
    X("Excursion to Las Coloradas", 150, "150usd", "per person",
      "Excursion to Las Coloradas to see flamingos and pink lagoons; dip in a secluded beach & lunch at a local fish joint."),
    X("Additional 75 minute massages", 145, "145usd each", "each", "Additional 75 minute massages."),
    X("Private or shared airport transfers", None, None, None,
      "Private or Shared Airport Transfers to and from CUN; we pair travelers together to share costs where possible. Transfers are never included in pricing."),
  ],
  extras_note=None),

"embodied-the-art-of-being-seen": dict(
  hotels=[UTOPIA],
  rooms=[
    R("Shared Double rooms", 3495, "$3,495usd per person", "per person", ELCUYO_ROOM_DESC["shared_double"], bed="Twin", bath="Shared"),
    R("Shared Tower Room", 3695, "$3,695usd per person", "per person", ELCUYO_ROOM_DESC["tower"], bed="King or 2 twins", bath="Shared"),
    R("Private Interior rooms", 3635, "$3,635usd private room", "private room", ELCUYO_ROOM_DESC["interior"], bed="Queen", bath="Shared"),
    R("Private ocean view room — second floor", 3755, "$3,755usd SECOND FLOOR ROOMS", "private room", ELCUYO_ROOM_DESC["ocean"], bed="Queen", bath="Shared", view="Ocean"),
    R("Private ocean view room — beachfront", 3815, "$3,815usd BEACHFRONT ROOM", "private room", ELCUYO_ROOM_DESC["ocean"], bed="Queen", bath="Shared", view="Beachfront"),
  ],
  extras=[X("Additional 75 minute massages", 140, "$140usd each", "each", "Additional 75 minute massages.")],
  extras_note=None),

"yoga-retreat-greece": dict(
  hotels=[dict(name=None, instagram=None, text=[
    "Accommodations in our own strikingly beautiful villa featuring breathtaking views of the island and the Aegean Sea. We will take a full property of luxury villas for ourselves. Swimming pools and lounge chairs, fully equipped kitchens, and quiet reading nooks are available throughout the property.",
    "Full breakfast will be offered daily in our main dining area. Traditional Greek fruits, cheeses, pastries, eggs, and coffee will be among the choices daily. Choose to create your own lunches and dinners in these lovely kitchens and lounge by the pool for the afternoon.",
    "Rooms are offered as private queens, semi-private queens (2 level accommodations with loft upstairs), and Shared accommodations. Each villa has its own bathrooms and some rooms have en-suite bathrooms with its own charms and unique details.",
    "Our yoga retreat offers spectacular accommodations on Greece’s most picturesque island in the Cyclades! You’ll be so glad you joined us for this truly amazing retreat experience.",
  ])],
  rooms=[
    R("Private Queen Room – private bathroom", 6295, "6,295usd Private", "private room", "This room has a private ensuite bathroom.", bed="Queen", bath="Private"),
    R("Private Queen Room – private bathroom, shared bed", 5295, "5,295usd Shared Bed — per person", "per person", "This room has a private ensuite bathroom. Shared-bed rate; you must provide your own bedmate.", bed="Queen", bath="Private"),
    R("Private Queen Room – shared bathroom", 6195, "6,195usd Private", "private room", "Bathrooms are shared with one other room.", bed="Queen", bath="Shared"),
    R("Private Queen Room – shared bathroom, shared bed", 5195, "5,195usd Shared Bed — per person", "per person", "Bathrooms are shared with one other room. Shared-bed rate; you must provide your own bedmate.", bed="Queen", bath="Shared"),
    R("Shared Double Room – shared bathroom", 5695, "2 twin beds: 5,695usd per person", "per person", "2 twin beds.", bed="Twin", bath="Shared"),
    R("Lofted Queen Bed – shared bathroom", 5995, "5,995usd Private", "private room", "The loft space is accessed by a ladder with a full and half shared bathroom downstairs.", bed="Queen", bath="Shared"),
    R("Shared Suite – shared bathroom", 6095, "Private Queen Room: 6,095usd Private", "private room", "One bathroom inside the bedroom is shared in this villa.", bed="Queen", bath="Shared"),
    R("Studio Style Twin Bed – shared bathroom", 5995, "Studio Style Twin Bed in Living Area: 5,995usd", None, "Studio-style twin bed in the living area. One bathroom inside the bedroom is shared in this villa.", bed="Twin", bath="Shared"),
  ],
  extras=[X("Optional healing massage at the villa", None, "135 euro each", "each",
            "75 minute healing massage at the villa, available on several afternoons.")],
  extras_note="This property is made up of 4 smaller villas, all with varying room types. Some villas share bathrooms as indicated. Space is limited to 15 students."),

"yoga-retreat-montenegro": dict(
  hotels=[dict(name=None, instagram=None, text=[
    "We’ll stay in a 16th century luxury villa, nestled in the charming UNESCO-protected village of Perast, in the heart of the Bay of Kotor. Surrounded by timeless stone architecture and breathtaking sea views, the villa offers complete privacy and the perfect setting to unwind.",
    "Our 300 year old villa – painstakingly renovated to mirror the tradition and heritage of the area – provides unimpeded views across the glittering red rooftops of Perast and out on to the cool water of the bay. Each aspect has been meticulously integrated to ensure that it offers an unparalleled experience where guests are enveloped in opulence and comfort while feeling truly at home.",
    "Our pool area, featuring an exquisite infinity pool, stands as the hallmark of our villa. With its breathtaking, uninterrupted views of the bay and neighboring islands, complemented by the serene presence of the monastery next door, it radiates an unparalleled energy that invites guests to immerse themselves in complete relaxation and tranquility, offering a sanctuary where the stresses of the world simply melt away.",
    "Pamper yourself with the jacuzzi and sauna overlooking the bay in your free time.",
  ])],
  rooms=[
    R("Deluxe Suite", 7295, "7,295usd Private", "private room", "Deluxe Suites with 1 king bed, ensuite bathroom with shower and bathtub or with fireplace (first come, first served).", bed="King", bath="Private"),
    R("Deluxe Suite — shared bed", 5995, "5,995usd per person Shared Bed", "per person", "Deluxe Suite shared-bed rate. You must provide your own bedmate; it is not possible to separate the king beds.", bed="King", bath="Private"),
    R("Superior Room", 6795, "6,795usd Private", "private room", "Private room with 1 king bed, ensuite bathroom with shower or tub (first come, first served).", bed="King", bath="Private"),
    R("Superior Room — shared bed", 5725, "5,725usd per person Shared Bed", "per person", "Superior Room shared-bed rate. You must provide your own bedmate; it is not possible to separate the king beds.", bed="King", bath="Private"),
  ],
  extras=[],
  extras_note="4 suites have bathtubs or a jacuzzi in the room; rooms are allocated first come, first served. Once the main villa is full the group spills over into a nearby hotel."),

"yoga-retreat-portugal": dict(
  hotels=[
    dict(name=None, instagram=None, label="3 nights in Lisbon: a boutique stay in São Bento", text=[
      "Tucked into Lisbon’s São Bento neighborhood, our boutique stay feels both intimate and quietly distinctive. Each space is thoughtfully designed, blending contemporary lines with artistic details that reflect the creative energy of the area.",
      "There’s a sense of ease to it—comfortable, understated, and personal—where mornings begin with a beautifully prepared breakfast and the city moves just outside your door, yet never overwhelming.",
      "It’s the kind of place that lets you settle in quickly and experience Lisbon in a more local, authentic and unhurried way.",
    ]),
    dict(name=None, instagram=None, label="4 nights in Costa Vicentina: a secluded boutique stay", text=[
      "The second half of our experience unfolds in a quietly extraordinary retreat set within a protected valley on Portugal’s wild southwest coast. Surrounded by rolling hills, eucalyptus groves, and open fields that stretch toward the sea, the property feels both grounded and expansive—deeply connected to the land it rests on.",
      "Days move slowly here. Morning light over the valley, long walks through nature just beyond your door, time by the pool, and meals rooted in the rhythms of the region. There is space to explore, to rest, to soften.",
      "The atmosphere is intimate and unforced—beautiful without excess, thoughtful in every detail. It’s the kind of place people return to.",
    ]),
  ],
  rooms=[
    R("Private Room", 7695, "7,695usd Private", "private room", "Standard rooms have a double bed and ensuite bathroom.", bed="Double", bath="Private"),
    R("Shared Room", 6995, "6,995usd per person", "per person", "Double rooms have 1 king bed or 2 twin beds with ensuite bathroom.", bed="King or 2 twins", bath="Private"),
  ],
  extras=[],
  extras_note="Room images on the old site are representations of the room types and may vary in decoration, layout and style."),

"camino-de-santiago": dict(
  hotels=[dict(name=None, instagram=None, text=[
    "Part of the pleasure of this route is that no two nights look alike.",
    "We stay in casas rurales with their own particular charm — family-run, unhurried, the kind of place where breakfast is whatever came out of the garden.",
    "We stay in traditional Galician pazos, stone manor houses with walled gardens and centuries of weather on them.",
    "And we finish in Compostela at a Relais & Châteaux hotel with a spa, for two nights of proper pampering at the end of the walk.",
    "All stays are the most charming along this route. Each is unique, well appointed and comfortable with ensuite bathrooms.",
  ])],
  rooms=[
    R("Private Room", 7995, "7,995usd Private", "private room", "All stays along the route are unique, well appointed and comfortable with ensuite bathrooms.", bath="Private"),
    R("Private Room — shared bed", 6995, "6,995usd Shared Bed per person", "per person", "Shared-bed rate in a private room.", bath="Private"),
    R("Shared Room", 6995, "6,995usd per person", "per person", "All shared rooms have 2 separate beds. A few nights you will have your own private room.", bed="2 separate beds", bath="Private"),
  ],
  extras=[],
  extras_note="Space is limited to 12 guests for an intimate retreat."),

"yoga-retreat-argentina": dict(
  hotels=[
    dict(name=None, instagram=None, label="Buenos Aires boutique hotel", text=[
      "3 nights in Buenos Aires’ Palermo neighborhood charming boutique hotel.",
    ]),
    dict(name=None, instagram=None, label="Villa La Angostura lake front hotel", text=[
      "4 nights in Villa La Angostura lake front accommodations. Dry sauna, heated pool, hammam and hotel gym are available for use.",
    ]),
  ],
  rooms=[
    R("Standard Room", 7295, "Standard Room 7,295usd — City views in BA; Ground floor in VLA", None, "These rooms have 1 king bed with private bathroom. Some can have 2 twin beds. Please inquire for availability.", bed="King", bath="Private", view="City views in Buenos Aires"),
    R("Superior Room", 7595, "Superior Room 7,595usd — City views in BA; Upper floor in VLA", None, "These rooms have 1 king bed with private bathroom. Some can have 2 twin beds. Please inquire for availability.", bed="King", bath="Private", view="City views in Buenos Aires"),
    R("Garden Room", 7695, "Garden Room 7,695usd — Garden views in BA; Upper floor in VLA", None, "These rooms have 1 king bed with private bathroom. Some can have 2 twin beds. Please inquire for availability.", bed="King", bath="Private", view="Garden views in Buenos Aires"),
  ],
  extras=[],
  extras_note="Domestic flights from AEP to BRC are not included in the pricing. We will advise which flights to book 4 months prior to the dates."),

"yoga-retreat-morocco": dict(
  hotels=[
    dict(name=None, instagram=None, label="Riad in the Marrakech medina", text=[
      "Our Moroccan retreat adventure will take us to several locations highlighting the best of the country. We begin in our own riad in the heart of the medina in Marrakech.",
      "A traditional riad with stunning design, authentic voice and incredible staff. It’s home for our retreats for the first few nights and where we stay for extra nights in the city to relax and unwind from the bustle of the souks.",
    ]),
    dict(name=None, instagram=None, label="Countryside domes near Essaouira", text=[
      "Moving on to the laid back coastal vibes of Essaouira, we embrace serenity and calm in the stunning countryside accommodations of our long standing partners in Morocco. A quick drive to the city center but far enough away to enjoy the peace of the area.",
      "Unique domes house each room with a common space poolside for meals, relaxation and connection. All rooms have private bathrooms en-suite, gorgeous thoughtful decor and plenty of time and space for contemplation in nature.",
      "Our favorite part is the new studio with views of the surrounding argan and olive groves. Perfect for incredible classes, introspection and peace.",
    ]),
  ],
  rooms=[
    R("Shared Twin Room", 4995, "4,995usd per person", "per person", "Shared Twin rooms have 2 twin beds, ensuite bathroom, and adorable details. Each room in the hotel is unique.", bed="Twin", bath="Private"),
    R("Private Room", 5995, "5,995usd private", "private room", "1 Double bed, ensuite bathroom, and charming details. Each room is unique.", bed="Double", bath="Private"),
    R("Private Room — shared bed", 4955, "4,955usd shared bed per person", "per person", "Shared-bed rate in a private room. You must provide your bedmate for this option.", bed="Double", bath="Private"),
  ],
  extras=[],
  extras_note="We will pair single travelers together looking to share a room. Roommates should book their accommodations together."),

"yoga-retreat-day-of-the-dead-oaxaca": dict(
  hotels=[dict(name=None, instagram=None, text=[
    "Step into the charm of a bygone era with our exclusive private accommodations in a stately mansion built in the last century. Nestled just 15 minutes from the heart of Oaxaca City, this extraordinary property is one of the few that retains its original furnishings, offering a unique and authentic experience.",
    "Surrounded by lush gardens, the villa provides a tranquil retreat where you can wake to the gentle melody of birdsong and enjoy the serenity of nature. Take a barefoot stroll across the grounds to reconnect with the earth, or explore the peaceful surroundings with a leisurely walk or jog.",
    "The villa is more than just a place to stay—it’s an invitation to indulge in life’s simple pleasures. From intimate gatherings under the stars to morning walks where the earth meets your feet, this is a space that encourages relaxation, reflection, and connection.",
  ])],
  rooms=[
    R("Shared Triple Room", None, "2027 PRICING COMING SOON", "per person", "3 twin beds and an ensuite bathroom.", bed="Triple", bath="Private"),
    R("Shared Double Room with shared bath", None, "2027 PRICING COMING SOON", "per person", "2 twin beds and a shared bathroom with one other room.", bed="Twin", bath="Shared"),
    R("Shared Double Room with private bath", None, "2027 PRICING COMING SOON", "per person", "2 twin beds and a private bathroom.", bed="Twin", bath="Private"),
    R("Private Room", None, "2027 PRICING COMING SOON", "private room", "King bed with private ensuite bathroom.", bed="King", bath="Private"),
    R("Private Room — shared bed", None, "2027 PRICING COMING SOON", "per person", "King bed with private ensuite bathroom, shared-bed rate. You must provide your own bedmate.", bed="King", bath="Private"),
  ],
  extras=[],
  extras_note="The old site shows 2027 prices as “XXusd” with the note “2027 PRICING COMING SOON”, so every price here is null. Space in the private villa is limited, so guests are asked to be open to sharing a room."),

"treasures-of-thailand-a-yoga-wellness-retreat": dict(
  hotels=[
    dict(name=None, instagram=None, label="5 nights in Chiang Mai Old City", text=[
      "Our first 5 nights will be enjoyed tucked inside the Old City walls in a stunning, classical Thai hotel with saltwater swimming pool and intricate details throughout.",
      "We will practice our yoga at the hotel and enjoy breakfasts daily as well.",
      "Deluxe Suite options are along the ground floor with access directly to the swimming pool from your private terrace.",
      "Our Superior private or shared room options have a charming wooden ceiling with beautiful details like bath products, robes and Thai decor.",
    ]),
    dict(name=None, instagram=None, label="4 nights beachfront on Koh Yao Noi", text=[
      "Then we make our way to the stunning beaches of southern Thailand on the island of Koh Yao Noi where we will be staying beachfront at a secluded, gorgeous and scenic hotel for our final four nights. Yoga daily, spa services available, kayaking, beaches and villages to explore nearby.",
      "Superior rooms have private or shared room options tucked into the jungle. Interior WC and sink area with open-air shower and balcony with jungle views.",
    ]),
  ],
  rooms=[
    R("Superior — Private Room", 7455, "7,455usd", "private room", "Private bathroom & balcony in Chiang Mai; jungle view room with private bath at the beach.", bath="Private"),
    R("Superior — Shared Double Bed", 6495, "6,495usd per person", "per person", "Shared-bed rate. You will be required to take a Private Superior if there is no roommate available.", bed="Double", bath="Private"),
    R("Shared Room — Queen Bed", 6595, "Queen Bed 6,595usd", "per person", "In Koh Yao Noi the shared room has a queen and a twin bed.", bed="Queen", bath="Private"),
    R("Shared Room — Twin Bed", 6395, "Twin Bed 6,395usd", "per person", "In Koh Yao Noi the shared room has a queen and a twin bed.", bed="Twin", bath="Private"),
  ],
  extras=[],
  extras_note="We stay in 2 different venues during the 9 night retreat: 5 nights in Chiang Mai Old City and 4 nights on Koh Yao Noi. Space is limited to 10 students."),
}
