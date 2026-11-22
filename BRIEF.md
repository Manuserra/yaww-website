# Overnight build · 2026-11-23

Brief de la noche. Es el archivo al que vuelvo después de cada compactación.
El brief anterior quedó en `docs/BRIEF-2026-11-20.md`.

## Reglas

- **Rama:** `overnight/2026-11-23`, creada desde `main` (hoy main = overnight/2026-11-20).
  Tag de inicio `night-00-start`; un tag por sección terminada (`night-01-…`).
  Push solo de esta rama, después de cada tag. **Nunca push a `main`**: es lo que ve Christina (GitHub Pages).
- **Presupuesto:** al arrancar, uso semanal 50 %. Paro al llegar a **65 %** (queda ~35 % de la semana).
  Si llego al tope: termino el commit en curso, escribo el reporte y paro.
- **Modelos:** el más fuerte para diseño y criterio (menú, contact, cards, encuadres dudosos);
  uno más barato para trabajo mecánico (revisar fotos en lote, renombres, links, cambios repetidos).
- **Confidencialidad:** ningún nombre de hotel salvo Utopia. Escanear el diff antes de cada commit.
- **Copy:** no inventar textos si existe en el sitio actual. Lo que falte va como `[missing data: …]`
  y como pregunta en el reporte.
- **CTAs de reserva:** no tocar la lógica de Book now / WeTravel (el usuario lo está hablando con WeTravel).
  El estilo de los botones del hero va solo como opciones en el board (sección 11).
- **Fuentes de fotos, en este orden:** repo local (`images/`), biblioteca
  `/Volumes/SwanDucky/02_Clients/01_YAWW/0_Material/0. By Location/0. All Bins Merged/`
  (Activities, Atmosphere, Collaborators, Food, Human, Places, Teachers, Water, Yoga),
  y solo si no está en ninguna: el sitio en producción yogaadventuresworldwide.com.
- **Páginas generadas:** si una página sale de plantilla + datos, se cambia el builder/datos y se regenera.
- **Agentes:** cada uno con archivos propios; nunca dos sobre el mismo archivo; `git add` con rutas explícitas.
- **Cache:** cada CSS/JS tocado lleva su `?v=` nuevo en todas las páginas que lo cargan.

## Prioridad (si se acaba el presupuesto, queda hecho lo de arriba)

1. Encuadre de fotos del itinerario (sección 1)
2. Sacar Breath Body Soul Santorini (sección 2)
3. Contact + pop-up de retiro + atribución (secciones 8, 9)
4. Fotos de personas: Christina, Rachel, Renata, dropdowns (sección 10)
5. Cards de teachers/facilitators: link solo en hover (sección 6)
6. Hover de cards con foto estilo premium (sección 3)
7. Menú: desplegable desktop + menú mobile (sección 5)
8. About · It takes a village 2 × 2 (sección 7)
9. Páginas de teachers: Spotify, YouTube, web, Instagram (sección 6)
10. Opciones para el board: card de retreats alineada a la izquierda (4), botones del hero (11), retreats > 1440 (12)

## 1 · Encuadre de las fotos de cada día del itinerario

Usuario: "muchas quedaron mal encuadradas, necesito que estén bien, que el objeto esté encuadrado correctamente para lo que fue pensado". Ejemplos: pose de lotus (la mano no debería estar cortada si la foto lo permite; la rodilla con la mano en el centro del frame), el tucán (no cortado, en el centro), la chica (su cara en el centro, no el pecho), el elefante. "Tendrás que asegurarte de cada foto de cada día de cada retreat."

Decidido:
- Listar las imágenes únicas de todos los itinerarios (hay ~578 slides en 20 páginas, muchas repetidas).
- Para cada una, mirar cómo queda en el recorte real del slider (672:408, y el estirado de días largos) y fijar el punto de interés con `object-position` por imagen.
- Si el archivo ya viene recortado y el sujeto no entra: buscar el original en la biblioteca local (o producción) y regenerar el archivo.
- Revisar en 1440 y en celular (28:17 / 81:49).
- Consumo: una sola pasada de visión por imagen única, con modelo barato; las dudosas las reviso yo.

## 2 · Sacar "Breath Body Soul Retreat in Santorini"

`data/retreats.js` id `santorini-sep-2027-berger` (19–25 sep 2027, Sue Berger, sold out). Sacarlo de la lista de retreats (y de todo lo que lea esa lista: filtros, contadores, home, dropdowns, teacher pages). La página se queda en el repo pero sin enlaces; se avisa en el reporte.
Ojo: **no** es "Breath Body Soul Retreat" de El Cuyo (enero 2027), que se queda.

