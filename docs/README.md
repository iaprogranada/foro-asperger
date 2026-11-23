# Documentación del proyecto

Material del proyecto que no es código. Esta carpeta no se sirve desde la web (ver `.htaccess`).

## `diseno/`

Fuente del diseño original, hecho el 2026-11-18 en un lienzo de Claude Design:
<https://claude.ai/artifact/6ydqyAfrbmq5nL5N6PwSZz> (privado, solo lo abre su dueño).

- `canvas.json`: posición y tamaño de las ocho pantallas en el lienzo.
- Un archivo `.dc.html` por pantalla: `Main` (portada), `Foro`, `Hilo`, `Recursos`, `Amor`, `Profesionales`, `Registro` y `Perfil`.

Son la copia de seguridad del lienzo. No se abren solos en un navegador: necesitan el editor de Claude Design.

## `capturas/`

Capturas de la web real funcionando, generadas por `tests/e2e.js` el 2026-11-18. Los nombres que empiezan por `m-` son de móvil (390 px de ancho). Las personas y los mensajes que aparecen son datos de prueba inventados.

Para volver a generarlas: instala la web en local y ejecuta `node tests/e2e.js` (las deja en `tests/shots/`).

## `PARA-EL-CEREBRO.md`

Texto preparado para la nota del proyecto en el repositorio `iaprogranada/CEREBRO`.
