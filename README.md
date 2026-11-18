# Foro Asperger

Foro sobre autismo y neurodivergencia para **foroasperger.com**. PHP 8.1+ y MySQL, sin dependencias: funciona en el hosting compartido de Hostinger tal cual.

## Qué incluye

- **Foros y subforos** en fichas con pictograma: 15 foros (según quién eres, etapa de vida y tema) y un foro privado para profesionales.
- **Hilos** con reacciones de texto («Me ayuda», «Me pasa igual», «Te mando ánimo»), indicador de tono, resumen del hilo, guardados, buscador y avisos a moderación.
- **Cuentas**: registro, acceso, recuperación de contraseña por correo y borrado de cuenta.
- **Zona privada**: foto o avatar dibujado, nombre, biografía, cómo te identificas y ajustes sensoriales (modo calma, sin animaciones, texto grande, ocultar contadores).
- **Recursos descargables**: con filtros, recurso destacado y revisión previa de lo que suben los profesionales.
- **Amor neurodivergente**: presentaciones (solo mayores de 18, solo visibles con cuenta), saludos que hay que aceptar, mensajes privados, bloqueo y avisos.
- **Zona de profesionales (de pago)**: ficha, verificación de colegiación, suscripción con Stripe, directorio y sello en sus respuestas.
- **Administración** (`/admin`): avisos, profesionales, recursos y usuarios.

## Instalar en Hostinger

1. **Base de datos**: hPanel → *Bases de datos → MySQL*. Crea una base de datos y apunta el nombre, el usuario y la contraseña.
2. **Archivos**: sube el contenido de este repositorio a `public_html` (con el *Administrador de archivos* o conectando el repositorio en hPanel → *Git*).
3. **PHP**: en hPanel → *Configuración de PHP*, elige PHP 8.1 o superior.
4. **Instalador**: abre `https://tudominio/instalar`, rellena los datos de la base de datos y crea tu cuenta de administración. El instalador crea `config.php`, las tablas y los foros.
5. **SSL**: activa el certificado gratuito y fuerza HTTPS en hPanel.

Las carpetas `uploads/avatars` y `storage` deben poder escribirse (en Hostinger ya es así).

## Activar los pagos con Stripe

Mientras no haya claves, las altas de profesionales quedan pendientes y puedes activarlas a mano en `/admin` («Activar sin Stripe»).

1. En Stripe, crea un **producto con precio mensual recurrente** para cada plan: ficha profesional, ficha destacada y centro o clínica.
2. Edita `config.php` y rellena el bloque `stripe`:
   - `secret_key`: tu clave secreta (`sk_live_…`).
   - `prices`: el ID de precio de cada plan (`price_…`).
   - `display`: el importe que se enseña en la web, por ejemplo `'19'`.
3. En Stripe → *Desarrolladores → Webhooks*, añade el endpoint `https://tudominio/stripe/webhook` con estos eventos:
   `checkout.session.completed`, `customer.subscription.created`, `customer.subscription.updated`, `customer.subscription.deleted`.
4. Copia el secreto de firma del webhook (`whsec_…`) en `webhook_secret`.
5. Activa el **portal de clientes** de Stripe para que cada profesional pueda ver sus facturas y darse de baja.

Una ficha se publica cuando está **verificada** (lo marcas tú en `/admin` tras comprobar la colegiación) **y** tiene el pago activo.

## Antes de abrir al público

- Completa los datos entre corchetes de `app/views/pages/privacidad.php` (titular, NIF, edad mínima) y revísalo con un asesor legal: la web guarda datos de salud opcionales.
- Comprueba que `mail_from` en `config.php` es un buzón real del dominio (se usa para recuperar contraseñas).
- Sube los primeros recursos desde `/recursos/subir`.

## Estructura

```
index.php            Rutas de la web
config.sample.php    Plantilla de configuración
app/controllers/     Lógica de cada sección
app/views/           Plantillas HTML
app/schema.sql       Tablas de la base de datos
app/seed.php         Foros y subforos iniciales
assets/              Estilos, JavaScript e icono
uploads/avatars/     Fotos de perfil
storage/             Archivos de recursos y registros (no accesible desde la web)
tests/e2e.js         Prueba de extremo a extremo con Playwright
```

Para cambiar los foros después de instalar, edita la tabla `forums` (por ejemplo con phpMyAdmin). Los colores disponibles son `mint`, `sky`, `green`, `amber`, `coral`, `pink`, `lav` y `sand`; los iconos están en `app/icons.php`.

## Desarrollo en local

```bash
php -S 127.0.0.1:8080 index.php   # y abre http://127.0.0.1:8080/instalar
node tests/e2e.js                 # con la web recién instalada (admin / admin12345)
```
