<?php
/** Arranque: configuración, sesión, usuario actual. */

date_default_timezone_set('Europe/Madrid');
mb_internal_encoding('UTF-8');

require APP . '/helpers.php';

// Ruta base (por si la web vive en una subcarpeta) y ruta pedida.
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
define('BASE', PHP_SAPI === 'cli-server' ? '' : rtrim($scriptDir, '/'));
$p = $GLOBALS['uriPath'] ?? '/';
if (BASE !== '' && str_starts_with($p, BASE)) $p = substr($p, strlen(BASE));
define('PATH', '/' . trim($p, '/'));

$configFile = ROOT . '/config.php';
$GLOBALS['config'] = is_file($configFile) ? require $configFile : [];

$debug = (bool) cfg('debug', false);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', ROOT . '/storage/php-error.log');

$https = (($_SERVER['HTTPS'] ?? '') === 'on') || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
session_name('fa_sesion');
session_set_cookie_params(['lifetime' => 60 * 60 * 24 * 30, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
ini_set('session.gc_maxlifetime', (string) (60 * 60 * 24 * 30));
session_start();

set_exception_handler(function (Throwable $e) use ($debug) {
    error_log((string) $e);
    // Hay configuración pero faltan las tablas: toca pasar por el instalador.
    if ($e instanceof PDOException && $e->getCode() === '42S02' && PATH !== '/instalar' && !headers_sent()) {
        header('Location: ' . url('/instalar'), true, 303);
        exit;
    }
    if (!headers_sent()) http_response_code(500);
    while (ob_get_level()) ob_end_clean();
    $msg = $debug ? e($e->getMessage() . ' — ' . basename($e->getFile()) . ':' . $e->getLine()) : 'Ha ocurrido un error. Inténtalo de nuevo dentro de unos minutos.';
    echo '<!doctype html><meta charset="utf-8"><title>Error</title><body style="font-family:Verdana,sans-serif;padding:40px;background:#F5F6F2;color:#1E2A32"><h1>Algo ha fallado</h1><p>' . $msg . '</p><p><a href="' . e(url('/')) . '">Volver al inicio</a></p>';
});

// Sin configuración todavía: solo funciona el instalador.
$GLOBALS['me'] = null;
if (!$GLOBALS['config']) {
    if (PATH !== '/instalar') redirect('/instalar');
    return;
}

if (!empty($_SESSION['uid'])) {
    $me = load_user((int) $_SESSION['uid']);
    if (!$me || $me['banned_at']) {
        unset($_SESSION['uid']);
    } else {
        $GLOBALS['me'] = $me;
        if (!$me['last_seen_at'] || strtotime($me['last_seen_at']) < time() - 300) {
            q('UPDATE users SET last_seen_at = NOW() WHERE id = ?', [$me['id']]);
        }
    }
}
