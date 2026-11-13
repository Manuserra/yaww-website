# Retiros abiertos de yogaadventuresworldwide.com — extracción

**Fecha de extracción:** 2026-11-12 · **Rama:** `retreats-data` · **Fuente:** https://yogaadventuresworldwide.com/

19 retiros abiertos, 524 fotos, 1.212 referencias a imagen — todas resueltas, todas con alt.
`python3 retreats/_validate.py` pasa con 0 errores y 0 warnings.

```
retreats/
  _schema.json      el schema (53 campos, uno por cada contenido variable de la plantilla)
  _index.json       los 19 retiros con fechas, status, profesor y de qué página vienen
  _validate.py      el validador (sin dependencias, corre con python3 pelado)
  _tools/           el script que llena la plantilla con un data.json
  <slug>/data.json  un retiro
```

**Las imágenes no están en git.** Las 524 fotos (692 MB) viven fuera del repo, en
`../yaww-retreat-images/`, con exactamente la misma estructura de carpetas:

```
yaww-retreat-images/
  retreats/_shared/{el-cuyo,santorini,montenegro,camino-de-santiago,teachers}/img/
  retreats/<slug>/img/
```

Por eso los valores de `file` en los `data.json` (`retreats/_shared/el-cuyo/img/x.jpg`)
siguen siendo válidos tal cual: resuelven con `yaww-retreat-images/` como raíz. `.gitignore`
ignora `retreats/**/img/` y `retreats/_shared/`, así que si copiás las carpetas de vuelta
dentro del repo para previsualizar las páginas, git no las va a volver a trackear.

`_validate.py` las busca relativas a la raíz del repo: para que pase, las imágenes tienen
que estar copiadas dentro del repo (ignoradas) o hay que apuntarlo a `yaww-retreat-images/`.

---

## 1. Los 19 retiros

| slug | fechas | status | tier | profesor/a | imgs | campos null |
|---|---|---|---|---|---|---|
| `morocco-nov-2026` | 7–14 nov 2026 | **sold_out** | — | Dawn Thie | 57 | 16 |
| `el-cuyo-dec-2026-delaney` | 1–6 dic 2026 | open | — | Alicia Delaney | 62 | 15 |
| `thailand-dec-2026` | 1–10 dic 2026 | **sold_out** | — | Amy Sennett-Starner | 56 | 17 |
| `el-cuyo-dec-2026-d29` | 29 dic 2026 – 3 ene 2027 | open · *New Retreat* | — | (sin asignar) | 61 | 12 |
| `el-cuyo-jan-2027-berger` | 24–29 ene 2027 | open | — | Sue Berger | 62 | 15 |
| `el-cuyo-jan-2027-coffey` | 29 ene – 2 feb 2027 | open | — | Jamie Coffey | 43 | 19 |
| `el-cuyo-feb-2027-delaney` | 2–7 feb 2027 | open | — | Alicia Delaney | 62 | 15 |
| `el-cuyo-feb-2027-morin-gray` | 14–20 feb 2027 | open | — | Genevieve Morin + Rachel Gray | 53 | 17 |
| `argentina-mar-2027` | 12–19 mar 2027 | **few_spaces** · *2 spaces left* | — | Alison Buchanan | 54 | 16 |
| `camino-de-santiago-apr-2027` | 18–27 abr 2027 | **few_spaces** · *2 spaces left* | **signature** | Jenniferlyn Chiemingo | 48 | 15 |
| `montenegro-may-2027-erickson` | 15–21 may 2027 | open | — | Bonnie Erickson | 36 | 19 |
| `portugal-may-2027` | 16–23 may 2027 | open | — | Dawn Thie | 48 | 20 |
| `montenegro-may-2027-cordover` | 22–28 may 2027 | **few_spaces** · *1 space left* | — | Melanie Cordover | 36 | 17 |
| `santorini-may-2027` | 23–29 may 2027 | open | — | Yessica Puebla | 51 | 20 |
| `santorini-sep-2027-erickson` | 12–18 sep 2027 | open | — | Bonnie Erickson | 52 | 18 |
| `camino-de-santiago-sep-2027` | 19–28 sep 2027 | open | **signature** | Genevieve Morin | 48 | 17 |
| `santorini-sep-2027-berger` | 19–25 sep 2027 | **sold_out** | — | Sue Berger | 52 | 16 |
| `santorini-sep-2027-d26` | 26 sep – 2 oct 2027 | open | — | (sin asignar) | 51 | 17 |
| `oaxaca-oct-2027` | 28 oct – 4 nov 2027 | open · *New Retreat* | — | (sin asignar) | 66 | 16 |

Las 19 fechas salen de `/all-upcoming-dates/` y coinciden una a una con los `date-repeater`
de cada página de location. No hay fechas futuras en una página que no estén en el calendario
ni al revés — las dos listas concuerdan.

