<?php
/** Funciones comunes de Foro Asperger. */

// ---------- Catálogos ----------

const COLORS = ['mint', 'sky', 'green', 'amber', 'coral', 'pink', 'lav', 'sand'];

const TONES = [
    'serio'    => 'Lo digo en serio',
    'broma'    => 'Es una broma',
    'pregunta' => 'Es una pregunta sincera',
    'desahogo' => 'Solo quiero desahogarme',
];

const REACTIONS = [
    'ayuda' => ['Me ayuda', 'check', 'mint'],
    'igual' => ['Me pasa igual', 'users', 'lav'],
    'animo' => ['Te mando ánimo', 'heart', 'pink'],
];

const IDENTITIES = [
    'autista'   => 'Autista',
    'asperger'  => 'Asperger',
    'tdah'      => 'TDAH',
    'aacc'      => 'Altas capacidades',
    'familiar'  => 'Familiar',
    'sin_dx'    => 'Sin diagnóstico todavía',
    'no_decir'  => 'Prefiero no decirlo',
];

const WHO = [
    'nd'      => ['Soy autista o neurodivergente', 'inf', 'mint'],
    'family'  => ['Soy familiar o pareja', 'house', 'pink'],
    'pro'     => ['Soy profesional', 'shield', 'amber'],
    'curious' => ['Quiero aprender sobre autismo', 'search', 'sky'],
];

const LOOKING = [
    'amistad' => ['Amistad', 'amistad', 'Alguien con quien hablar y compartir aficiones', 'users', 'mint'],
    'grupo'   => ['Un grupo de amigos', 'un grupo de amigos', 'Varias personas para quedar o jugar online', 'group', 'sky'],
    'pareja'  => ['Alguien especial', 'a alguien especial', 'Conocer a una persona para una relación de pareja', 'heart', 'pink'],
];

const COMM = [
    'texto'   => ['Mensajes escritos', 'Prefiere mensajes escritos', 'chat'],
    'voz'     => ['Notas de voz', 'Prefiere notas de voz', 'mic'],
    'video'   => ['Videollamada', 'Prefiere videollamada', 'video'],
    'persona' => ['En persona', 'Prefiere quedar en persona', 'pin'],
];

const INTERESTS = ['Senderismo', 'Juegos de mesa', 'Videojuegos', 'Astronomía', 'Dibujo', 'Música', 'Animales',
    'Programación', 'Cine', 'Lectura', 'Cocina', 'Fotografía', 'Deporte', 'Ciencia', 'Manga y anime', 'Naturaleza'];

const RES_KINDS = [
    'guia'        => ['Guía', 'Guías', 'book'],
    'plantilla'   => ['Plantilla', 'Plantillas', 'calendar'],
    'pictogramas' => ['Pictogramas', 'Pictogramas', 'image'],
];

const AUDIENCES = [
    'todos'    => 'Para todos',
    'adultos'  => 'Para adultos',
    'familias' => 'Para familias',
    'trabajo'  => 'Para el trabajo',
];

const PLANS = [
    'basic' => ['Ficha profesional', ['Ficha en el directorio', 'Sello de profesional verificado', 'Botón «Pedir cita»', 'Acceso al foro privado']],
    'featured' => ['Ficha destacada', ['Todo lo de la ficha profesional', 'Apareces primero en tu ciudad', 'Publicas recursos con tu nombre', 'Respondes con tu sello en todos los foros']],
    'center' => ['Centro o clínica', ['Todo lo de la ficha destacada', 'Nombre del centro en la ficha', 'Varias especialidades del equipo', 'Anuncias talleres y eventos']],
];

const AVATAR_ICONS = ['inf', 'planet', 'leaf', 'spark', 'headphones', 'star', 'heart', 'tree', 'bolt', 'key'];

/** Condición SQL: el profesional (alias pr) está verificado y con el pago al día. */
const PRO_ACTIVE = "(pr.user_id IS NOT NULL AND pr.verified_at IS NOT NULL AND (pr.manual_active = 1 OR pr.sub_status IN ('active','trialing')))";

