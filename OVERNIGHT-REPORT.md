
## 4 · Cards de retreats (opción)
Copia de `retreats.html` en `proposals/retreats-cards/` con interruptor "Cards" (Ours / Premium-style; tecla C). ON: texto a la izquierda, fecha arriba del todo con el estilo de premium (Raleway 11/600, tracking .18em, mayúsculas, #877C71), nombre como ahora, location en Libre Baskerville cursiva con el color de premium, teacher en nuestro estilo con el azul primario (`--sea-blue` #475764, el del logo). Solo CSS (flex `order`), sin tocar JS. Ojo: ese azul ya es el color del nombre, así que la línea del teacher se ve igual que antes; si querés que se distinga, hay que elegir otro azul. Recomendación: adoptar. Board: fila `retreats-cards`.

## 11 · Botones del hero (opciones)
`proposals/hero-buttons/index.html` (copia de la página El Cuyo Feb 2027) con interruptor A / B / C (tecla B). A actual. B: Book now sólido y "I want more info" como link subrayado con flecha (nunca se parecen). C: Book now en bronce sólido con sombra; More info en outline con fondo oscuro tenue que solo oscurece un poco al hover (nunca se vuelve idéntico). Textos y links sin cambios. Recomendación: C (B si se quiere lo más liviano). Board: fila `hero-buttons`.

## 12 · Retreats > 1440 (opción)
`proposals/retreats-wide/retreats.html` con interruptor "Width" (Capped 1440 / Fluid; tecla W). Fluid: por encima de 1440 el container crece hasta 1920 y las 12 columnas escalan (label de season = 3 col, tres cards = 3 col cada una). Medido a 1920: card 432 px y label 432 px (capped: 312). Por debajo de 1440 no cambia nada. Recomendación: adoptar con tope en 1920. Board: fila `retreats-wide`. También se agregó la entrada `contact-alt` en `topics.js` (la página puede no existir todavía).

## 1 · Encuadre de fotos del itinerario
- 275 imágenes únicas en los sliders de itinerario de las 26 páginas `retreatpage-*.html` (30 son `images/dayN-M.jpg`, ya recortadas a 672×406 y bien encuadradas; 245 son webp de `images/retreat-photos/`). 128 de las webp son verticales, que es donde el centro por defecto cortaba caras/manos.
- 135 reglas `object-position` en `css/photo-focus.css`, por nombre de archivo y agrupadas por location (sin reglas donde el centro ya funcionaba). Puntos de interés: cara/mano/animal/plato/monumento. Calculados contra la banda 28:17 (móvil y escritorio) y comprobados también contra un recorte cuadrado (días largos en laptop).
- Casos pedidos: lotus/mano en mudra (`hands-resting-in-meditation-seat`, mano centrada y entera), hornbill (centrado, entero), chica en cobra/upward dog (cara en el centro), elefante en las palmeras (entero, antes cortado). Verificado en navegador (Thailand, 1440) y en recortes simulados 28:17 y 1:1 de todas las reglas.
- No se reemplazó ningún archivo: no hizo falta buscar originales.
- Sin resolver (el sujeto no cabe en la banda 28:17 de una foto vertical, es limitación del archivo): `elephants-walking-the-track` (cabezas ya cortadas en el archivo), `triangle-pose-by-the-lake` (cara centrada, mano levantada cortada), `dancer-pose-above-the-water` (estatua entera no cabe), `legs-raised-in-yoga-class` (pies o manos quedan cortados), `holding-up-a-red-paper-flower` (cara y flor no caben juntas; quedó la flor con la cara a medias). Si se quiere verlas enteras, habría que ofrecerlas con otro formato o elegir otra foto.
- No verificado en móvil real; el cálculo usa la misma proporción (28:17 / 81:49).

## 2 · Santorini fuera

- `santorini-sep-2027-berger` (Breath Body Soul Retreat in Santorini, sep 2027) sale de `data/retreats.js`: desaparece de retreats, filtros y contadores (ahora 16), home, páginas de teacher y dropdowns. Sale también de `sitemap.xml`.
- `retreatpage-santorini-sep-2027-berger.html` se queda en el repo sin ningún enlace. `retreats/_index.json`, `retreats/santorini-sep-2027-berger/data.json` y `_tools/editions.py` aún lo mencionan (son fuente de build, no se leen en el sitio); si se vuelve a correr `build_page.py` no se regenera porque ya no está en `data/retreats.js`.
- El Breath Body Soul Retreat de El Cuyo (ene 2027) no se tocó.

## 10 · Fotos de personas

- Dropdowns de retreats: Camino de Santiago tiene miniatura (`images/location-thumbs/camino-de-santiago.jpg`, recorte del sendero musgoso de `retreat-photos/camino-de-santiago/`). Rachel Gray tiene avatar (`images/teachers/avatars/rachel-gray.jpg`, recorte cuadrado de la cara).
- Rachel Gray: foto del Embodied (`retreat-photos/teachers/yoga-teacher-rachel-gray-portrait.webp`) en teachers.html y en su página.
- Christina Thomas: `images/team/christina-thomas.jpg` ahora es la foto de Founder de about, recortada 3:4 con la cara arriba al centro (mismo path, así que no cambia nada más). El archivo anterior queda como `christina-thomas-old.jpg`. No aparece en las retreat pages.
- Renata Centi: nueva foto principal (El Cuyo, riendo, buena luz) en `images/people/renata-centi-1.jpg` (mismo path, 933x1400); la anterior queda como `renata-centi-prev.jpg`. Alternativas para el board: `images/people/renata-alt-1.webp` (sombrero y camisa blanca, verde de fondo, mirada de perfil) y `renata-alt-2.webp` (sombrero, pared amarilla de Izamal, cuerpo medio). Duda: las fotos de Cerdeña de esa carpeta parecen otra persona (pelo más claro); no las usé.
- Pedido al orquestador: ninguno; las retreat pages ya apuntan a `renata-centi-1.jpg`, que se cambió en el lugar.

## 6b · Teacher pages: Spotify, YouTube, links

- `js/people-cards.js` + `css/people.css` + comentario en `data/teachers.js`: campos opcionales por teacher `spotifyPlaylist` (URL o id), `youtube` (id o URL, se embebe con youtube-nocookie), `website` (string o `{url,label}`) e `instagram` (@usuario o URL). Los antiguos `spotify` y `video` siguen funcionando.
- Bloque "Listen & watch" (iframes lazy, YouTube 16:9, Spotify 352px) y links Website / Instagram bajo el bio con `target=_blank rel=noopener`. Campo vacío = no se pinta nada. Verificado con datos de prueba (ya revertidos) y sin ellos.
- URLs reales: no hay ninguna en el repo ni en el sitio actual (no tiene páginas de teacher; solo hay un video de YouTube del retreat de Tulum, que no es de un teacher). Todos los campos quedan vacíos para los 15 teachers. Dawn Thie ("I AM Dawn Thie Yoga") y Genevieve Morin ("Truro Yoga") mencionan su web en el bio, pero sin URL: preguntar.
- Nota: si hace falta `?v=` nuevo para `css/people.css`, `js/people-cards.js` y `data/*.js` lo sube el orquestador.

## 3 · Hover de cards

- Hover de premium en todas las cards con foto (`css/cards.css`): la foto se acerca a 105 % en 1,6 s con el ease de premium (0.22, 1, 0.36, 1), recortada dentro del marco; sin velo.
- Cubre: cards de retreat (retreats, home y rails, locations, teacher pages), locations y "more locations", teachers/facilitators, journal, posts relacionados, cards de About ("How we work") y room cards.
- Títulos como en premium: retreat y location sin subrayado; personas, journal y relacionados con subrayado que aparece en 0,5 s.
- El "Veil" de `base.css` queda neutralizado desde `cards.css` (no se tocó base.css). Solo con mouse real; con reduced motion no hay zoom.
- Verificado a 1440 en retreats, home (rail), locations, teachers y journal: zoom 1.05, sin saltos, sin errores de consola.

## 6a · More about en hover

- "More about {nombre}" invisible hasta pasar el mouse por la card; aparece con fade y sube 6 px. También aparece con foco de teclado (`:focus-within`). En touch siempre visible.
- Solo opacity/transform: la card no cambia de altura (verificado: 629 px antes y durante el hover).

## 7 · About · It takes a village

- 2 × 2 horizontal en la grilla: 6 + 6 columnas (672 × 408 a 1440, gutter 48), gutter 24 en ≤1024, una columna en celular (verificado a 1440, 1024 y 390).
- Sofi y Jenniferlyn conservan sus fotos (las mismas de sus cards); encuadre con `object-position` por foto. Ambas recortan bien en horizontal.
- Pregunta: los archivos son 672 × 888; en pantallas retina el recorte horizontal queda algo blando. ¿Hay originales de mayor resolución de esas dos fotos? En la biblioteca (Teachers/Team Sofi y Teacher Jennieferlyn) hay fotos de varias personas mezcladas y no pude confirmar cuál es Sofi, así que no cambié ninguna.
- Manu y Maggie siguen sin foto ni rol (`[missing data]`).

## 8 · Contact

- Se fue "You might be looking for". La columna izquierda ahora es: datos directos, después "Since 2008" · "4.9 on Google · 45 reviews" y la reseña de Google de Jessica S. (la misma que está en About: "Christina and her crew are magic travel masters…"), y al final un solo link discreto "For yoga teachers · Leading a retreat with us →" a teach-with-us.html. En celular va después del formulario.
- Chip "Something else" → "Press & partnerships" (valor `press`, ancla `#press`).
- "A trip for my group": "How many of you?" pasa a chips (un toque), y "Roughly when?" queda al lado de "Anywhere in mind?" (`destination`, opcional).
- Checkbox "Also send me news of future retreats" (`newsletter`), destildado, antes del botón. Se envía `newsletter: "yes"` solo si está tildado.
- Éxito: "Thank you, {name}. Jenniferlyn will write back to {email} within 24 hours." (como texto, nunca HTML; probado con `<b>Ana</b>`).
- El envío sigue igual: POST JSON a `YAWW_CONTACT.endpoints.contact` (data/contact.js); hoy es `null`, así que se simula y se loguea el payload. No hay Formspree/Netlify.
- Alternativa en el board: `proposals/contact-alt/index.html`, una sola columna tipo carta; la pregunta "What is it about?" en Baskerville grande incluye como cuarta línea "Leading a retreat with us", así no hace falta ningún bloque de links aparte.
- Verificado a 1440 y 390, las tres ramas, error de grupo sin tamaño, éxito simulado. Sin errores de JS.
- Preguntas: ¿link de "4.9 on Google · 45 reviews" a la ficha de Google (no tenemos la URL)? ¿Las cifras siguen vigentes? Si la persona elige WhatsApp o llamada, el mensaje igual dice "write back to {email}": ¿lo dejamos así o lo adaptamos?

## 9 · Pop-up y atribución

- Todas las retreat pages (26) abren el pop-up desde todos sus botones (hero, rooms, barra fija, waitlist): verificado una por una en el navegador. Lo que linkeaba a `contact.html#general-inquiry` sin abrir nada era "Ask about these dates" (bloque de hosts cuando el teacher no está anunciado: Oaxaca y Santorini sep D26) y los botones de rooms de la plantilla `retreatpage-elcuyo.html`. Arreglado en `build_page.py` y en la plantilla; páginas regeneradas (ediciones + waitlist), `_validate.py` sin errores. Book now / WeTravel y el estilo de los botones del hero no se tocaron.
- Mismo mensaje de éxito que contact. `retreat_id` ya salía de `data-retreat-id` (= id de data/retreats.js); en las páginas waitlist es el id de la location (`france`, `morocco`…), porque no hay edición.
- Atribución (`js/attribution.js`, en todas las páginas): en la primera página de la sesión guarda `utm_source/medium/campaign/term/content`, `referrer` (solo externo, sin query) y `landing_page`; un link nuevo con utm en la misma sesión lo reemplaza. Además guarda el primer contacto en localStorage (`first_*` + `first_seen`), porque la reserva suele llegar semanas después. Todo con try/catch; nada va en URLs. Los dos formularios reciben los campos ocultos al enviar. Documentado en `docs/lead-form.md` ("Attribution"). El campo viejo `utm` del pop-up se reemplazó por estos.
- No subí `?v=` (lo hace el orquestador): tocados `css/contact.css`, `js/contact.js`, `js/lead-modal.js`, `js/attribution.js`.
- Preguntas: ¿confirmás estos campos de atribución (y guardar el primer contacto)? ¿El formulario de waitlist (js/retreat-cards.js, no es mío) también debería mandarlos? Basta con `YAWW_ATTRIBUTION.get()`.
