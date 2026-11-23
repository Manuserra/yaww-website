# Council · Contact page · 2026-11-23

**Pregunta del usuario:** "Sigo sin estar convencido con la página contact, es re importante, no sé en qué estoy fallando."

**Pregunta enmarcada (para los 5):** ¿Cuál es el trabajo real de la página de contacto de YAWW, qué está fallando y qué estructura concreta construir esta noche? Contexto: desde 2008, 4.9 en Google (45 reseñas), retiros de 2.300–3.500 USD, público mayormente mujeres de 40–65, muchas viajan solas; Christina (founder), Jenniferlyn (guest care, responde en 24 h); cada retiro ya tiene su pop-up "I want more info"; los teachers traen grupos enteros. Página actual: dos columnas, sin foto, email faltante, bloque de confianza, link chico para teachers, formulario con chips.

Nota de método: por presupuesto, la ronda de revisión cruzada se hizo con 2 revisores en vez de 5; el chairman fue el orquestador.

## Las 5 respuestas (resumen fiel)

**Contrarian.** Falla porque hace trabajar al visitante y no muestra a ninguna persona: promete calidez y entrega un formulario genérico. El email faltante es un bug. Los teachers son un agregado. El segmento más grande ("no sé cuál retiro") no tiene camino. Los huéspedes ya reservados no tienen lugar. Construir: promesa de 24 h firmada por Jenniferlyn, fotos, formulario corto con "no sé" primero, líneas directas, banda de teachers, cita junto al formulario.

**First Principles.** Pregunta equivocada: no es layout, es qué necesita una mujer nerviosa de 52 años, a punto de gastar 3.000 USD sola, para apretar Send. El trabajo es el momento en que decide si hay una persona amable del otro lado; el segundo trabajo es rutear sin menú de call center. Construir: foto de Jenniferlyn con la promesa de 24 h; chips "Help me choose / Private group / Something else" con el primero por defecto; email y WhatsApp reales; una cita sobre "me respondieron"; banda ancha para teachers con botón. Lo de mayor palanca: una cara, un nombre y un email real.

**Expansionist.** Es una conversación de venta, no un formulario. Fotos y voz de Jenniferlyn y Christina; tres puertas grandes (elegir retiro, grupo, liderar un retiro), cada una con su formulario; tira "qué pasa después"; la puerta de teachers como pipeline ("¿cuántos invitados podrías traer?").

**Outsider (58 años, sola, primer retiro).** "We would love to hear from you" es papel tapiz. Sin caras, es la falla mayor. El email vacío parece roto. Mi pregunta ("¿es para mí, sola, a mi edad?") no tiene botón. Demasiados campos; "A call" asusta; "+52 1" desconcierta; no sé qué pasa después de enviar. Construir: "Questions before you book? Ask Jenniferlyn.", su foto junto al formulario, formulario corto con prompt "First retreat? Traveling alone?", "what happens next", tres mini respuestas (¿sola? ¿experiencia? ¿cancelación?).

**Executor.** Hero con fotos de Jenniferlyn y Christina y la promesa de 24 h; formulario de una columna; chips que incluyan "Not sure which retreat" y "Already booked"; líneas directas; confianza después del formulario. Se hace con un HTML y un CSS, con fotos que ya existen.

## Revisión cruzada

- **Más sólida (los dos revisores):** First Principles; Contrarian segunda.
- **Puntos ciegos:** Expansionist sobre-construye (tres formularios) y es demasiado transaccional para este público; Outsider recorta lo que el propio usuario pidió (newsletter, campos de grupo) y casi borra a teachers y grupos.
- **Lo que nadie vio:** el pop-up de cada retiro ya resuelve las preguntas sobre un retiro concreto, así que contact puede ser más chico; accesibilidad para 40–65 (labels arriba, ≥16 px, contraste AA, objetivos de 44 px); qué pasa después de enviar (confirmación, auto-respuesta, a quién llega); spam; la voz en primera persona de Jenniferlyn sería copy inventado; mirar los últimos mensajes reales para saber qué pregunta la gente.

## Veredicto del chairman

**Acuerdo (alto):** falta una persona. La página dice "alguien lee cada mensaje" y no muestra a nadie. Arreglo de mayor palanca: la cara y el nombre de Jenniferlyn con la promesa de 24 h, y un email real.

**Choque:** formulario corto vs. ruteo. El Outsider quiere sacar los chips; Executor y First Principles los quieren, pero empezando por "Help me choose". Gana el ruteo suave: un chip por defecto que habla el idioma de la persona indecisa, sin obligarla a clasificarse.

**Recomendación:** mantener dos columnas. Izquierda: título, Jenniferlyn (foto, nombre, guest care, "writes back within 24 hours") y Christina más chica, líneas directas y la cita. Derecha: formulario con "Help me choose a retreat" por defecto, prompt en el mensaje ("First retreat? Travelling alone?…") y una línea "qué pasa después" bajo el botón. Abajo, una banda ancha para teachers con botón. Nada de voz en primera persona inventada.

**Lo primero:** conseguir el email público real (y confirmar si el teléfono atiende WhatsApp). Sin eso, cualquier versión se ve rota.