---

## 2. Qué falta, y en cuántos retiros

**En los 19:** `about_title`, `gallery_title`, `gallery_intro`, `highlights`, `testimonials`,
`carbon_offset`.
El sitio viejo no tiene bloque de highlights, no tiene copy de carbon offset, y su carrusel
"Praise" está en el HTML pero vacío: el markup existe, las citas no. Los tres primeros son
encabezados que la plantilla ya trae por defecto.

**En 18:** `subtitle` (sólo Embodied tiene uno: *A Yoga & Boudoir Photography Experience*).

**En 17:** `tier` / `tier_label` / `tier_source` — ver §3.

**En 13:** `accommodations[].name`. El sitio describe la propiedad con todo detalle pero
nunca la nombra, salvo El Cuyo (*Utopia Guesthouse & Yoga Studio*). Para que la plantilla
tenga algo que poner en la pestaña, cada alojamiento lleva además un `label` con el
encabezado que sí publica el sitio (*"3 nights in Lisbon"*, *"4 nights beachfront on Koh Yao Noi"*).

**En 9:** `extras`. Sólo los tres retiros de El Cuyo y Santorini publican extras con precio.

**En 5:** `travel_tips` — Santorini y Portugal no tienen esa sección en ningún lado;
Embodied y Mini El Cuyo tampoco, pero sí la hereda (ver §4).

**Profesores sin bio ni foto en ningún lado del sitio viejo:** Jamie Coffey
(`el-cuyo-jan-2027-coffey`) y Yessica Puebla (`santorini-may-2027`). No están en `/team/`
ni en la página del retiro. Los dejé con `bio: null` y `photo: null`.
`role` e `instagram` están vacíos para todos: `/team/` no publica ni cargo ni redes.

**Precios que no existen:** `oaxaca-oct-2027`. El sitio muestra literalmente `XXusd` y
*"2027 PRICING COMING SOON"*. Los cinco tipos de habitación están con nombre y descripción
reales y `price_usd: null`. Es el único retiro sin precios.

---

## 3. Decisiones que tomé

**Un retiro = destino + fechas + profesor.** El sitio viejo agrupa por location: una página
por destino con varias fechas adentro. Las 19 filas son ediciones concretas. `source_url`
dice de qué página salió cada una y `source_is_shared: true` marca las que comparten página.

**Slugs.** `destino-mes-año`. Cuando dos chocan agrego el apellido del profesor, como pediste
(`el-cuyo-feb-2027-delaney` / `el-cuyo-feb-2027-morin-gray`). Cuando el que choca no tiene
profesor asignado uso el día de inicio en vez del apellido: `el-cuyo-dec-2026-d29`,
`santorini-sep-2027-d26`. No había otra forma de desambiguar sin inventar un nombre.

**Destino para los retiros itinerantes.** Marruecos, Tailandia, Argentina, Portugal y
Montenegro se mueven entre dos o más sitios, y la marca los nombra por país. Usé el país como
`destination` (y por lo tanto en el slug y en el nombre de archivo), con el detalle en `region`
y en `location_label` (`"Bay of Kotor, Montenegro"`, `"Chiang Mai & Koh Yao Noi, Thailand"`).

**Tier.** Sólo es deducible para Camino de Santiago: su post lleva el término
`retreat_categories-signature-retreats`. El resto de las categorías del sitio viejo son
temáticas (`yoga`, `art-creative`, `yoga-food-and-wine`, `family-retreats`), no colecciones,
y **no existe ninguna taxonomía de Deep Dives**. Los otros 17 quedan en `null` con la
explicación en `_notes.tier`. **No usé los tiers del array `RETREATS` de `retreats.html`:
son datos de diseño inventados** — ahí Santorini sep-2027 aparece como "deep" y "signature"
en filas consecutivas, y los nombres de retiro son placeholders ("Yoga Retreat Name").

**Morocco nov-2026 empezó antes de hoy.** Arrancó el 7 de noviembre y termina el 14: está en
curso, no reservable. Lo dejé igual porque el sitio lo sigue listando bajo *Upcoming Dates*,
marcado con `started_before_today: true` en el índice y una nota en `_notes.date_start`.
Si el criterio es "reservable", sacalo.

**Nights y days** salen de la aritmética entre `date_start` y `date_end`. Coinciden con las
noches que declara el bloque *Includes* de cada página en los 11 casos donde el sitio las dice.

**Precios de habitación y alojamientos: transcritos a mano.** El HTML viejo intercala el
nombre de la habitación, el precio y la descripción en un orden que cambia de página a página
— una lectura automática le ponía a "Shared Double rooms" el precio de la habitación de al
lado. Están en `retreats/_tools/curation.py` y los revisé uno por uno contra la página.
Las tarifas "shared bed" las abrí como habitación propia (p. ej. *Deluxe Suite* y *Deluxe
Suite — shared bed*), porque son dos precios distintos para la misma habitación y la
plantilla muestra un precio por tarjeta.

