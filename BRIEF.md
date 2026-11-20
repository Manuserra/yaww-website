# YAWW website — brief para el trabajo nocturno

Este archivo es la fuente de verdad. Cualquier agente que trabaje en el sitio lo lee entero antes de tocar nada y vuelve a él cuando tenga dudas.

- **Sitio oficial:** `index.html` y las páginas que enlaza. `premium/` e `index-premium.html` son solo referencia: se pueden mirar y se les pueden tomar ideas, pero no se editan.
- **Alcance:** todas las páginas del sitio oficial.
- **Personas:** C = Christina, la jefa. Su gusto manda en el copy y en la confidencialidad.

---

## 0. Reglas de trabajo (no negociables)

### Control de versiones
- Todo el trabajo va en la rama `overnight/2026-11-20`, creada desde `main`. **Nunca se hace commit ni push a `main`**: GitHub Pages publica `main` y C mira esa URL.
- Primer commit de la rama: un snapshot de los archivos sin trackear que ya existían (`index-polish.html`, `proto-*.html`, `images/icon_*-glyph.svg`, `images/movement-hillside-crop.jpg`), para no perderlos.
- **Un commit por tarea**, con un mensaje claro que empiece por la sección: `retreat-page: …`, `locations: …`, `global: …`. Así cada cambio se puede revertir por separado con `git revert`.
- Al cerrar cada sección, un **tag**: `night-01-global`, `night-02-retreat-page`, etc. Así se puede comparar o volver a un punto entero.
- `git push origin overnight/2026-11-20` después de cada sección, como backup. Solo esa rama.
- Prohibido: force push, rebase de historia ya pusheada, borrar archivos sin trackear del usuario.

### Propuestas y decisiones pendientes
- Cuando hay una decisión de diseño o de producto con varias respuestas razonables, **se implementa la opción recomendada en el sitio** y las alternativas se guardan como prototipos en `proposals/<tema>/option-a.html`, `option-b.html`…
- `proposals/index.html` es el tablero de la mañana. Por cada decisión muestra la pregunta, las opciones con link y captura, cuál quedó implementada y por qué. Debe ser rápido de recorrer: el usuario va a responder "esta, esta, esta".
- Los prototipos son archivos aislados. Pueden llevar CSS inline (ver la memoria "draft pages are frozen").
- `OVERNIGHT-REPORT.md` se va actualizando durante la noche: qué se hizo (con el commit), qué quedó a medias, qué falta y qué preguntas hay para el usuario.

### Presupuesto (créditos)
- Al empezar, el uso semanal (todos los modelos) estaba en el 35 %, con reset el 24/11. **Tope de la noche: 65 % semanal.** Al llegar ahí se para, se commitea, se pushea y se escribe el reporte.
- Revisar el uso (`get_usage`) al terminar cada sección. Si el límite de 5 horas está en más del 85 %, esperar al reset en vez de forzar.
- Ahorro: los agentes leen solo los archivos que necesitan, no se relanzan auditorías completas por cada cambio pequeño y las tareas mecánicas van a modelos más baratos.
- Si no alcanza para todo, se sigue el **orden de prioridad de la sección 12**.

### Cosas que no se hacen sin el usuario
- No se contacta a nadie: ni teachers, ni WeTravel, ni C.
- No se publica nada, no se hace deploy y no se toca WordPress en vivo.
- No se inventan datos de negocio (precios, fechas, nombres de hoteles). Si falta algo, se pone un placeholder visible: `[missing photo: …]` o `[missing data: …]`.

