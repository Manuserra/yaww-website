# WordPress: informe de agentes


## B · URLs y SEO

- **Inventario del sitio vivo** (sitemaps de Yoast, 23-nov-2026): 21 páginas, 35 journal (34 posts + `/journals/`), 46 retreats (`/retreat/<slug>/`), 9 categorías de blog, 11 de journal, 8 de retreats (`/Retreats/…`), 6.345 páginas de adjuntos. Sin tags ni autores. Locations, teachers y facilitators no existen hoy.
- **Mapa**: `docs/wp-url-map.md` (130 URLs listadas + regla para los 6.345 adjuntos). 28 se mantienen (home y 27 posts con el mismo slug), el resto 301.
- **Redirecciones**: `inc/redirects.php`, solo actúa cuando WordPress daría 404 (nada que exista se toca, sin bucles). Cada entrada lista candidatos y usa el primero que exista. Cubre `/retreat/*` a `/retreats/*` o a su location, categorías a `/journal/`, sitemaps de Yoast a `/wp-sitemap.xml`, adjuntos (siguen al padre), y los `.html` del prototipo (`retreatpage-*`, `locationpage-*`, `teacher-*`, `facilitator-*`, `team-*`, `journal/*.html`).
- **SEO**: `inc/seo.php` (sin plugin): título, descripción (`seo_description`, luego extracto, luego texto generado), canonical, Open Graph, Twitter, JSON-LD (Organization, WebSite, Event, TouristTrip, TouristDestination, Person, BlogPosting, BreadcrumbList), noindex en búsqueda, 404, adjuntos, `lead` y posts con meta `seo_noindex`=1. Sitemap nativo `/wp-sitemap.xml` sin autores ni adjuntos; robots.txt virtual sin `Crawl-delay`.
- **Verificado en localwpcom.local** (con un mu-plugin temporal, ya borrado): 301 de 13 rutas con `curl -I`, head de la home, robots.txt, sitemap, y un retreat de prueba (borrado). **Pendiente** con el contenido real: títulos únicos de todos los retreats, destinos finales de las redirecciones (hoy caen en `/retreats/` porque no hay posts) y el `Event` con precios.
- **Para A (functions.php)**: añadir `require_once YAWW_DIR . '/inc/redirects.php';` y `require_once YAWW_DIR . '/inc/seo.php';` después de `fields.php`. functions.php no carga `inc/*.php` solo.
- **Preguntas**: ver el final de `docs/wp-url-map.md` (páginas de políticas con slug `privacy_policies`, `termsandconditions`, `cancellation_policies`; retreats sin destino nuevo como Cuba, Guatemala, Holbox, Tulum; 7 slugs de journal que difieren, conviene importarlos con el slug vivo).