**Políticas.** El bloque de cancelación / responsabilidad del cliente / COVID es idéntico en
los 11 retiros: es texto del sitio, no del retiro. Guardé la política de cancelación en
`booking.cancellation_policy` y el resto quedó fuera; lo que sí varía por retiro es el
depósito (750 USD en El Cuyo, 1.000 USD en el resto) y está en `booking.deposit_usd`.

**FAQs.** Camino de Santiago es el único con bloque de FAQs propio: tiene nueve preguntas
reales y están tal cual. Para los otros 18 armé las cinco preguntas que trae la plantilla
con respuestas **citadas** del bloque Policies de la sección Reserve de su propia página
(métodos de pago, cancelación, cambios de plan, demoras de vuelo, link a términos). No
inventé ninguna respuesta, pero no son específicas del retiro y está dicho en `_notes.faqs`.

**Alt text: miré las 524 fotos.** Las armé en hojas de contacto de 20 y las describí a partir
de lo que se ve, no del nombre de archivo. Por eso los alts dicen cosas como *"Water pouring
from a coconut-shell shower head over green patterned tiles"* y no *"outdoor shower 2"*.

**Resolución.** A cada URL le saqué el sufijo `-WxH` de WordPress; cuando el archivo era
`-scaled` y existía el original sin ese sufijo, bajé el original (bastante más grande:
3 MB contra 730 KB en un caso típico). A los archivos editados en WordPress (`-eTIMESTAMP`)
los dejé como están: sacarles el `-scaled` da 404, y la versión base sin editar es **otro
recorte**, no la misma foto más grande. Ninguna imagen se comprimió, redimensionó ni convirtió.

**El QR de pago** (`Screenshot-2026-06-09-at-8.11.07-p.m.png`, el del Zelle en la sección
Reserve de El Cuyo y Embodied) quedó fuera: no es contenido del retiro y la plantilla no
tiene dónde ponerlo. Está en `booking.deposit_instructions` como texto.

---

## 4. Datos que vienen de la página de location, no del retiro

Está marcado retiro por retiro en `_notes`. Resumen:

- **El Cuyo (4 ediciones de `beach-yoga-retreat-mexico`), Santorini (4), Montenegro (2),
  Camino (2):** `intro`, `about_body`, `includes`, `not_included`, `itinerary`,
  `accommodations`, `rooms`, `extras`, `gallery`, `hero_image` y `travel_tips` son
  **de la location**, idénticos entre las fechas que comparten página. Lo único propio de
  cada edición es fecha, profesor, status y SEO.
- **Embodied y Mini El Cuyo:** sus páginas no tienen sección Travel Tips. Les copié la de
  `beach-yoga-retreat-mexico` — mismo destino, misma casa — con la nota puesta en
  `_notes.travel_tips`. Si no querés ese préstamo, se borra el campo y listo.
- **Fotos de alojamiento en los retiros de dos sedes** (Portugal, Marruecos, Tailandia):
  el sitio viejo las pasa todas por una sola galería sin marcar cuál es cuál. Las miré y las
  repartí a la sede que muestran. Argentina usa los encabezados del sitio, que sí separan.

---

## 5. Problemas encontrados

**1 imagen no se pudo bajar.** `https://yogaadventuresworldwide.com/wp-content/uploads/2025/10/10087287916.jpg`
(itinerario de Embodied) da 404 en el servidor; probé el original, el `-scaled` y la URL tal
cual de la página, tres intentos cada uno. No está en los datos. La variante
`10087287916.jpg_exif1-3.jpg` sí existe y es otra foto, que sí está.

**74 imágenes sólo existen en tamaño chico** porque el original ya no está en el servidor:
Marruecos 23, Portugal 21, Santorini 13, Montenegro 10, Tailandia 3, Argentina 2, El
Cuyo/Embodied 1, Camino 1. Las peores son 5 de Montenegro que sólo quedan en 300×200
(`SPA`, `SUITE-3-1`, `VILLA-EXT-1`, `pool3`, `pool4`). Cada una lleva un campo
`low_resolution` en su objeto de imagen con las dimensiones reales. **Hay que pedirle los
originales al cliente antes de publicar**: a 300×200 no sirven ni para una tarjeta.

**El bloque de precios de Oaxaca 2027 no existe todavía** (`XXusd`). Ver §2.

**La REST API del sitio viejo no expone el CPT `retreat`** (`/wp-json/wp/v2/types` sólo
lista post, page, attachment y los internos), así que todo salió de scrapear HTML. Tampoco
sirvió `/wp-json/wp/v2/media`: devuelve 1.040 adjuntos de 6.714, ninguno de los retiros, y
de esos 1.040 sólo 46 tienen `alt_text`. **El sitio viejo prácticamente no tiene alt text**:
las imágenes de retiro salen con `alt=""`.