// ---------- Configuración ----------

function cfg(string $path, mixed $default = null): mixed
{
    $v = $GLOBALS['config'] ?? [];
    foreach (explode('.', $path) as $k) {
        if (!is_array($v) || !array_key_exists($k, $v)) return $default;
        $v = $v[$k];
    }
    return $v;
}

// ---------- Base de datos ----------

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = cfg('db');
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $c['host'], $c['port'] ?? 3306, $c['name']);
        $pdo = new PDO($dsn, $c['user'], $c['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec("SET time_zone = '" . date('P') . "'");
    }
    return $pdo;
}

function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}
function row(string $sql, array $params = []): ?array { $r = q($sql, $params)->fetch(); return $r ?: null; }
function rows(string $sql, array $params = []): array { return q($sql, $params)->fetchAll(); }
function val(string $sql, array $params = []): mixed { return q($sql, $params)->fetchColumn(); }
function insert(string $sql, array $params = []): int { q($sql, $params); return (int) db()->lastInsertId(); }

// ---------- Salida ----------

function e(mixed $s): string { return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path = '/'): string { return BASE . $path; }
function abs_url(string $path = '/'): string { return rtrim((string) cfg('base_url', ''), '/') . $path; }
function asset(string $file): string
{
    $full = ROOT . '/assets/' . $file;
    return BASE . '/assets/' . $file . '?v=' . (is_file($full) ? filemtime($full) : '1');
}

function redirect(string $path): never
{
    header('Location: ' . (preg_match('#^https?://#', $path) ? $path : url($path)), true, 303);
    exit;
}

function back(string $fallback = '/'): never
{
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($ref && parse_url($ref, PHP_URL_HOST) === explode(':', $host)[0]) {
        header('Location: ' . $ref, true, 303);
        exit;
    }
    redirect($fallback);
}

function abort(int $code = 404, string $message = ''): never
{
    http_response_code($code);
    $titles = [403 => 'No tienes permiso para ver esta página', 404 => 'No encontramos esta página', 429 => 'Demasiados intentos'];
    view('error', ['title' => $titles[$code] ?? 'Ha ocurrido un error', 'message' => $message, 'code' => $code]);
}

function view(string $name, array $data = []): never
{
    extract($data, EXTR_SKIP);
    ob_start();
    require APP . '/views/' . $name . '.php';
    $content = ob_get_clean();
    require APP . '/views/layout.php';
    exit;
}

function partial(string $name, array $data = []): void
{
    extract($data, EXTR_SKIP);
    require APP . '/views/partials/' . $name . '.php';
}

function icon(string $name, int $size = 24, string $extra = ''): string
{
    static $icons = null;
    $icons ??= require APP . '/icons.php';
    $inner = $icons[$name] ?? $icons['chat'];
    return '<svg class="ic" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" aria-hidden="true" ' . $extra . '>' . $inner . '</svg>';
}

function color(string $c): string { return in_array($c, COLORS, true) ? $c : 'mint'; }

function avatar(array $u, int $size = 52, string $class = ''): string
{
    $style = 'width:' . $size . 'px;height:' . $size . 'px';
    if (($u['avatar_type'] ?? 'icon') === 'photo' && !empty($u['avatar_value'])) {
        return '<img class="avatar ' . $class . '" style="' . $style . '" src="' . e(url('/uploads/avatars/' . $u['avatar_value'])) . '" alt="" loading="lazy">';
    }
    [$ic, $col] = array_pad(explode(':', $u['avatar_value'] ?? 'inf:mint'), 2, 'mint');
    return '<span class="avatar c-' . color($col) . ' ' . $class . '" style="' . $style . '">' . icon($ic, (int) round($size * 0.58)) . '</span>';
}

function display_name(array $u): string { return e($u['display_name'] ?: $u['username']); }

