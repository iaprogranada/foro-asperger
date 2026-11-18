<?php
/** Recursos descargables. */

const RES_EXT = [
    'pdf'  => ['application/pdf'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip', 'application/octet-stream'],
    'odt'  => ['application/vnd.oasis.opendocument.text', 'application/zip', 'application/octet-stream'],
    'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip', 'application/octet-stream'],
    'zip'  => ['application/zip'],
    'png'  => ['image/png'],
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
];

function can_upload_resources(): bool
{
    return is_mod() || (is_pro() && in_array(user()['pro_plan'] ?? '', ['featured', 'center'], true));
}

function resources_index(): void
{
    $kind = isset(RES_KINDS[$_GET['tipo'] ?? '']) ? $_GET['tipo'] : '';
    $aud = isset(AUDIENCES[$_GET['para'] ?? '']) && ($_GET['para'] ?? '') !== 'todos' ? $_GET['para'] : '';
    $term = trim((string) ($_GET['q'] ?? ''));

    $where = "r.status = 'published'"; $params = [];
    if ($kind) { $where .= ' AND r.kind = ?'; $params[] = $kind; }
    if ($aud) { $where .= ' AND r.audience = ?'; $params[] = $aud; }
    if ($term !== '') { $like = '%' . addcslashes($term, '%_\\') . '%'; $where .= ' AND (r.title LIKE ? OR r.description LIKE ?)'; array_push($params, $like, $like); }

    $list = rows("SELECT r.*, u.display_name, u.username, " . PRO_ACTIVE . " AS by_pro FROM resources r JOIN users u ON u.id = r.uploader_id LEFT JOIN professionals pr ON pr.user_id = u.id WHERE {$where} ORDER BY r.featured DESC, r.created_at DESC LIMIT 60", $params);
    $featured = null;
    if (!$kind && !$aud && $term === '' && $list && $list[0]['featured']) $featured = array_shift($list);

    view('resources/index', ['title' => 'Recursos para descargar', 'active' => 'recursos', 'list' => $list, 'featured' => $featured,
        'kind' => $kind, 'aud' => $aud, 'term' => $term, 'canUpload' => can_upload_resources()]);
}

function resources_download(string $id): void
{
    require_login();
    $r = row('SELECT * FROM resources WHERE id = ?', [(int) $id]);
    if (!$r || ($r['status'] !== 'published' && !is_mod() && (int) $r['uploader_id'] !== uid())) abort(404);
    $path = ROOT . '/storage/resources/' . basename($r['file_name']);
    if (!is_file($path)) abort(404, 'El archivo de este recurso ya no está disponible.');
    q('UPDATE resources SET downloads = downloads + 1 WHERE id = ?', [$r['id']]);
    $name = preg_replace('/[^\w. -]+/u', '_', $r['original_name']);
    header('Content-Type: ' . $r['mime']);
    header('Content-Length: ' . filesize($path));
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('X-Content-Type-Options: nosniff');
    readfile($path);
    exit;
}

function resources_upload(): void
{
    require_login();
    if (!can_upload_resources()) abort(403, 'Pueden publicar recursos el equipo del foro y los profesionales con ficha destacada o de centro.');
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = post('title'); $desc = post('description');
        $kind = isset(RES_KINDS[post('kind')]) ? post('kind') : 'guia';
        $aud = isset(AUDIENCES[post('audience')]) ? post('audience') : 'todos';
        $f = $_FILES['file'] ?? null;

        if (mb_strlen($title) < 5 || mb_strlen($title) > 120) $errors[] = 'El título debe tener entre 5 y 120 caracteres.';
        if (mb_strlen($desc) > 300) $errors[] = 'La descripción tiene más de 300 caracteres.';
        if (!$f || $f['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Elige el archivo que quieres publicar. El tamaño máximo es 15 MB.';
        } else {
            $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']) ?: '';
            if (!isset(RES_EXT[$ext]) || !in_array($mime, RES_EXT[$ext], true)) $errors[] = 'Ese tipo de archivo no se admite. Puedes subir PDF, Word (docx), OpenDocument (odt), PowerPoint (pptx), ZIP, PNG o JPG.';
            elseif ($f['size'] > 15 * 1024 * 1024) $errors[] = 'El archivo pesa más de 15 MB.';
        }

        if (!$errors) {
            @mkdir(ROOT . '/storage/resources', 0755, true);
            $stored = bin2hex(random_bytes(16)) . '.' . $ext;
            if (!move_uploaded_file($f['tmp_name'], ROOT . '/storage/resources/' . $stored)) {
                $errors[] = 'No se pudo guardar el archivo. Inténtalo otra vez.';
            } else {
                $icons = ['guia' => 'book', 'plantilla' => 'calendar', 'pictogramas' => 'chat-lines'];
                $mimeOut = in_array($ext, ['docx', 'odt', 'pptx'], true) ? RES_EXT[$ext][0] : $mime;
                q('INSERT INTO resources (title, description, kind, audience, icon, color, file_name, original_name, mime, size, uploader_id, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)',
                    [$title, $desc, $kind, $aud, $icons[$kind], COLORS[array_rand(COLORS)], $stored, mb_substr($f['name'], 0, 190), $mimeOut, (int) $f['size'], uid(), is_mod() ? 'published' : 'pending']);
                flash('ok', is_mod() ? 'Recurso publicado.' : 'Recurso enviado. El equipo del foro lo revisa antes de publicarlo.');
                redirect('/recursos');
            }
        }
    }
    view('resources/upload', ['title' => 'Publicar un recurso', 'active' => 'recursos', 'errors' => $errors]);
}
