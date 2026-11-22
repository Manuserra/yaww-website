/* ============================================================
   YAWW — TEACHERS CATALOG
   Single source of truth for teacher names, photos and bios. The
   retreat cards read the name from here, and teachers.html plus each
   teacher page read everything else.

   Maps to WordPress as a `teacher` CPT:
     id / slug  -> post slug          bio       -> post content (array of paragraphs)
     name       -> post title         photo     -> featured image
     avatar     -> ACF image (square, face-centred crop for small circles)
     shortBio   -> ACF text (the card blurb)
     pageUrl    -> permalink

   Optional profile slots, each rendered only when filled:
     quote       -> one line in her own words (ACF text)
     specialties -> short list of styles (ACF repeater / tags)
     video       -> YouTube or Vimeo embed URL of her teaching
     spotify     -> Spotify playlist URL or id
     role        -> overrides the "Teacher" eyebrow (e.g. Photographer)
     shortName   -> what she goes by, for "More about Anj"
     photoPosition -> CSS object-position for the portrait crop

   `needsContent: true` marks a teacher who has retreats on the books
   but no bio or photo written yet. Her page is built and linked, and
   shows a clearly-marked placeholder instead of invented copy. Fill
   in `photo`, `shortBio` and `bio` and drop the flag — nothing else
   has to change.

   `tbd` is the stand-in for a retreat whose teacher is not announced:
   it has no page and is skipped by the roster and the index.
   ============================================================ */
