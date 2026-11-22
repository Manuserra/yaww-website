
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
