<?php
/**
 * Configuración de Foro Asperger.
 *
 * El instalador (/instalar) crea config.php por ti. Si prefieres hacerlo a mano,
 * copia este archivo como config.php y rellena los datos.
 */
return [
    'site_name' => 'Foro Asperger',
    // Dirección pública de la web, sin barra final.
    'base_url'  => 'https://foroasperger.com',

    // Base de datos MySQL (hPanel de Hostinger → Bases de datos → MySQL).
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => '',
        'user' => '',
        'pass' => '',
    ],

    // Dirección desde la que salen los correos (recuperar contraseña, avisos).
    'mail_from' => 'hola@foroasperger.com',

    // Stripe: zona de pago para profesionales.
    // Mientras secret_key esté vacío, las altas quedan como «pendientes» y se activan desde /admin.
    'stripe' => [
        'secret_key'     => '',   // sk_live_... o sk_test_...
        'webhook_secret' => '',   // whsec_... (endpoint: https://tudominio/stripe/webhook)
        // ID de precio recurrente de cada plan (price_...).
        'prices' => [
            'basic'    => '',
            'featured' => '',
            'center'   => '',
        ],
        // Precio que se muestra en la web para cada plan, en euros al mes. Ejemplo: '19'.
        'display' => [
            'basic'    => '',
            'featured' => '',
            'center'   => '',
        ],
    ],

    // true solo en desarrollo: muestra los errores en pantalla.
    'debug' => false,
];
