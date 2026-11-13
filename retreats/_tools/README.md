# Cómo se armaron estos datos

Los scripts quedan acá para que la extracción sea reproducible y para que, cuando el
sitio viejo cambie una fecha o un precio, no haya que hacerlo todo a mano otra vez.

| archivo | qué hace |
|---|---|
| `editions.py` | las 19 ediciones abiertas, transcritas de `/all-upcoming-dates/` el 2026-11-12, y el mapeo location → destino/país/región |
| `curation.py` | alojamientos, tipos de habitación con precio y extras opcionales, **transcritos a mano** de cada página: el HTML viejo intercala nombre, precio y descripción en un orden que cambia de página a página y una lectura automática mezclaba precios entre habitaciones |
| `gen.py` | arma los `<slug>/data.json` combinando lo de arriba con el texto scrapeado (includes, itinerario, travel tips, políticas) y con la asignación de imágenes |
| `build_page.py` | llena `retreatpage-elcuyo.html` con un `data.json`. Sólo **lee** la plantilla: reemplaza las regiones variables y deja CSS y scripts intactos |

`gen.py` espera en su directorio de trabajo los intermedios del scrapeo
(`structured.json`, `img_sections.json`, `img_assign.json`, `teachers.json`), que no se
versionan: son el estado del sitio viejo en la fecha de extracción, no datos del proyecto.
Lo que sí se versiona es el resultado: `_index.json` y los `data.json`.

Validar siempre después de regenerar:

    python3 retreats/_validate.py
