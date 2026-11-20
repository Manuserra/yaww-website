# Cómo se armaron estos datos

Los scripts quedan acá para que la extracción sea reproducible y para que, cuando el
sitio viejo cambie una fecha o un precio, no haya que hacerlo todo a mano otra vez.

| archivo | qué hace |
|---|---|
| `editions.py` | las 19 ediciones abiertas, transcritas de `/all-upcoming-dates/` el 2026-11-12, y el mapeo location → destino/país/región |
| `curation.py` | alojamientos, tipos de habitación con precio y extras opcionales, **transcritos a mano** de cada página: el HTML viejo intercala nombre, precio y descripción en un orden que cambia de página a página y una lectura automática mezclaba precios entre habitaciones |
| `gen.py` | arma los `<slug>/data.json` combinando lo de arriba con el texto scrapeado (includes, itinerario, travel tips, políticas) y con la asignación de imágenes |
| `build_page.py` | construye una página por cada retiro de `data/retreats.js` (`retreatpage-<id>.html`) sobre `retreatpage-elcuyo.html`, que sólo **lee**: reemplaza las regiones variables y deja CSS y scripts intactos. Exporta las fotos que falten a `images/retreat-photos/` en WebP y le pone `pageUrl` a cada retiro. `python3 retreats/_tools/build_page.py [id ...]` |
| `page_content.py` | la capa editorial: Travel Tips por destino en el formato de la plantilla, nombres de alojamiento que el sitio viejo no da, facilitadores |
| `chrome.py` | lo que comparten todas las páginas oficiales: el skip link al principio del `<body>`, `<main id="main">` y **un solo** `?v=` para todo `css/` y `js/` (`ASSET_VERSION`). Los builders lo aplican antes de `seo.apply()`; `python3 retreats/_tools/chrome.py` actualiza las páginas escritas a mano. Subir `ASSET_VERSION` con cada cambio en `css/` o `js/` |

`gen.py` espera en su directorio de trabajo los intermedios del scrapeo
(`structured.json`, `img_sections.json`, `img_assign.json`, `teachers.json`), que no se
versionan: son el estado del sitio viejo en la fecha de extracción, no datos del proyecto.
Lo que sí se versiona es el resultado: `_index.json` y los `data.json`.

Validar siempre después de regenerar:

    python3 retreats/_validate.py