window.YAWW_TEACHERS = [
  {
    id: "alicia-delaney",
    name: "Alicia Delaney",
    slug: "alicia-delaney",
    photo: "images/people/alicia-delaney-1.jpg",
    avatar: "images/people/alicia-delaney-1.jpg",
    pageUrl: "teacher-alicia-delaney.html",
    photoPosition: "50% 30%",
    specialties: ["Restorative", "Yoga nidra", "Pranayama", "Reiki"],
    shortBio: "Alicia teaches gentle, nervous-system-informed yoga that makes room for rest. Expect slow pacing, open choices and plenty of laughter, with only a little sweat.",
    bio: [
      "Alicia Delaney (E-RYT 200, YACEP) has a deep appreciation for quiet. A natural nurturer and teacher, she is steeped in the work of helping others cultivate more pause in their busy lives, and making their lives less busy in the process. Alicia’s nervous system-informed understanding of yoga and pranayama lend her offerings a consistent quality of gentleness. Gathering in circle to amplify these practices allows students to tenderly meet themselves with more compassion and grace again and again and again.",
      "Her mission is to guide students toward ease and delight in their bodies through relaxed pacing, open choice sequencing, and invitational language. When practicing together, you can expect contemplation, curiosity, laughter, and empowering movement with only a little sweat. Her classes are an exploration of the slower and spiritual side of yoga, an opportunity to step out of our habits of rushing and performance so we can really hear what our bodies have to teach us. Uncovering what we so often hide about ourselves, from ourselves, can be a transformative experience. But doing so with the support of a group of like-minded friends in a beautiful setting on retreat? Life-changing.",
      "Always seeking out the medicine of rest and ways to share it with the world, Alicia has studied with internationally-renowned yoga therapist, Jillian Pransky, is attuned as an Usui Reiki practitioner, holds certification as a yoga nidra guide from the Shiva Shakti School of Yoga & Healing Arts, and is trained in Hot Stone Restorative Yoga and its applications for deeper states of ease.",
      "When she is not teaching in a studio in the heart of the midwest, Alicia is sharing the magic of relaxation through her platform, Slow & Steady Yoga. Join her to create a retreat rich with reverence, rest, reflection, and joy that’ll make your cheeks hurt from smiling."
    ]
  },
  {
    id: "alison-buchanan",
    name: "Alison Buchanan",
    slug: "alison-buchanan",
    photo: "images/people/alison-buchanan-1.jpg",
    avatar: "images/people/alison-buchanan-1.jpg",
    pageUrl: "teacher-alison-buchanan.html",
    photoPosition: "55% 30%",
    quote: "Strength comes from within.",
    specialties: ["Strength", "Alignment", "Restorative", "Meditation"],
    shortBio: "Alison builds strong, playful classes on a deep love of alignment and anatomy. Mornings are for strength, evenings for restorative practice and meditation.",
    bio: [
      "Alison’s motto is “strength comes from within.” By leading from the heart, and believing in our innate power, she feels that every one of our goals is achievable.",
      "Alison brings this philosophy to her teaching, where she aims to create challenging, yet playful sequences. Her classes are holistic and mindful, and always tuned to what students need in the moment. Deep down, Alison is an alignment and anatomy nerd who loves to share her knowledge with the aim of helping people move better, and thus feel better, in their bodies.",
      "Before moving to Seattle, Alison was a longtime teacher in the San Francisco Bay Area. She has taught yoga professionally for 13 years, and continues to advance her yogic studies with dynamic asana and mediation teachers nationwide. During yoga retreats with Alison, you can expect her to be an engaged leader who offers a strength-focused yoga class in the morning, and a restorative/meditation class in the evening. She’ll be a person with whom you can authentically connect on-and-off the mat. She’ll also be an active participant, who’ll happily go adventuring with you, and then reminisce about it all over a glass a wine at the end of the day.",
      "Come experience adventure, excitement, restoration and all-around fun with her on a life-changing retreat."
    ]
  },
  {
    id: "amy-appel",
    name: "Amy Appel",
    slug: "amy-appel",
    photo: "images/people/amy-appel-1.jpg",
    avatar: "images/people/amy-appel-1.jpg",
    photoPosition: "50% 25%",
    pageUrl: "teacher-amy-appel.html",
    quote: "Move with joy. Savor the experience.",
    shortBio: "Amy creates warm, uplifting classes known for their compassion and attention to detail, balancing strength, softness and ease for every student.",
    bio: [
      "Amy creates a warm and uplifting space where students are invited to move with intention while embracing the beauty and rhythm of the present moment.",
      "Known for her compassionate teaching and attention to detail, her classes offer a balance of strength, softness and ease, meeting each student exactly where they are.",
      "On her retreat in Paris and Champagne, her guidance invited guests to slow down, connect more deeply, and savor both the practice and the experience, on and off the mat."
    ]
  },
  {
    id: "amy-sennett-starner",
    name: "Amy Sennett-Starner",
    slug: "amy-sennett-starner",
    photo: "images/people/amy-sennett-starner-1.jpg",
    avatar: "images/people/amy-sennett-starner-1.jpg",
    photoPosition: "35% 30%",
    pageUrl: "teacher-amy-sennett-starner.html",
    shortBio: "Amy's teaching is shaped by her life as a dancer. Her classes go beyond form into body, breath and awareness, rooted in strength, softness and renewal.",
    bio: [
      "Amy brings an intuitive approach to teaching, shaped by her life as a dancer and her years guiding movement. Her classes invite you beyond form, into a deeper connection with body, breath and awareness.",
      "With warmth and presence, she encourages curiosity, self-respect and a practice rooted in strength, softness and renewal."
    ]
  },
  {
    id: "andrea-manitsas",
    name: "Andrea Manitsas",
    shortName: "Anj",
    slug: "andrea-manitsas",
    photo: "images/people/andrea-manitsas-1.jpg",
    avatar: "images/people/andrea-manitsas-1.jpg",
    photoPosition: "50% 20%",
    pageUrl: "teacher-andrea-manitsas.html",
    specialties: ["Rhythmic yoga", "Breath", "Music", "Mantra"],
    shortBio: "Anj weaves movement, music, breath, scent and mantra into creative, rhythmic yoga. Her retreats are a seasonal reset: a soft ignition and a remembering.",
    bio: [
      "Andrea goes by Anj. She blends movement, music, breath, scent, inquiry and mantra into creative, rhythmic yoga.",
      "Her El Cuyo retreat, The Winter Thaw, brought that mix to an open-air studio steps from the turquoise water of the Yucatán: a seasonal reset, a soft ignition and a remembering."
    ]
  },
  {
    id: "bonnie-erickson",
    name: "Bonnie Erickson",
    slug: "bonnie-erickson",
    photo: "images/people/bonnie-erickson-1.jpg",
    avatar: "images/people/bonnie-erickson-1.jpg",
    photoPosition: "50% 35%",
    pageUrl: "teacher-bonnie-erickson.html",
    shortBio: "Bonnie sees yoga as curiosity, connection and caring for ourselves. She brings movement, music, plenty of laughter and a little fire to each day.",
    bio: [
      "Bonnie believes yoga is less about striving and more about curiosity, connection and caring for ourselves, with plenty of laughter along the way.",
      "In Montenegro, she'll weave movement, music and a little fire into each day, with energizing mornings, softer evenings, and space to let go of what we're ready to leave behind.",
      "Come ready to move, breathe, laugh and connect. The rest? Throw it into the fire."
    ]
  },
  {
    id: "carie-cook",
    name: "Carie Cook",
    slug: "carie-cook",
    photo: "images/people/carie-cook-1.jpg",
    avatar: "images/people/carie-cook-1.jpg",
    photoPosition: "50% 40%",
    pageUrl: "teacher-carie-cook.html",
    quote: "The greatest gift we can give ourselves is self-knowledge.",
    specialties: ["Vinyasa", "Guided journalling", "Tarot", "Intention setting"],
    shortBio: "A relational therapist and yoga teacher, Carie holds space to slow down and listen inward, reconnecting with the body, the breath and the heart.",
    bio: [
      "A relational therapist and yoga teacher, Carie holds space to slow down, listen inward, and reconnect with the body, breath and heart, offering an invitation to pause and remember who you already are.",
      "On retreat, her days bring Vinyasa and guided journalling, with tarot reading, intention setting and vision boarding along the way."
    ]
  },
  {
    id: "dawn-thie",
    name: "Dawn Thie",
    slug: "dawn-thie",
    photo: "images/teachers/dawn-thie.jpg",
    avatar: "images/teachers/avatars/dawn-thie.jpg",
    pageUrl: "teacher-dawn-thie.html",
    quote: "Find your true self.",
    specialties: ["Sivananda Hatha", "Breathwork", "Trauma-informed"],
    shortBio: "Dawn guides students toward a deeper relationship with Self. Rooted in Sivananda Hatha, her trauma-informed classes are classical with a modern, accessible twist.",
    bio: [
      "Find Your True Self. It is Dawn’s highest intention to help guide students to a deeper relationship with Self. By moving our bodies and our energy, we free our soul to be seen and we can connect to parts of Self that are waiting to be explored.",
      "Dawn carefully designs classes to clear the energetic channels within the body to create more space and open the path to inner peace. Incorporating the 8 Limb Path of Yoga, she invites students to explore something deeper within themselves using asana, breath work, intention and focus, in a way that meets students where they are. Feeling into the energy present in the room, Dawn uses her authentic voice offering clear and concise guidance, while weaving in yoga philosophy and heartfelt intention for both a physical and spiritual experience. All through a trauma informed approach.",
      "When we connect to Self, we begin to experience the innate peace within and thus walk along this planet more peacefully. Dawn has been leading retreats nationally and internationally, as well as teacher trainings and workshops around the country. Originally trained in the traditional Sivananda Hatha style of yoga, Dawn’s classes offer a classical approach to yoga with a modern twist of accessibility. To learn more about Dawn please check her website, I AM Dawn Thie Yoga."
    ]
  },
  {
    id: "genevieve-morin",
    name: "Genevieve Morin",
    slug: "genevieve-morin",
    photo: "images/teachers/genevieve-morin.jpg",
    avatar: "images/teachers/avatars/genevieve-morin.jpg",
    pageUrl: "teacher-genevieve-morin.html",
    shortBio: "Genevieve has led retreats around the world for 20 years. Trilingual in English, French and Spanish, she uses yoga and movement as a language without borders.",
    bio: [
      "Genevieve has been leading retreats internationally for the last 20 years.  She is passionate about worldwide travel, full immersion in different cultures, and the importance of broadening our understanding of one another.",
      "As a trilingual yoga teacher, speaking English, French and Spanish, verbal and non-verbal communication are at the core of her interests.  Like cooking or creating music, moving the body, dancing and yogic expression are international, borderless forms of communication.  The potential of connection to ourselves, to one another, and to a universal energy is boundless.",
      "Even when Genevieve travels on retreat to a place where common language is not spoken, sharing the language of yoga and movement is always possible.  Genevieve loves leading a group of people to unique destinations, especially those who may feel timid or shy in a foreign place.  To offer an opportunity for people to experience our human oneness is pivotal and life-changing.  We are all human beings with similar needs and desires, and despite borders or differences, we all yearn toward meaningful connections.  Genevieve is endlessly intrigued by how simple life truly is… and how simple and joyful our journeys can be.",
      "Discover more about Genevieve on her website, Truro Yoga."
    ]
  },
  {
    id: "jamie-coffey",
    name: "Jamie Coffey",
    slug: "jamie-coffey",
    photo: "images/people/jamie-coffey-1.jpg",
    avatar: "images/people/jamie-coffey-1.jpg",
    photoPosition: "50% 25%",
    pageUrl: "teacher-jamie-coffey.html",
    quote: "A fresh start, your way.",
    specialties: ["Breath-led yoga", "Mindful movement", "Stillness"],
    shortBio: "Jamie weaves breath-led yoga, mindful movement and moments of stillness into an easy rhythm. Come as you are, and leave feeling more alive.",
    bio: [
      "A new year doesn't have to mean a new you. Sometimes all we need is a little space to breathe, move and reconnect with ourselves.",
      "On the quiet shores of El Cuyo, Jamie weaves together breath-led yoga, mindful movement and moments of stillness. It's an easy rhythm that lets you step away from the everyday and welcome the year ahead with fresh energy and intention.",
      "Come as you are. Leave feeling more alive."
    ]
  },
  {
    id: "jenniferlyn-chiemingo",
    name: "Jenniferlyn Chiemingo",
    slug: "jenniferlyn-chiemingo",
    photo: "images/teachers/jenniferlyn-chiemingo.jpg",
    avatar: "images/teachers/avatars/jenniferlyn-chiemingo.jpg",
    pageUrl: "teacher-jenniferlyn-chiemingo.html",
    specialties: ["Vinyasa", "Integrative Yoga Therapy"],
    shortBio: "JL teaches flowing, grounding vinyasa built on joy, love and connection. She supports newcomers with care and keeps regular students finding new depth.",
    bio: [
      "Jenniferlyn is known affectionately by her students + friends as JL…and the words joy, love, and yoga surely describe her best!",
      "Through yoga on the mat and in travel, JL’s goal is to provide her students with the opportunity to experience genuine joy, love, and connection (a.k.a. yoga). JL’s unique yoga teaching style is a creation all her own and an experience from which all can benefit. They are flowing, grounding, spiritual and inspiring. When you step away from one of her Vinyasa classes, you will feel more connected to yourself and the world around you. She provides loving support to newbies while continually challenging regular practitioners to find greater depths in their practice.",
      "JL first experienced yoga in 2000 when she was looking for a way to escape the daily stress from her job as a TV news producer in northern California. That was when yoga started to change her life. Overcome by the power and benefits of yoga in her own life, JL decided to move her career from the newsroom to the yoga mat in 2002. With curiosity and ambition, she followed her dream and began her training at Kripalu Center for Yoga & Health with an emphasis in Integrative Yoga Therapy. JL continued her training with Shiva Rea, embracing Vinyasa yoga. Through Vinyasa, she learned the powerful energizing and cleansing benefits of linking breath with movement.",
      "JL is listed E-RYT with Yoga Alliance and has taught for more than 22 years in Seattle and the Bay Area. After more than 15 years leading international yoga retreats she partnered with Yoga & Adventures Worldwide in 2021 to bring travelers on our bespoke yoga adventures and beyond. These intimate getaways have the potential to change your life if you are open to the possibility…"
    ]
  },
  {
    id: "melanie-cordover",
    name: "Melanie Cordover",
    slug: "melanie-cordover",
    photo: "images/teachers/melanie-cordover.jpg",
    avatar: "images/teachers/avatars/melanie-cordover.jpg",
    pageUrl: "teacher-melanie-cordover.html",
    specialties: ["Dynamic flow", "Yin"],
    shortBio: "A dancer from Miami turned yoga teacher, Melanie teaches dynamic flows and traditional yin. Her creative classes build confidence on and off the mat.",
    bio: [
      "A native Floridian from Miami, Melanie grew up dancing and turned her love for movement into a prolific and promising yogic career. Melanie teaches dynamic yoga studio classes and traditional yin classes weekly. She has led retreats in South Florida and the Caribbean over the past 3 years, and truly loves the experience of connecting and creating memorable and meaningful moments. Melanie is a E-RYT 500 hour instructor with an advanced certification in Yin yoga.",
      "Her yogic journey has been an incredible ride to empower yogis to find confidence and strength in their practice and to drive that beyond the mat. She aspires to inspire others by weaving thematic messages and creative flows in a mindful, beautiful and peaceful setting with her enthusiasm and invigorating energy.  Join Melanie for a journey within a journey to find your passion and purpose through love, light and liberty within."
    ]
  },
  {
    id: "rachel-gray",
    name: "Rachel Gray",
    slug: "rachel-gray",
    role: "Photographer",
    photo: "images/retreat-photos/teachers/yoga-teacher-rachel-gray-portrait.webp",
    avatar: "images/teachers/avatars/rachel-gray.jpg",
    photoPosition: "50% 15%",
    pageUrl: "teacher-rachel-gray.html",
    specialties: ["Boudoir photography", "Portrait photography"],
    shortBio: "Rachel is the photographer behind our boudoir and portrait retreats. Her sessions are about being witnessed and letting yourself be seen, on your own terms.",
    bio: [
      "On our Embodied retreats in El Cuyo, you'll be guided through an intimate boudoir and portrait photography journey with world-class photographer Rachel Gray.",
      "This is not about performing or getting it right. It's about being witnessed, about uncovering, and about letting yourself be seen, on your own terms."
    ]
  },
  {
    id: "sue-berger",
    name: "Sue Berger",
    slug: "sue-berger",
    photo: "images/teachers/sue-berger.jpg",
    avatar: "images/teachers/avatars/sue-berger.jpg",
    pageUrl: "teacher-sue-berger.html",
    specialties: ["Yoga", "Barre", "Personal training", "Life coaching"],
    shortBio: "With three decades in wellness, Sue teaches inspiring, challenging classes that stay accessible to every level. Expect to sweat, laugh and move to great music.",
    bio: [
      "Meet Sue, a seasoned wellness expert with over three decades of experience in the industry. Certified as a yoga instructor, barre instructor, personal trainer, and life coach, her multifaceted expertise reflects her deep commitment to holistic health and wellness.",
      "Sue’s journey with yoga began when she surrendered to the flow, finding harmony between mind, body, and soul. In her classes, Sue creates an atmosphere that is both inspiring and challenging, yet always accessible to individuals of all fitness levels. Get ready to sweat, laugh, groove to great music, and witness the transformative results you seek.",
      "With a focus on fostering self-love and acceptance, Sue strives to cultivate a safe and nurturing space in her yoga classes. Whether you practice with her virtually, in-studio, or embark on a yoga adventure, she is dedicated to guiding and supporting you on your wellness journey. Join her on the mat and experience the joy of moving, breathing, and embracing your authentic self."
    ]
  },
  {
    id: "yessica-puebla",
    name: "Yessica Puebla",
    slug: "yessica-puebla",
    photo: "images/people/yessica-puebla-1.jpg",
    avatar: "images/people/yessica-puebla-1.jpg",
    photoPosition: "50% 30%",
    pageUrl: "teacher-yessica-puebla.html",
    quote: "Strength isn't how much you can override.",
    specialties: ["Yoga", "Pilates", "Strength training", "Breathwork"],
    shortBio: "Founder of The Embodied Strength Method, Yessica blends yoga, Pilates, strength and breathwork into classes that are educational and genuinely playful.",
    bio: [
      "With more than a decade in movement and holistic health, Yessica, founder of The Embodied Strength Method, blends yoga, Pilates, strength training and breathwork into classes that are intentional, educational and genuinely playful.",
      "She'll teach you how to move, why you're moving, and what your body has been trying to say all along. She'll challenge you, make you laugh, and probably get you dancing on the mat.",
      "No flexibility, fitness or spirituality required."
    ]
  },

  { id: "tbd", name: null, placeholderLabel: "a YAWW instructor", unannounced: true }
];

/* Lede for teachers.html */
window.YAWW_TEACHERS_INTRO = "Our teachers bring grounded guidance and soulful presence to every retreat. Each one offers thoughtful practices that honor the body, steady the mind and invite deeper connection along the way.";