function time_ago(?string $dt): string
{
    if (!$dt) return '';
    $d = time() - strtotime($dt);
    if ($d < 60) return 'hace un momento';
    if ($d < 3600) { $m = (int) floor($d / 60); return 'hace ' . $m . ($m === 1 ? ' minuto' : ' minutos'); }
    if ($d < 86400) { $h = (int) floor($d / 3600); return 'hace ' . $h . ($h === 1 ? ' hora' : ' horas'); }
    if ($d < 172800) return 'ayer';
    if ($d < 2592000) return 'hace ' . (int) floor($d / 86400) . ' días';
    $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    $t = strtotime($dt);
    return 'el ' . date('j', $t) . ' de ' . $meses[(int) date('n', $t) - 1] . ' de ' . date('Y', $t);
}

/** Texto de un mensaje: se escapa, se separa en párrafos y se enlazan las direcciones web. */
function format_body(string $text): string
{
    $text = e(trim(str_replace(["\r\n", "\r"], "\n", $text)));
    $text = preg_replace_callback('#\bhttps?://[^\s<]+#u', function ($m) {
        $u = rtrim($m[0], '.,;:!?)');
        return '<a href="' . $u . '" rel="nofollow ugc noopener" target="_blank">' . $u . '</a>' . substr($m[0], strlen($u));
    }, $text);
    $out = '';
    foreach (preg_split("/\n{2,}/", $text) as $p) {
        if (trim($p) !== '') $out .= '<p>' . nl2br($p, false) . '</p>';
    }
    return $out;
}

function slugify(string $s): string
{
    $s = mb_strtolower(trim($s), 'UTF-8');
    $s = strtr($s, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n', 'ç' => 'c', 'à' => 'a', 'è' => 'e', 'ò' => 'o']);
    $s = trim(preg_replace('/[^a-z0-9]+/', '-', $s), '-');
    return substr($s, 0, 80) ?: 'hilo';
}

function plural(int $n, string $one, string $many): string { return $n . ' ' . ($n === 1 ? $one : $many); }

function csv_list(?string $s): array { return array_values(array_filter(array_map('trim', explode(',', (string) $s)), 'strlen')); }

function paginate(int $total, int $per): array
{
    $pages = max(1, (int) ceil($total / $per));
    $page = min($pages, max(1, (int) ($_GET['p'] ?? 1)));
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $per, 'per' => $per, 'total' => $total];
}

function page_url(int $p): string
{
    $qs = $_GET; $qs['p'] = $p;
    return url(PATH) . '?' . http_build_query($qs);
}

// ---------- Sesión, CSRF y avisos ----------

