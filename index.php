<?php
/**
 * Foro Asperger · controlador frontal.
 * Todas las direcciones de la web pasan por aquí (ver .htaccess).
 */

declare(strict_types=1);

define('ROOT', __DIR__);
define('APP', ROOT . '/app');

$uriPath = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

// Servidor de desarrollo (php -S localhost:8080 index.php): sirve solo assets y fotos subidas.
if (PHP_SAPI === 'cli-server') {
    if (preg_match('#^/(assets|uploads)/[\w./-]+\.(css|js|svg|png|jpg|jpeg|webp|ico|woff2)$#', $uriPath) && is_file(ROOT . $uriPath)) {
        return false;
    }
}

require APP . '/bootstrap.php';

$routes = [
    // método, patrón, archivo de controlador, función
    ['GET',  '/',                                   'home',      'home_index'],
    ['GET',  '/buscar',                             'home',      'home_search'],
    ['ANY',  '/instalar',                           'install',   'install_run'],

    ['ANY',  '/registro',                           'auth',      'auth_register'],
    ['ANY',  '/entrar',                             'auth',      'auth_login'],
    ['POST', '/salir',                              'auth',      'auth_logout'],
    ['ANY',  '/recuperar',                          'auth',      'auth_forgot'],
    ['ANY',  '/restablecer/([a-f0-9]{64})',         'auth',      'auth_reset'],

    ['GET',  '/foro/([a-z0-9-]+)',                  'forum',     'forum_show'],
    ['ANY',  '/foro/([a-z0-9-]+)/nuevo',            'forum',     'thread_new'],
    ['GET',  '/foro/([a-z0-9-]+)/([a-z0-9-]+)',     'forum',     'forum_show'],
    ['GET',  '/hilo/(\d+)(?:-[a-z0-9-]*)?',         'forum',     'thread_show'],
    ['POST', '/hilo/(\d+)/responder',               'forum',     'thread_reply'],
    ['POST', '/hilo/(\d+)/guardar',                 'forum',     'thread_bookmark'],
    ['POST', '/hilo/(\d+)/moderar',                 'forum',     'thread_moderate'],
    ['POST', '/post/(\d+)/reaccion',                'forum',     'post_react'],
    ['ANY',  '/post/(\d+)/editar',                  'forum',     'post_edit'],
    ['POST', '/post/(\d+)/borrar',                  'forum',     'post_delete'],
    ['POST', '/avisar',                             'forum',     'report_create'],

    ['GET',  '/recursos',                           'resources', 'resources_index'],
    ['ANY',  '/recursos/subir',                     'resources', 'resources_upload'],
    ['GET',  '/recursos/(\d+)/descargar',           'resources', 'resources_download'],

    ['GET',  '/amor',                               'amor',      'amor_index'],
    ['POST', '/amor/presentacion',                  'amor',      'amor_save'],
    ['POST', '/amor/presentacion/borrar',           'amor',      'amor_delete'],
    ['POST', '/amor/saludo/(\d+)',                  'amor',      'amor_greet'],
    ['POST', '/amor/saludo/(\d+)/responder',        'amor',      'amor_answer'],
    ['POST', '/bloquear/(\d+)',                     'amor',      'user_block'],
    ['GET',  '/mensajes',                           'amor',      'messages_index'],
    ['GET',  '/mensajes/(\d+)',                     'amor',      'messages_show'],
    ['POST', '/mensajes/(\d+)',                     'amor',      'messages_send'],

    ['GET',  '/profesionales',                      'pros',      'pros_landing'],
    ['GET',  '/profesionales/directorio',           'pros',      'pros_directory'],
    ['ANY',  '/profesionales/alta',                 'pros',      'pros_apply'],
    ['POST', '/profesionales/pagar',                'pros',      'pros_checkout'],
    ['POST', '/profesionales/facturacion',          'pros',      'pros_portal'],
    ['GET',  '/profesionales/gracias',              'pros',      'pros_thanks'],
    ['GET',  '/profesionales/ver/([A-Za-z0-9_.-]+)', 'pros',     'pros_show'],
    ['POST', '/stripe/webhook',                     'pros',      'stripe_webhook'],

    ['ANY',  '/perfil',                             'profile',   'profile_edit'],
    ['GET',  '/perfil/hilos',                       'profile',   'profile_threads'],
    ['GET',  '/perfil/guardados',                   'profile',   'profile_bookmarks'],
    ['ANY',  '/perfil/cuenta',                      'profile',   'profile_account'],
    ['GET',  '/u/([A-Za-z0-9_.-]+)',                'profile',   'profile_public'],

    ['GET',  '/admin',                              'admin',     'admin_index'],
    ['POST', '/admin/accion',                       'admin',     'admin_action'],

    ['GET',  '/(normas|privacidad|accesibilidad|contacto)', 'home', 'home_page'],
];

$method = $_SERVER['REQUEST_METHOD'] === 'HEAD' ? 'GET' : $_SERVER['REQUEST_METHOD'];

foreach ($routes as [$m, $pattern, $file, $fn]) {
    if (!preg_match('#^' . $pattern . '$#', PATH, $args)) continue;
    if ($m !== 'ANY' && $m !== $method) continue;
    if ($m === 'ANY' && !in_array($method, ['GET', 'POST'], true)) continue;
    if ($method === 'POST' && $fn !== 'stripe_webhook') csrf_check();
    require APP . '/controllers/' . $file . '.php';
    array_shift($args);
    $fn(...$args);
    exit;
}

abort(404);