**El peso.** 692 MB en 524 fotos, la más grande de 20,7 MB. Demasiado para un repo de git
normal, así que **las imágenes quedaron fuera**: los masters están en
`../yaww-retreat-images/` y git ignora las carpetas `img/`. En el repo viajan sólo los JSON.
Para publicar, lo que corresponde es subirlas a la biblioteca de medios de WordPress desde
esa carpeta; los nombres de archivo ya son los definitivos.

**La página de Marruecos tiene la sección "Praise" duplicada** en su barra de navegación
interna. No afecta los datos, pero si alguien migra el sitio viejo tal cual, ahí está.

---

## 6. Las 3 páginas de prueba

- `retreatpage-camino-de-santiago-apr-2027.html` — el único con tier, FAQs propias (8) y
  nueve grupos de travel tips.
- `retreatpage-el-cuyo-dec-2026-delaney.html` — el destino de la plantilla original, con
  5 habitaciones y 5 extras.
- `retreatpage-santorini-sep-2027-berger.html` — sold out (prueba el flag), 8 tipos de
  habitación, y sin travel tips: esa sección desaparece.

No pude darte uno de cada tier porque **sólo existe un tier deducible**. Las tres están
generadas con `retreats/_tools/build_page.py`, que lee la plantilla y sólo reemplaza las
regiones variables — el CSS y los scripts quedan intactos. Verificadas en el navegador:
renderizan la foto de hero, el badge, el flag de sold out, los 10/6/7 días del itinerario con
sus sliders, las tarjetas de habitación con precios reales, las pestañas de alojamiento y
extras y el acordeón de FAQs. Sin errores de consola y sin lorem ipsum.

---

## 7. Lo que falta en la plantilla (o le sobra)

**La plantilla no tiene lugar para datos reales que sí existen:**

1. **`price_unit`.** `retreatpage-elcuyo.html:4097` escribe `'per person'` fijo para toda
   tarjeta de habitación. En los datos reales conviven *per person*, *private room* y tarifas
   *shared bed*. Mostrar "per person" sobre un precio de habitación privada es un error de
   precio, no de diseño.
2. **Precio nulo.** La misma función hace `tier.priceUSD.toLocaleString(...)` sin chequear.
   Con Oaxaca 2027 (sin precios) **rompe**. Necesita un estado "precio a confirmar".
3. **`pricing_notes`.** Notas que cambian la decisión de compra y hoy no se muestran: *"4
   suites tienen bañera o jacuzzi, se asignan por orden de reserva"*, *"cuando se llena la
   villa principal pasamos a un hotel cercano"*, *"límite de 10/12/15 personas"*, *"los
   vuelos domésticos AEP–BRC no están incluidos"*.
4. **`itinerary_notes` y `optional_excursions_schedule`.** La plantilla tiene días con
   bullets, pero no el pie de "el itinerario puede cambiar" ni el bloque de excursiones
   opcionales con horarios que publica El Cuyo.
5. **Un solo profesor.** `.retreat-teacher` asume uno. Embodied tiene dos (Genevieve Morin y
   la fotógrafa Rachel Gray, con bio propia en la página vieja). Lo resolví metiendo al
   segundo como párrafo final; la plantilla debería aceptar N.
6. **Alojamiento sin nombre.** Las pestañas de `HOTELS` esperan un nombre. En 13 de 19 no hay,
   y uso el `label`. La plantilla debería asumir que el nombre puede faltar.
7. **`includes_footnote` / `not_included_footnote`.** Hay un sólo slot de footnote y algunos
   retiros traen dos notas distintas (asteriscos de transfer y de bebidas).
8. **`extras[].gallery` queda vacía.** El sitio viejo no asocia fotos a cada extra: la
   plantilla las pide y no hay de dónde sacarlas sin inventar.
9. **`booking.payment_methods`, `deposit_instructions` y `balance_note`** no tienen slot. El
   rail "How to book" los resume pero pierde el detalle (Zelle, wire, Bitcoin).

**La plantilla pide datos que el sitio viejo no tiene** (por eso quedan `null`):
`highlights`, `testimonials` (el carrusel Praise está vacío en las 11 páginas),
`carbon_offset`, `gallery_intro`, y las fotos por habitación en varios tipos — el sitio
pone imágenes sólo bajo algunos de los encabezados de su bloque de precios.

**Un detalle del tier:** la plantilla dibuja el badge sólo si hay `tier_label`. Con 17 de 19
en `null`, hoy el badge aparece en 2 páginas. Hay que decidir el tier de los otros 17 a mano
antes de publicar — no es un dato que se pueda derivar del sitio viejo.
