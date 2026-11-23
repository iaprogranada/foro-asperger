# 📥 PARA EL CEREBRO · Foro Asperger

Preparado el 2026-11-23. La sesión que construyó el foro no tiene permiso de escritura en `iaprogranada/CEREBRO`, así que este texto está pendiente de pasar allí. Cuando esté aplicado, se puede borrar este archivo.

## 1. Añadir a `06 Webs de negocio/4 Apps y plataformas/Foro Asperger.md`

Cambiar `actualizado:` a `2026-11-23` y añadir estas secciones debajo de «Ficha»:

```markdown
## Dónde está cada cosa
- **Código:** repo `iaprogranada/foro-asperger`, rama `ccr-0b59b11c-ow5mzq` (es la rama por defecto; no existe `main`). 64 archivos de aplicación.
- **Diseño fuente:** carpeta `docs/diseno/` del repo (8 pantallas + `canvas.json`) y lienzo en claude.ai.
- **Capturas de la web funcionando:** carpeta `docs/capturas/` del repo (23 imágenes, escritorio y móvil).
- **Instrucciones de instalación y de Stripe:** `README.md` del repo.
- **Skills:** no se creó ni se cambió ninguna. El diseño se hizo con el tipo «Design» de Claude, no con una skill propia.
- **No está en GitHub a propósito:** `config.php` (lleva la contraseña de la base de datos) y los archivos que suban los usuarios.

## Decisiones tomadas
- 2026-11-18 · PHP + MySQL en Hostinger compartido, sin dependencias — lo eligió Chusi frente a Vercel + Supabase.
- 2026-11-18 · Cobro de la zona de profesionales con Stripe (suscripción mensual) — lo eligió Chusi. Sin claves, las altas se activan a mano desde `/admin`.
- 2026-11-18 · Estilo «fichas táctiles»: pictogramas dibujados en vez de emojis, colores pastel, infinito dorado en lugar de la pieza de puzle, tipografías Lexend y Atkinson Hyperlegible.
- 2026-11-18 · Las presentaciones de «Amor neurodivergente» solo se ven con cuenta; para saludar hay que tener presentación propia; descargar recursos requiere cuenta.
- 2026-11-18 · El «Resumen del hilo» lo escribe moderación a mano (no es automático con IA).

## Pendiente de decidir (Chusi)
- Precio de los tres planes de profesionales.
- Edad mínima para registrarse y para la Zona joven.
- Emojis del sistema o los pictogramas actuales.
- Crear la rama `main` y abrir un pull request, o dejar la rama actual como principal.

## Probado y sin probar
- Probado: 51 comprobaciones en navegador real sobre una instalación limpia (`tests/e2e.js`) y el aviso de pago de Stripe con eventos firmados simulados.
- Sin probar: el servidor de Hostinger, el pago real con tarjeta y el envío real de correos.
```

## 2. Añadir a `01 Director/Decisiones.md` (entre las del 21-nov y las del 17-nov)

```markdown
- 2026-11-18 · [[Foro Asperger]] · Se construye en PHP + MySQL para el Hostinger compartido y cobra a los profesionales con Stripe — lo eligió Chusi: sin servicios ni cuotas extra. Pictogramas en vez de emojis y presentaciones de «Amor neurodivergente» solo visibles con cuenta.
```

## 3. Añadir arriba en `01 Director/Bitácora.md`

```markdown
- 2026-11-23 · [[Foro Asperger]] · Auditoría del proyecto: código verificado en GitHub (64 archivos idénticos al local), diseño fuente y capturas guardados en `docs/` del repo, ficha ampliada con decisiones y pendientes.
```