## 3 · Hover de cards con foto como en premium

"Me gusta la opción de https://manuserra.github.io/yaww-website/premium/retreats.html para los hover states de las cards, aplícame esos hover states para todos los hovers de cards con foto del sitio." Tomar el hover de premium y aplicarlo a todas las cards con foto (retreats, locations, teachers, team, journal, home).

## 4 · Variante de card de la página de retreats (on/off)

"Una versión de mi página de retreats que pueda prender y apagar": textos de la card alineados a la izquierda, fechas arriba de todo con el estilo de tipo y texto de premium, la location en nuestro estilo de tipo pero con el color de premium, la teacher en nuestro estilo pero en azul primario (el del logo). Toggle on/off en la página (como el de tipografía), y fila en el board.

## 5 · Menú

"Mantengamos este layout, pero al hacer hover sobre los items que tienen más opciones debería ofrecerse un desplegable moderno, editorial, copado, a la altura del resto del sitio, con exquisita UX/UI."
Mobile: tomar el menú de premium (animación al desplegar, todo azul) pero en azul brand (el del logo), con el tamaño y la tipografía de premium para los items, en color sand secondary. Mantener nuestro contenido.

## 6 · Teachers y facilitators

- Cards: "More about {nombre}" aparece solo al hacer hover sobre la card (en touch, visible siempre).
- Páginas de teacher: poder insertar playlist de Spotify y videos de YouTube (desde los datos), y links a su sitio web e Instagram que abran en otra pestaña. Si no hay URLs reales: el campo queda vacío y no se muestra; las URLs faltantes van como pregunta.

## 7 · About · "It takes a village"

Hoy son 4 fotos verticales del equipo. Pasar a 4 horizontales en 2 × 2 (dos por fila), respetando la grilla y los aspect ratios, bien encuadradas.

## 8 · Contact

- Rama "A trip for my group": campo opcional "Anywhere in mind?" (`name="destination"`, placeholder "Mexico, Greece, open to ideas…", `data-show-when="topic=private-group"`), al lado de "Roughly when?".
- Checkbox de newsletter destildado, antes del botón: "Also send me news of future retreats" (`name="newsletter"`).
- Éxito: "Thank you, {name}. Jenniferlyn will write back to {email} within 24 hours."
- Bloque de confianza en la columna izquierda, debajo de los datos: "Since 2008" + "4.9 on Google · 45 reviews" + una reseña corta con nombre (de las reseñas que ya están en el sitio).
- "You might be looking for" es redundante: "A private retreat" ya está en los tags; "A question about one retreat" explica la UI (mala UX); "Press and partnerships" debería ser el label de "Something else"; "Leading a retreat with us" es el que más sentido tiene. "A lo mejor hay una forma mucho mejor de resolver la página de contact."
  Decidido: implementar mi recomendación y dejar la alternativa en el board.

## 9 · Pop-up "Ask us about this retreat" + atribución

- Verificar que "I want more info" abre el pop-up en **todas** las retreat pages (al menos una linkea a `contact.html#general-inquiry`).
- Mismo mensaje de éxito que en contact.
- Campo oculto `retreat_id` con el id de `data/retreats.js`.
- "Atribución (los dos formularios)": el pedido no trae detalle. Decidido por mí (a confirmar): campos ocultos `utm_source`, `utm_medium`, `utm_campaign`, `utm_term`, `utm_content`, `referrer` y `landing_page`, guardados en la primera visita de la sesión y enviados con los dos formularios. Va como pregunta para confirmar.

## 10 · Fotos de personas

- Facilitators: la foto de Christina tiene que ser la que se usa en Founder.
- Dropdowns de retreats: Camino de Santiago y Rachel Gray no tienen foto; agregarlas.
- Rachel Gray no tiene foto en la lista de teachers, pero sí en el retreat Embodied: usar esa.
- "Me gustaría usar otra foto de Renata": candidata de la carpeta Teacher Renata del disco. Implementar la mejor y dejar alternativas en el board.

## 11 · Botones del hero de las retreat pages (solo board)

"Me preocupa que los botones sean demasiado iguales cuando hay hover en I want more info, y que en su estado default no sean lo suficientemente llamativos (aunque entiendo que es por estar sobre una foto)." Proponer 2–3 opciones en el board; no tocar el sitio.

## 12 · Página de retreats > 1440 (solo board)

"Me pregunto si la retreat page debería ser responsive para más de 1440, que se sigan agrandando las cards y la columna con el label de la season, de manera proporcional siguiendo la grid." Opción en el board con recomendación.
