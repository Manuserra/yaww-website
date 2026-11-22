
## 4 · Cards de retreats (opción)
Copia de `retreats.html` en `proposals/retreats-cards/` con interruptor "Cards" (Ours / Premium-style; tecla C). ON: texto a la izquierda, fecha arriba del todo con el estilo de premium (Raleway 11/600, tracking .18em, mayúsculas, #877C71), nombre como ahora, location en Libre Baskerville cursiva con el color de premium, teacher en nuestro estilo con el azul primario (`--sea-blue` #475764, el del logo). Solo CSS (flex `order`), sin tocar JS. Ojo: ese azul ya es el color del nombre, así que la línea del teacher se ve igual que antes; si querés que se distinga, hay que elegir otro azul. Recomendación: adoptar. Board: fila `retreats-cards`.

## 11 · Botones del hero (opciones)
`proposals/hero-buttons/index.html` (copia de la página El Cuyo Feb 2027) con interruptor A / B / C (tecla B). A actual. B: Book now sólido y "I want more info" como link subrayado con flecha (nunca se parecen). C: Book now en bronce sólido con sombra; More info en outline con fondo oscuro tenue que solo oscurece un poco al hover (nunca se vuelve idéntico). Textos y links sin cambios. Recomendación: C (B si se quiere lo más liviano). Board: fila `hero-buttons`.

## 12 · Retreats > 1440 (opción)
`proposals/retreats-wide/retreats.html` con interruptor "Width" (Capped 1440 / Fluid; tecla W). Fluid: por encima de 1440 el container crece hasta 1920 y las 12 columnas escalan (label de season = 3 col, tres cards = 3 col cada una). Medido a 1920: card 432 px y label 432 px (capped: 312). Por debajo de 1440 no cambia nada. Recomendación: adoptar con tope en 1920. Board: fila `retreats-wide`. También se agregó la entrada `contact-alt` en `topics.js` (la página puede no existir todavía).
