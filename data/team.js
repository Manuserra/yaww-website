/* ============================================================
   YAWW — TEAM CATALOG
   The people who run the retreats, as opposed to the people who
   teach on them (those live in data/teachers.js). Someone can be in
   both: Jenniferlyn Chiemingo and Renata Centi are team AND teachers,
   with a different blurb for each role, which is why the two
   catalogs are separate rather than one list with a flag.

   Maps to WordPress as a `team_member` CPT, same shape as `teacher`:
     id / slug -> post slug     bio      -> post content (paragraphs)
     name      -> post title    photo    -> featured image
     shortBio  -> ACF text (the card blurb)
     pageUrl   -> permalink

   Shown on facilitators.html (the file keeps its old name so nothing
   that reads YAWW_TEAM breaks). `listed: false` keeps a person's data
   without a card: Marina and Fleur keep an unlisted page because
   retreat pages still link to it, and JL now points to her teacher
   page. Christina's photo is the old candid: no real portrait exists
   yet (see docs/people-assets.md).

   Order is deliberate, not alphabetical: the founder opens the grid.
   ============================================================ */
window.YAWW_TEAM = [
  {
    id: "christina-thomas",
    name: "Christina Thomas",
    slug: "christina-thomas",
    photo: "images/team/christina-thomas.jpg",
    pageUrl: "facilitator-christina-thomas.html",
    shortBio: "Founder of YAWW, Christina brings global retreat expertise and a clear, creative vision. She leads with intention, precision, and a deep appreciation for meaningful travel.",
    bio: [
      "In 2008 Christina left her fancy, big city Interior Design career to follow her heart in the Caribbean of Tulum, Mexico. After many amazing (and bumpy) serendipitous turns, she has founded Utopia Guesthouse & Yoga Studios in Tulum, Havana and now El Cuyo, Yucatan. She started Yoga Adventures Tulum in 2008 and Yoga & Adventures Worldwide in 2010, offering unique, affordable retreat adventures in some of the world’s most fascinating locations.",
      "Her passion for yoga began back in 1994 in a college Kundalini course. Since then she has studied and practiced with many well-known masters and aspiring ones. She acquired her RYT-200 in 2012 from the Institute of Interdisciplinary Yoga Studies with a focus on Iyengar, Ashtanga, and Viniyoga traditions. Although she greatly enjoys leading asana practices she prefers to be on the mat as a student. Hence, she is the creator and (sometimes) host during her bespoke retreats.",
      "She has been planning and hosting retreats around the world since 2009 and traveling on her own since she was 5 years old. Each destination she chooses holds a special place in her heart. Join us to find out why!"
    ]
  },
  {
    id: "marina-coria",
    listed: false,
    name: "Marina Coria",
    slug: "marina-coria",
    photo: "images/team/marina-coria.jpg",
    pageUrl: "facilitator-marina-coria.html",
    shortBio: "Marina brings calm clarity and thoughtful care to our retreats. Based in Barcelona, she blends human insight with grounded organization throughout.",
    bio: [
      "Marina was born in Neuquén, Argentina, in the valleys of northern Patagonia, and has made Barcelona her home since 2010. A professional astrologer with a humanistic approach, she combines symbolic insight with a grounded sense of care and organization.",
      "With more than a decade of experience in hospitality, Marina has a natural talent for making people feel welcome. She brings empathy, clarity, and attention to detail to her role as facilitator — from the flow of logistics to the atmosphere of trust and ease. Rooted in her connection with nature, she creates spaces that are both harmonious and human, where each person can feel supported throughout their journey."
    ]
  },
  {
    id: "sofi-von-linden",
    name: "Sofi von Linden",
    slug: "sofi-von-linden",
    photo: "images/team/sofi-von-linden.jpg",
    pageUrl: "facilitator-sofi-von-linden.html",
    shortBio: "Sofi brings a grounded, steady presence to our retreats, offering clear guidance and a thoughtful global perspective to every group she leads.",
    bio: [
      "Sofi has been an integral part of our journey since the very beginning, joining us at the original Utopia Guesthouse & Yoga Studio in Tulum back in 2012. Over the years, her presence has become synonymous with the essence of our retreats—a perfect blend of warmth, wisdom, and unforgettable experiences.",
      "Hailing from Buenos Aires, Argentina, Sofi is a true citizen of the world. Her adventures have taken her far and wide, including many years living in the magical energy of Tulum, Mexico, and now calling Barcelona her home. Her global perspective and deep understanding of diverse cultures add an enriching layer to every retreat she leads.",
      "As our original retreat facilitator, Sofi is the heartbeat of our team. Her return to the helm, guiding retreats across Europe and beyond, feels like a homecoming in every sense. With her unique mix of compassion, humor, and laser-sharp focus, she ensures that every guest feels seen, supported, and cared for. Whether it’s holding space for personal growth, guiding transformative experiences, or simply sharing a laugh, Sofi creates an environment where you can truly thrive.",
      "Her natural ability to connect with people, coupled with her wealth of experience, makes her an unparalleled retreat leader. We are beyond grateful to have Sofi on our team, leading retreats that bring people together in the most meaningful and magical ways. Get ready for an unforgettable adventure with Sofi!"
    ]
  },
  {
    id: "fleur-optekamp",
    listed: false,
    name: "Fleur Optekamp",
    slug: "fleur-optekamp",
    photo: "images/team/fleur-optekamp.jpg",
    pageUrl: "facilitator-fleur-optekamp.html",
    shortBio: "Fleur brings global perspective and design sensibility to our retreats in Asia, supporting each experience with clarity and thoughtful coordination.",
    bio: [
      "Fleur has been a lifelong friend of YAWW, always inspiring us with her adventurous spirit, passion for travel, and deep love for wellness. As we expand our offerings to Southeast Asia, she was the natural choice to lead the way. She and Christina, our founder, attended Interior Design school together in North Carolina, worked side by side in a high-end residential firm in NYC for years and have remained close over the past decades as life has led them on their global paths.",
      "Born in Guam, where her father worked as a hotel manager, Fleur grew up immersed in different cultures, living in Malaysia, Saudi Arabia, the U.A.E., Germany, and the United States. Now based in Melbourne, Australia she continues to embrace a global lifestyle, always seeking new experiences, meaningful connections, and fresh sources of inspiration.",
      "Her wanderlust and curiosity shine through in everything she does—whether she’s exploring hidden gems, getting lost in art museums, grounding herself with yoga and hiking, or dancing under the stars at a live music event. Fleur has her own freelance design firm curating inspirational corporate interiors in the hotel world and beyond. We are so honored that she makes time for facilitating our retreats together.",
      "With impeccable attention to detail, incredible problem-solving skills, and an energy that makes every experience more fun, Fleur is the perfect person to curate and care for our retreats in Asia.",
      "We couldn’t be more excited to have her on board—get ready for some unforgettable adventures with Fleur at the helm!"
    ]
  },
  {
    id: "karolina-quesada",
    name: "Karolina Quesada",
    slug: "karolina-quesada",
    photo: "images/team/karolina-quesada.jpg",
    pageUrl: "facilitator-karolina-quesada.html",
    shortBio: "Karolina brings cultural fluency and grounded ease to every retreat. Based in Havana, she adds clear insight, warm presence, and thoughtful care throughout.",
    bio: [
      "Karolina was born in Bonn, Germany, to German and Serbian roots, giving her a rich and diverse cultural heritage that has shaped her adventurous and dynamic spirit. From a young age, she has traveled the world, embracing new experiences and cultures with enthusiasm and grace.",
      "Her life is a mosaic of talents and accomplishments, spanning roles as a restaurant owner, fashion designer, producer, stylist, and hotel manager, among many others. Fluent in an astounding seven languages, Karolina’s linguistic abilities and cross-cultural fluency allow her to connect effortlessly with people from all corners of the globe.",
      "For years, Karolina has been an integral part of our team, based full-time in the vibrant city of Havana. Her deep understanding of local culture, coupled with her global perspective, makes her a cornerstone of our retreats, adding authenticity, creativity, and warmth to every experience she helps craft.",
      "Whether she’s guiding guests through the streets or sharing her incredible stories, Karolina brings unmatched energy and expertise to her role. Her ability to weave together her German precision, Serbian passion, and worldly experience makes every retreat a transformative journey.",
      "We are incredibly fortunate to have Karolina leading retreats with us across the globe. Her unique blend of skills, charisma, and genuine care ensures that every guest leaves with unforgettable memories and a deep sense of connection."
    ]
  },
  {
    id: "jenniferlyn-chiemingo",
    listed: false,
    name: "Jenniferlyn Chiemingo",
    slug: "jenniferlyn-chiemingo",
    photo: "images/team/jenniferlyn-chiemingo.jpg",
    pageUrl: "teacher-jenniferlyn-chiemingo.html",
    shortBio: "JL supports our inbox and guest communications with clarity, care, and deep retreat knowledge. She keeps everything running smoothly behind the scenes.",
    bio: [
      "Helping with all your email needs, inquiries and plans…JL is on the case! We are so grateful to have her helping out with our inbox. She’s taught most of our retreats so she knows the answers and we are thrilled to have her support."
    ]
  },
  {
    id: "renata-centi",
    name: "Renata Centi",
    slug: "renata-centi",
    photo: "images/people/renata-centi-1.jpg",
    photoPosition: "50% 35%",
    pageUrl: "facilitator-renata-centi.html",
    shortBio: "Renata brings warm presence and steady care to our retreats in El Cuyo and Sardinia. A teacher and therapist, she supports each guest with clarity, ease, and quiet strength.",
    bio: [
      "Renata lives in the tranquil seaside paradise of El Cuyo, where she wears many hats as a yoga teacher, massage therapist, and retreat facilitator. Originally from the lush and rolling landscapes of Umbria, Italy, Renata brings a blend of European charm and soulful warmth to every experience she curates.",
      "With a deep passion for wellness and connection, Renata is a cornerstone of our retreats in both Sardinia and El Cuyo. Her yoga sessions are designed to nurture the mind and body, while her expert massage therapy offers a healing touch that leaves guests feeling renewed and restored.",
      "Beyond her professional skills, it’s Renata’s genuine kindness and grounded presence that make her truly unforgettable. She creates a welcoming space where every guest feels cared for and valued, whether it’s on the mat, during a heartfelt conversation, or while sharing in the magic of a retreat adventure.",
      "Renata’s ability to seamlessly blend wellness, hospitality, and cultural richness ensures that every retreat she facilitates is as inspiring as it is rejuvenating. Join her in Sardinia or El Cuyo and experience the unique energy she brings to every moment—you’ll leave feeling transformed in all the best ways."
    ]
  }
];

/* Lede for facilitators.html */
window.YAWW_TEAM_INTRO = "A facilitator from our team travels with every group. They know the place, handle every detail and look after each guest, so all you have to do is be there.";
