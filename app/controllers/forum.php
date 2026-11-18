<?php
/** Foros, subforos, hilos, respuestas, reacciones y avisos a moderación. */

const PER_PAGE = 20;

function find_forum(string $slug): array
{
    $forum = row('SELECT * FROM forums WHERE parent_id IS NULL AND slug = ?', [$slug]);
    if (!$forum) abort(404);
    if (!can_see_forum($forum)) abort(403, 'Este foro es solo para profesionales verificados. Puedes darte de alta en la zona de profesionales.');
    return $forum;
}

function find_thread(int $id): array
{
    $t = row('SELECT t.*, f.name AS sub_name, f.slug AS sub_slug, f.color AS sub_color, f.pro_only, f.parent_id,
                     pf.name AS forum_name, pf.slug AS forum_slug
              FROM threads t JOIN forums f ON f.id = t.forum_id LEFT JOIN forums pf ON pf.id = f.parent_id
              WHERE t.id = ? AND t.deleted_at IS NULL', [$id]);
    if (!$t) abort(404);
    if (!can_see_forum($t)) abort(403, 'Este hilo está en el foro privado de profesionales.');
    return $t;
}

function forum_show(string $slug, ?string $subSlug = null): void
{
    $forum = find_forum($slug);
    $subs = rows('SELECT * FROM forums WHERE parent_id = ? ORDER BY position', [$forum['id']]);
    $sub = null;
    if ($subSlug !== null) {
        foreach ($subs as $s) if ($s['slug'] === $subSlug) $sub = $s;
        if (!$sub) abort(404);
    }

    $order = $_GET['orden'] ?? 'recientes';
    $where = 't.deleted_at IS NULL AND ' . ($sub ? 'f.id = ?' : '(f.id = ? OR f.parent_id = ?)');
    $params = $sub ? [$sub['id']] : [$forum['id'], $forum['id']];
    if ($order === 'sin-respuesta') $where .= ' AND t.reply_count = 0';
    $orderBy = $order === 'utiles' ? 't.helpful_count DESC, t.last_post_at DESC' : 't.pinned DESC, t.last_post_at DESC';

    $pg = paginate((int) val('SELECT COUNT(*) ' . THREAD_FROM . " WHERE {$where}", $params), PER_PAGE);
    $threads = rows('SELECT ' . THREAD_COLS . ' ' . THREAD_FROM . " WHERE {$where} ORDER BY {$orderBy} LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);

    view('forum/show', [
        'title' => $sub ? $sub['name'] . ' · ' . $forum['name'] : $forum['name'],
        'description' => $forum['description'],
        'active' => $forum['pro_only'] ? 'pros' : 'foros',
        'forum' => $forum, 'subs' => $subs, 'sub' => $sub, 'threads' => $threads, 'pg' => $pg, 'order' => $order,
    ]);
}

function thread_new(string $slug): void
{
    require_login();
    $forum = find_forum($slug);
    $subs = rows('SELECT * FROM forums WHERE parent_id = ? ORDER BY position', [$forum['id']]);
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = post('title'); $body = post('body');
        $tone = array_key_exists(post('tone'), TONES) ? post('tone') : 'serio';
        $subId = (int) post('sub');
        $target = $subs ? null : $forum;
        foreach ($subs as $s) if ((int) $s['id'] === $subId) $target = $s;

        if (!$target) $errors[] = 'Elige un subforo para tu hilo.';
        if (mb_strlen($title) < 5 || mb_strlen($title) > 140) $errors[] = 'El título debe tener entre 5 y 140 caracteres.';
        if (mb_strlen($body) < 10) $errors[] = 'El mensaje es muy corto. Escribe al menos una frase.';
        if (mb_strlen($body) > 10000) $errors[] = 'El mensaje es demasiado largo. El máximo son 10.000 caracteres.';
        if (!$errors && !throttle('thread:' . uid(), 5, 600)) $errors[] = 'Has abierto varios hilos seguidos. Espera unos minutos antes de abrir otro.';

        if (!$errors) {
            $tid = insert('INSERT INTO threads (forum_id, user_id, title, slug, tone) VALUES (?,?,?,?,?)', [$target['id'], uid(), $title, slugify($title), $tone]);
            q('INSERT INTO posts (thread_id, user_id, body, tone, is_first) VALUES (?,?,?,?,1)', [$tid, uid(), $body, $tone]);
            flash('ok', 'Tu hilo está publicado.');
            redirect('/hilo/' . $tid . '-' . slugify($title));
        }
    }
    view('forum/new', ['title' => 'Abrir un hilo nuevo', 'active' => 'foros', 'forum' => $forum, 'subs' => $subs, 'errors' => $errors, 'preSub' => $_GET['sub'] ?? '']);
}

function thread_show(string $id): void
{
    $t = find_thread((int) $id);
    $author = row('SELECT u.username, u.display_name, u.avatar_type, u.avatar_value FROM users u WHERE u.id = ?', [$t['user_id']]);

    $pg = paginate((int) val('SELECT COUNT(*) FROM posts WHERE thread_id = ? AND deleted_at IS NULL', [$t['id']]), PER_PAGE);
    $posts = rows('SELECT p.*, u.username, u.display_name, u.avatar_type, u.avatar_value, u.identities, u.role, ' . PRO_ACTIVE . ' AS is_pro, pr.profession, pr.city AS pro_city
                   FROM posts p JOIN users u ON u.id = p.user_id LEFT JOIN professionals pr ON pr.user_id = u.id
                   WHERE p.thread_id = ? AND p.deleted_at IS NULL ORDER BY p.created_at, p.id LIMIT ' . $pg['per'] . ' OFFSET ' . $pg['offset'], [$t['id']]);

    $counts = []; $mine = [];
    if ($posts) {
        $ids = implode(',', array_map(fn ($p) => (int) $p['id'], $posts));
        foreach (rows("SELECT post_id, kind, COUNT(*) AS c FROM reactions WHERE post_id IN ({$ids}) GROUP BY post_id, kind") as $r) $counts[$r['post_id']][$r['kind']] = (int) $r['c'];
        if (uid()) foreach (rows("SELECT post_id, kind FROM reactions WHERE user_id = ? AND post_id IN ({$ids})", [uid()]) as $r) $mine[$r['post_id']][$r['kind']] = true;
    }

    $similar = rows('SELECT t.id, t.title, t.slug FROM threads t WHERE t.forum_id = ? AND t.id <> ? AND t.deleted_at IS NULL ORDER BY t.last_post_at DESC LIMIT 4', [$t['forum_id'], $t['id']]);
    $saved = uid() ? (bool) val('SELECT 1 FROM bookmarks WHERE user_id = ? AND thread_id = ?', [uid(), $t['id']]) : false;

    view('forum/thread', [
        'title' => $t['title'], 'active' => $t['pro_only'] ? 'pros' : 'foros',
        't' => $t, 'author' => $author, 'posts' => $posts, 'pg' => $pg, 'counts' => $counts, 'mine' => $mine, 'similar' => $similar, 'saved' => $saved,
    ]);
}

function thread_reply(string $id): void
{
    require_login();
    $t = find_thread((int) $id);
    if ($t['locked'] && !is_mod()) { flash('error', 'Este hilo está cerrado. No admite más respuestas.'); redirect('/hilo/' . $t['id']); }
    $body = post('body');
    $tone = array_key_exists(post('tone'), TONES) ? post('tone') : 'serio';
    if (mb_strlen($body) < 2 || mb_strlen($body) > 10000) {
        flash('error', 'Escribe tu respuesta antes de publicarla. El máximo son 10.000 caracteres.');
        redirect('/hilo/' . $t['id'] . '#responder');
    }
    if (!throttle('reply:' . uid(), 15, 600)) { flash('error', 'Has escrito muchas respuestas seguidas. Espera unos minutos.'); redirect('/hilo/' . $t['id']); }

    $pid = insert('INSERT INTO posts (thread_id, user_id, body, tone) VALUES (?,?,?,?)', [$t['id'], uid(), $body, $tone]);
    q('UPDATE threads SET reply_count = reply_count + 1, last_post_at = NOW() WHERE id = ?', [$t['id']]);
    $total = (int) val('SELECT COUNT(*) FROM posts WHERE thread_id = ? AND deleted_at IS NULL', [$t['id']]);
    $page = (int) ceil($total / PER_PAGE);
    flash('ok', 'Tu respuesta está publicada.');
    redirect('/hilo/' . $t['id'] . '-' . $t['slug'] . ($page > 1 ? '?p=' . $page : '') . '#post-' . $pid);
}

function find_post(int $id): array
{
    $p = row('SELECT p.*, t.title, t.slug, t.forum_id FROM posts p JOIN threads t ON t.id = p.thread_id WHERE p.id = ? AND p.deleted_at IS NULL AND t.deleted_at IS NULL', [$id]);
    if (!$p) abort(404);
    find_thread((int) $p['thread_id']); // comprueba permisos del foro
    return $p;
}

function post_react(string $id): void
{
    require_login();
    $p = find_post((int) $id);
    $kind = post('kind');
    if (!isset(REACTIONS[$kind])) abort(404);
    if ((int) $p['user_id'] !== uid()) {
        if (val('SELECT 1 FROM reactions WHERE post_id = ? AND user_id = ? AND kind = ?', [$p['id'], uid(), $kind])) {
            q('DELETE FROM reactions WHERE post_id = ? AND user_id = ? AND kind = ?', [$p['id'], uid(), $kind]);
        } else {
            q('INSERT INTO reactions (post_id, user_id, kind) VALUES (?,?,?)', [$p['id'], uid(), $kind]);
        }
        if ($kind === 'ayuda') {
            q("UPDATE threads SET helpful_count = (SELECT COUNT(*) FROM reactions r JOIN posts p ON p.id = r.post_id WHERE p.thread_id = ? AND r.kind = 'ayuda') WHERE id = ?", [$p['thread_id'], $p['thread_id']]);
        }
    } else {
        flash('info', 'No puedes reaccionar a tu propio mensaje.');
    }
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $q = parse_url($ref, PHP_URL_QUERY);
    redirect('/hilo/' . $p['thread_id'] . '-' . $p['slug'] . ($q ? '?' . $q : '') . '#post-' . $p['id']);
}

function post_edit(string $id): void
{
    require_login();
    $p = find_post((int) $id);
    if ((int) $p['user_id'] !== uid() && !is_mod()) abort(403);
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $body = post('body'); $title = post('title');
        if (mb_strlen($body) < 2 || mb_strlen($body) > 10000) $errors[] = 'El mensaje no puede estar vacío. El máximo son 10.000 caracteres.';
        if ($p['is_first'] && (mb_strlen($title) < 5 || mb_strlen($title) > 140)) $errors[] = 'El título debe tener entre 5 y 140 caracteres.';
        if (!$errors) {
            q('UPDATE posts SET body = ?, edited_at = NOW() WHERE id = ?', [$body, $p['id']]);
            if ($p['is_first']) q('UPDATE threads SET title = ? WHERE id = ?', [$title, $p['thread_id']]);
            flash('ok', 'Cambios guardados.');
            redirect('/hilo/' . $p['thread_id'] . '-' . $p['slug'] . '#post-' . $p['id']);
        }
    }
    view('forum/edit', ['title' => 'Editar mensaje', 'active' => 'foros', 'p' => $p, 'errors' => $errors]);
}

function post_delete(string $id): void
{
    require_login();
    $p = find_post((int) $id);
    if ((int) $p['user_id'] !== uid() && !is_mod()) abort(403);
    if ($p['is_first']) {
        q('UPDATE threads SET deleted_at = NOW() WHERE id = ?', [$p['thread_id']]);
        flash('ok', 'El hilo se ha borrado.');
        redirect('/');
    }
    q('UPDATE posts SET deleted_at = NOW() WHERE id = ?', [$p['id']]);
    q('UPDATE threads SET reply_count = GREATEST(reply_count - 1, 0) WHERE id = ?', [$p['thread_id']]);
    flash('ok', 'El mensaje se ha borrado.');
    redirect('/hilo/' . $p['thread_id'] . '-' . $p['slug']);
}

function thread_bookmark(string $id): void
{
    require_login();
    $t = find_thread((int) $id);
    if (val('SELECT 1 FROM bookmarks WHERE user_id = ? AND thread_id = ?', [uid(), $t['id']])) {
        q('DELETE FROM bookmarks WHERE user_id = ? AND thread_id = ?', [uid(), $t['id']]);
        flash('info', 'Hilo quitado de tus guardados.');
    } else {
        q('INSERT INTO bookmarks (user_id, thread_id) VALUES (?,?)', [uid(), $t['id']]);
        flash('ok', 'Hilo guardado. Lo encuentras en «Mi perfil → Guardados».');
    }
    redirect('/hilo/' . $t['id'] . '-' . $t['slug']);
}

function thread_moderate(string $id): void
{
    require_mod();
    $t = find_thread((int) $id);
    switch (post('do')) {
        case 'pin':     q('UPDATE threads SET pinned = 1 - pinned WHERE id = ?', [$t['id']]); break;
        case 'lock':    q('UPDATE threads SET locked = 1 - locked WHERE id = ?', [$t['id']]); break;
        case 'summary': q('UPDATE threads SET summary = ? WHERE id = ?', [post('summary') ?: null, $t['id']]); break;
    }
    flash('ok', 'Hilo actualizado.');
    redirect('/hilo/' . $t['id'] . '-' . $t['slug']);
}

function report_create(): void
{
    require_login();
    $type = post('type'); $target = (int) post('id');
    if (!in_array($type, ['post', 'user', 'presentation', 'message'], true) || $target < 1) abort(404);
    if (throttle('report:' . uid(), 10, 3600)) {
        q('INSERT INTO reports (reporter_id, target_type, target_id, reason) VALUES (?,?,?,?)', [uid(), $type, $target, mb_substr(post('reason'), 0, 500)]);
    }
    flash('ok', 'Aviso enviado. Una persona del equipo de moderación lo va a revisar.');
    back('/');
}