### Calidad
- Seguir el design system existente: tokens en `css/`, grids, aspect ratios de imagen y bordes **cuadrados** (sin rounded corners).
- Copy en inglés, alineado al wording del sitio actual (https://yogaadventuresworldwide.com, sobre todo las páginas de cada retiro), que es el que le gusta a C. **Nada de AI slop**: sin em dashes de relleno, sin "it's not X, it's Y", sin tríadas forzadas, sin hablar de nosotros como si estuviéramos improvisando. Si el sitio actual ya tiene un copy para algo, se reutiliza. Si hace falta copy nuevo, se escribe en el mismo registro.
- **Confidencialidad: los nombres de hoteles nunca aparecen**, salvo Utopia, que es nuestro. Se usa "hotel boutique en Chiang Mai" o similar.
- Verificar cada cambio en el navegador a 1440 y a 390 de ancho antes de commitear.
- Skills útiles: `emil-design-eng`, `animate`, `review-animations`, `design:design-critique`, `design:ux-copy`, `copywriting`, `ui-ux-pro-max`, `designloop`, `break-ui`, `simplify`, `design:accessibility-review`, `marketing:seo-audit`, `frontend-design`.

### Fuentes
- Reviews reales: `/Users/manuelserra/Downloads/YAWW — Tripadvisor and Google Reviews.md`
- Fotos de Renata: `/Volumes/SwanDucky/02_Clients/01_YAWW/0_Material/0. By Location/0. All Bins Merged/Teachers/Teacher Renata/`
- Material general: `/Volumes/SwanDucky/02_Clients/01_YAWW/0_Material/`
- Google Drive, unidad compartida "banco de imágenes": carpetas `2025-02_ElCuyo_Retreat_AliciaDelaney_UGC`, `2025-02_ElCuyo_Retreat_AlisonBuchanan_UGC`, etc.
- Canva: kits y carruseles de cada retiro, para fotos y bios de teachers.
- Sitio en vivo: https://yogaadventuresworldwide.com (locations en `/yoga/`, contacto en `/contact-about-yoga-retreats/`)
- Referencias de UX de búsqueda: https://livekindred.com/es/community-explore y https://www.evaneos.es/

---

## 1. Global

1. **Hover de cards:** un mismo hover para todas las cards del sitio (retreats, teachers, team, journal, locations), de buen gusto y sutil. Se define una vez y se reutiliza. Hay un punto de partida en `proto-card-hover.html`.
2. **Hover de botones:** la animación de relleno de la versión premium. Se mantiene lo nuestro: todos los botones con el mismo ancho.
3. **Color stone de los botones secundarios:** a C no le gusta, dice que parece vómito de bebé. Hay que iterarlo hacia un marrón que tire a dorado y se lea premium, sin alejarse mucho de la marca. Se hace una propuesta de 3 a 5 opciones (`proposals/stone-color/`), con muestras de botón, eyebrow y fondo, y su contraste AA. Se implementa la recomendada en el token.
4. **Limpieza de código:** eliminar CSS y JS muertos o duplicados, y unificar en `css/` y `js/`. Sin romper nada; verificar visualmente antes y después (skill `simplify`).
5. **SEO:** títulos y meta descriptions únicos, un H1 por página, jerarquía de headings, alt text, Open Graph, `schema.org` (Organization, Event/TouristTrip para los retiros, Person para las teachers), canonical, sitemap.xml y robots.txt.
6. **Consistencia tipográfica y de estilos** en todo el sitio. Proponer mejoras de buen gusto sin cambios grandes.

## 2. Homepage
1. **Espaciado vertical:** más aire en todas las secciones, para que se sienta más luxury. Excepción: el penúltimo módulo de CTA y el footer, que están bien.
2. **Guion del video del hero** en `docs/hero-video-script.md`: duración total, lista de clips en orden, duración y contenido de cada clip y notas de ritmo y transición. Pensado para conseguir algo parecido con el material que existe; revisar `0. Video Selects Bin` en SwanDucky.

## 3. Retreats (listado), de las secciones más importantes
1. Las tabs de tier, en hover, usan el color de hover que ya usamos.
2. Dropdowns: en estado hover, el botón × para borrar se pierde y no se lee. Hay que darle contraste y un hover propio.
3. **Producto y UX (propuesta, no implementar a ciegas):** ¿los tiers deberían ser un dropdown más? ¿Los dropdowns deberían ser multiselect con búsqueda? ¿Hace falta un search global? ¿Conviene un buscador que abra un listado de destinos con imágenes, como Kindred o Evaneos? Hacerlo con criterio de negocio: catálogo chico (unos 15 destinos), compra de alto valor y emocional. Usar el brainstorming de producto. Entregar `docs/retreats-search-product.md` con la recomendación y prototipos navegables en `proposals/retreats-search/`. Las mejoras chicas claras sí se implementan.

## 4. Retreat page (template `retreatpage-*.html`)
1. El label de tier (Deep Dive, Signature…) va **arriba del título**, con el color coding de las cards.
2. El tag de "X spaces left" va con **bordes cuadrados**.
3. **Hero:** ahora está mal resuelto (a 1440 es solo una imagen; en pantallas grandes queda la imagen y el texto abajo). Tiene que ir todo above the fold: imagen de fondo, nombre del retiro, info clave y CTAs. Referencia: el hero de la versión premium, con la tipografía bajada a nuestro registro. Hay un punto de partida en `proto-hero-above.html`. Hacer opciones en `proposals/hero/`. Los heros de las location pages tienen que ser consistentes con este.
4. **"I want more info"** abre un **modal con un formulario** para captar leads: gente interesada que todavía no está lista para comprar. Es uno de los objetivos más importantes del sitio. Campos mínimos y bien pensados: el retiro va precargado, nombre, email y luego opcionales con buena UX (por ejemplo, ¿viajas solo o acompañado?, ¿qué te gustaría saber?, ¿cómo preferís que te contactemos?). UI exquisita, estados de error debajo de cada input y un estado de éxito cálido. Más adelante se conecta con WeTravel; dejar el `action` y los nombres de campo fáciles de mapear. Documentar el porqué de cada campo en `docs/lead-form.md`.
5. **Your hosts:** dar más aire entre "your hosts" e "includes". El texto alineado al borde superior de la imagen y "Meet Alicia" alineado al borde inferior. Quitar los sublabels (E-RYT 200…, "YAWW team · El Cuyo & Sardinia") a todos: esa info va en la descripción. Si no hay facilitator asignada, **no aparece**: ni empty state ni tabs. Las tabs solo aparecen si hay 2 o más personas.
6. **Itinerario:** la línea de progreso con el scroll y el estilo tipográfico de "Day 1, Day 2" de la versión premium, con nuestro color.
7. **Accommodations:** las tabs solo aparecen con 2 o más hoteles. Los nombres de hoteles nunca aparecen (salvo Utopia): usar "hotel boutique en [ciudad]" o equivalente.
8. **Testimonials:** 3 reales por retiro, sacados del `.md` de reviews. Si hay de esa location, mejor; si no, globales. Se pueden editar para acortarlos, sin "…".
9. **Rooms pricing:** proponer un layout de 4 columnas de info y 8 de cards horizontales apiladas. Cuando hay un solo hotel, no repetir el bloque del hotel en cada card. Cada habitación lleva una foto chica y pills de características. Donde falten fotos o datos (por ejemplo, Thailand), placeholders visibles. Revisar si el sitio en vivo da información de habitaciones que nosotros no damos. El modal de habitación lleva tabs para cambiar de habitación o de hotel y muestra también las pills. **"Reserve your spot" y WeTravel:** recomendar dónde va el CTA (en la sección, en cada card con la habitación preseleccionada, o en ambos) y marcar qué depende de lo que permita el embed de WeTravel. Se implementa la recomendada y las alternativas van a proposals.
10. **Offset Your Carbon Footprint:** no hay link. Proponer: eliminarlo, moverlo antes del footer como nota discreta o convertirlo en un dato de "Additional info". Se implementa la recomendada.
11. **Additional info** escrita para cada retiro: información que de verdad ayude a decidirse (nivel físico, clima, cómo llegar, qué llevar, viajar solo, depósito y cancelación si está en el sitio actual). Sin inventar políticas.
12. **Sticky bar:** más elegante, tomando cosas de la premium. Sumar un CTA secundario de "Ask for info" que abre el mismo modal de leads.

## 5. Locations
1. **Fotos del grid** de Argentina, El Cuyo, France, Oaxaca, Portugal, Santorini y Basque Country: usar las de "yoga retreat locations" de https://yogaadventuresworldwide.com/yoga/. Para El Cuyo, la del salón vacío. Descargarlas, optimizarlas (webp/jpg, peso razonable, srcset) y reencuadrarlas con criterio al aspect ratio del grid. Las demás quedan como están.
2. **Blurbs:** todos con la misma cantidad de líneas (ahora son 3, está bien). Sin AI slop, alineados al wording de las retreat pages del sitio actual y cumpliendo su rol en este grid.
3. **Template de location page** para **todas** las locations (ahora solo existe Iceland, y no gusta):
   - La retreat page vende el retiro. La location page vende el destino y empuja la venta del retiro. **No son lo mismo** y la location page no puede ser un resumen perezoso de la retreat page.
   - Nos posiciona como **autoridad y expertos** en ese lugar: una especie de city guide desde nuestra mirada. Se puede apoyar en los pilares de la homepage (comida, movimiento, etc.).
   - **Sin nombres de hoteles** ni nada que la competencia pueda copiar.
   - Usa nuestros aspect ratios, grids y design system. Si faltan imágenes, placeholders de color con un label del tipo "aquí iría una foto de [X]".
   - **Copy:** a C no le gustó "Where the road runs out and the country starts." Si el sitio actual ya tiene un copy (subtítulo, descripción), se usa. Solo se escribe copy nuevo para lo que no existe, y en ese mismo registro.
   - Sin Signature, Deep Dive ni tier: eso es del retiro, no de la location.
   - Quitar la línea "Next retreat · Oct 11–19, 2026 · 3 upcoming dates · Hosting here since 2019".
   - **Hero:** toda la info above the fold, consistente con el nuevo hero de las retreat pages.
   - **Con retiros:** un módulo bastante arriba, como en las teacher pages, con el fondo stone que usa el módulo "For teachers" de la homepage.
   - **Sin retiros:** rediseñar el estado con buen gusto. El label "Upcoming retreats in Iceland" no puede quedar pegado a "No dates scheduled yet". Los errores del formulario van debajo de cada input, sin romper el layout. **Join the waitlist** es el CTA principal; se elimina "See upcoming dates" y "Ask us about Iceland" va junto al CTA principal. Reescribir "We are still putting this one together." con un mensaje que no nos haga quedar como improvisados.
   - Hacer varias propuestas del template en `proposals/location-template/` e implementar la recomendada en todas las locations.

## 6. About us
1. Todos los eyebrows (Our story, Founder…) en el mismo marrón que los de la homepage, y bien escritos: algunos ahora no tienen sentido.
2. "Someone from our team, with you": si entra en una línea, mejor. El botón dice "See all facilitators" o similar.
3. **It takes a village:** el eyebrow es "Team". Se quita a Christina (ya aparece arriba). Orden: Sofi, JL, Manu, Maggie. Grid de 2×2 con fotos rectangulares.
4. Testimonials reales, con caras si las hay en el material.
5. Badge de ★ Google y ★ Tripadvisor: evaluar los widgets oficiales (el de Tripadvisor existe; el de Google suele requerir terceros). Recomendar y dejarlo preparado. Si hace falta una cuenta o una API key, se anota como pregunta.
6. El módulo For teachers es el mismo de la homepage.
7. Sumar un carrusel con link a "See all teachers" y otro con link a "See all team".

## 7. Teachers (listado)
1. Agregar también a las teachers sin retiros. Blurbs con largos consistentes.
2. Para las teachers sin fotos: buscar sus retiros en Canva, descargar las fotos y leer la info para escribir sus textos.
3. En cada card, abajo, un link "More about [Name]" (pedido de C).
4. En el título de arriba, evitar la viuda "way.".
5. Cambiar las fotos de Alicia Delaney y Alison Buchanan por fotos de su set profesional de El Cuyo, en la unidad compartida de Drive.

## 8. Teacher pages
1. "Upcoming retreats with [Teacher]" con fondo stone, el mismo de los otros módulos.
2. Proponer mejoras de layout: ahora se siente pobre. Por ejemplo, foto vertical a la izquierda y texto a la derecha, más jerarquía de información, y slots para contenido que podemos pedirles a las teachers (playlist de Spotify embebida, video dando una clase, frase, especialidades). Dejar los slots preparados con placeholders y una lista en `docs/teacher-content-request.md` de qué pedirles. El objetivo es ayudar a cada teacher a venderse.

## 9. Team → Facilitators
1. Renombrar "Team" a "Facilitators" en la navegación, los títulos y las URLs (con redirects o links actualizados).
2. Quitar a Marina, Fleur y JL.
3. Mejores fotos de Renata (carpeta de SwanDucky) y de C (buscar en Drive o en el material).
4. Link "More about [Name]" en cada card.
5. La página de cada facilitator alinea su layout con la de teacher, pero más simple.

## 10. Journal
1. Mejores hover states en las tabs de filtro.
2. Los labels de temática de las cards van separados con un punto medio: `INSPO · LIFESTYLE · TRAVEL`.

## 11. Contact
Repensar desde UX, producto y negocio. La página actual viene del sitio viejo (https://yogaadventuresworldwide.com/contact-about-yoga-retreats/) y nadie respondería esos formularios.
- Ahora la captura de leads de cada retiro está en las retreat pages.
- "General inquiry" y "Submit" se reemplazan por un lenguaje humano.
- **Teach with us** pasa a ser su propia página (`teach-with-us.html`): por qué YAWW, cómo funciona, qué hacemos nosotros, testimonials de teachers y el formulario. Los módulos "For teachers" de la homepage y de About apuntan ahí.
- "Build a custom retreat": entender qué es (grupos privados o empresas, según el sitio actual) y decidir si merece una página, una sección o desaparecer.
- Entregar la propuesta en `docs/contact-strategy.md` con prototipos e implementar la recomendada.

## 12. Orden de prioridad (si no alcanza para todo)
1. Global: botones, hover de cards, opciones de color stone.
2. Retreat page completa (incluye el modal de leads).
3. Locations: fotos, blurbs, template y páginas.
4. Retreats (listado): fixes y propuesta de search.
5. About, Teachers, Teacher pages, Facilitators.
6. Journal, Contact y Teach with us.
7. Homepage: espaciado y guion de video.
8. Revisión responsive de cada página (1440, 1024, 768, 390). Se corrige todo lo que se rompa.
9. Auditoría UX/UI y design critique. Se corrige lo que valga la pena; los cambios grandes van como propuesta.
10. Limpieza de código y SEO.
11. **Si queda presupuesto:** empezar el theme de WordPress en `wordpress-theme/` (sin ACF Pro por ahora: templates, partials y `functions.php`). El sitio HTML no se pierde.
12. **Al final:** una opción tipográfica más jugada en `proposals/typography-expressive/`. Si la premium es un 10 en expresividad y la nuestra un 1, buscamos un 5. Sacarle más partido a Libre Baskerville manteniendo nuestros layouts, editorial y elegante, sin brutalismo.