function csrf_token(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field(): string { return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">'; }
function csrf_check(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', (string) ($_POST['_csrf'] ?? ''))) {
        abort(403, 'El formulario ha caducado. Vuelve atrás, recarga la página e inténtalo otra vez.');
    }
}

function flash(string $type, string $msg): void { $_SESSION['flash'][] = [$type, $msg]; }
function flashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

function old(string $key, mixed $default = ''): mixed { return $_POST[$key] ?? $default; }
function post(string $key, string $default = ''): string { return trim((string) ($_POST[$key] ?? $default)); }

/** Limita acciones repetidas (entrar, registrarse, publicar). Devuelve false si se supera el máximo. */
function throttle(string $key, int $max, int $seconds): bool
{
    $key = substr($key, 0, 190);
    q('DELETE FROM throttle WHERE reset_at < NOW()');
    q('INSERT INTO throttle (k, hits, reset_at) VALUES (?, 1, DATE_ADD(NOW(), INTERVAL ? SECOND)) ON DUPLICATE KEY UPDATE hits = hits + 1', [$key, $seconds]);
    return (int) val('SELECT hits FROM throttle WHERE k = ?', [$key]) <= $max;
}

function client_ip(): string { return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; }

// ---------- Usuarios y permisos ----------

function user(): ?array { return $GLOBALS['me'] ?? null; }
function uid(): int { return (int) (user()['id'] ?? 0); }
function is_mod(): bool { return in_array(user()['role'] ?? '', ['mod', 'admin'], true); }
function is_admin(): bool { return (user()['role'] ?? '') === 'admin'; }
function is_pro(): bool { return !empty(user()['is_pro']); }

function load_user(int $id): ?array
{
    return row('SELECT u.*, ' . PRO_ACTIVE . ' AS is_pro, pr.plan AS pro_plan FROM users u LEFT JOIN professionals pr ON pr.user_id = u.id WHERE u.id = ?', [$id]);
}

function require_login(): array
{
    if (!user()) {
        $_SESSION['after_login'] = PATH;
        flash('info', 'Para continuar necesitas entrar en tu cuenta.');
        redirect('/entrar');
    }
    return user();
}

function require_mod(): void { require_login(); if (!is_mod()) abort(403); }
function require_admin(): void { require_login(); if (!is_admin()) abort(403); }

function can_see_forum(array $forum): bool { return empty($forum['pro_only']) || is_pro() || is_mod(); }

function blocked_between(int $a, int $b): bool
{
    return (bool) val('SELECT 1 FROM blocks WHERE (blocker_id = ? AND blocked_id = ?) OR (blocker_id = ? AND blocked_id = ?) LIMIT 1', [$a, $b, $b, $a]);
}

function unread_count(): int
{
    if (!uid()) return 0;
    static $n = null;
    return $n ??= (int) val('SELECT COUNT(*) FROM messages m JOIN conversations c ON c.id = m.conversation_id WHERE (c.user_a = ? OR c.user_b = ?) AND m.sender_id <> ? AND m.read_at IS NULL', [uid(), uid(), uid()])
        + (int) val("SELECT COUNT(*) FROM greetings WHERE to_id = ? AND status = 'pending'", [uid()]);
}

// ---------- Correo ----------

function send_mail(string $to, string $subject, string $body): void
{
    $from = cfg('mail_from', 'no-responder@' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
    $headers = "From: " . cfg('site_name', 'Foro Asperger') . " <{$from}>\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    $ok = !cfg('debug') && @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers);
    if (!$ok) {
        @file_put_contents(ROOT . '/storage/mail.log', date('c') . " → {$to}\n{$subject}\n{$body}\n\n", FILE_APPEND);
    }
}

// ---------- Archivos ----------

/** Guarda una foto de perfil: la recorta en cuadrado, la reduce a 256 px y la vuelve a codificar. */
function save_avatar(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) return null;
    $info = @getimagesize($file['tmp_name']);
    if (!$info) return null;
    $src = match ($info[2]) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($file['tmp_name']),
        IMAGETYPE_PNG  => @imagecreatefrompng($file['tmp_name']),
        IMAGETYPE_WEBP => @imagecreatefromwebp($file['tmp_name']),
        default => false,
    };
    if (!$src) return null;
    $w = imagesx($src); $h = imagesy($src); $side = min($w, $h);
    $dst = imagecreatetruecolor(256, 256);
    imagefill($dst, 0, 0, imagecolorallocate($dst, 252, 252, 250));
    imagecopyresampled($dst, $src, 0, 0, (int) (($w - $side) / 2), (int) (($h - $side) / 2), 256, 256, $side, $side);
    $name = bin2hex(random_bytes(12)) . '.jpg';
    $ok = imagejpeg($dst, ROOT . '/uploads/avatars/' . $name, 86);
    imagedestroy($src); imagedestroy($dst);
    return $ok ? $name : null;
}

function human_size(int $bytes): string
{
    if ($bytes >= 1048576) return str_replace('.', ',', (string) round($bytes / 1048576, 1)) . ' MB';
    return max(1, (int) round($bytes / 1024)) . ' KB';
}

// ---------- Consultas compartidas ----------

const THREAD_COLS = 't.id, t.title, t.slug, t.pinned, t.locked, t.reply_count, t.helpful_count, t.last_post_at, t.created_at, u.username, u.display_name, u.avatar_type, u.avatar_value, f.name AS sub_name, f.color AS sub_color, f.pro_only';
const THREAD_FROM = 'FROM threads t JOIN users u ON u.id = t.user_id JOIN forums f ON f.id = t.forum_id';

function thread_url(array $t): string { return url('/hilo/' . $t['id'] . '-' . $t['slug']); }
