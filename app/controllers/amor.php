<?php
/** Amor neurodivergente: presentaciones, saludos, mensajes privados y bloqueos. */

function my_presentation(): ?array { return uid() ? row('SELECT * FROM presentations WHERE user_id = ?', [uid()]) : null; }

function amor_index(): void
{
    $looking = isset(LOOKING[$_GET['busca'] ?? '']) ? $_GET['busca'] : '';
    $city = trim((string) ($_GET['ciudad'] ?? ''));
    $ages = ['18-30' => [18, 30], '31-50' => [31, 50], '51' => [51, 120]];
    $age = isset($ages[$_GET['edad'] ?? '']) ? $_GET['edad'] : '';

    $where = 'u.banned_at IS NULL AND p.user_id <> ?'; $params = [uid()];
    if (uid()) { $where .= ' AND NOT EXISTS (SELECT 1 FROM blocks b WHERE (b.blocker_id = ? AND b.blocked_id = p.user_id) OR (b.blocker_id = p.user_id AND b.blocked_id = ?))'; array_push($params, uid(), uid()); }
    if ($looking) { $where .= ' AND p.looking = ?'; $params[] = $looking; }
    if ($city !== '') { $where .= ' AND p.city LIKE ?'; $params[] = '%' . addcslashes($city, '%_\\') . '%'; }
    if ($age) { $where .= ' AND p.age BETWEEN ? AND ?'; array_push($params, ...$ages[$age]); }

    // Las presentaciones solo se enseñan a personas con cuenta.
    $pg = paginate(uid() ? (int) val("SELECT COUNT(*) FROM presentations p JOIN users u ON u.id = p.user_id WHERE {$where}", $params) : 0, 18);
    $people = uid() ? rows("SELECT p.*, u.username, u.display_name, u.avatar_type, u.avatar_value FROM presentations p JOIN users u ON u.id = p.user_id WHERE {$where} ORDER BY p.updated_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params) : [];

    $sent = [];
    if (uid() && $people) {
        $ids = implode(',', array_map(fn ($p) => (int) $p['user_id'], $people));
        foreach (rows("SELECT from_id, to_id, status FROM greetings WHERE (from_id = ? AND to_id IN ({$ids})) OR (to_id = ? AND from_id IN ({$ids}))", [uid(), uid()]) as $g) {
            $other = (int) $g['from_id'] === uid() ? $g['to_id'] : $g['from_id'];
            $sent[$other] = $g['status'] === 'accepted' ? 'accepted' : ((int) $g['from_id'] === uid() ? 'sent' : ($g['status'] === 'pending' ? 'received' : ''));
        }
    }
    view('amor/index', ['title' => 'Amor neurodivergente', 'description' => 'Conoce a personas neurodivergentes como tú. Empecemos por la amistad.', 'active' => 'amor',
        'mine' => my_presentation(), 'people' => $people, 'pg' => $pg, 'sent' => $sent, 'looking' => $looking, 'city' => $city, 'age' => $age]);
}

function amor_save(): void
{
    $me = require_login();
    $name = post('first_name'); $age = (int) post('age'); $city = post('city'); $body = post('body');
    $looking = isset(LOOKING[post('looking')]) ? post('looking') : 'amistad';
    $comm = isset(COMM[post('comm')]) ? post('comm') : 'texto';
    $interests = array_values(array_intersect(INTERESTS, (array) ($_POST['interests'] ?? [])));
    foreach (csv_list(post('other_interests')) as $extra) if (mb_strlen($extra) <= 24) $interests[] = $extra;
    $interests = array_slice(array_unique($interests), 0, 8);

    $errors = [];
    if (mb_strlen($name) < 1 || mb_strlen($name) > 40) $errors[] = 'Escribe tu nombre (máximo 40 caracteres).';
    if ($age < 18) $errors[] = 'Esta zona es solo para personas de 18 años o más.';
    if ($age > 110) $errors[] = 'Revisa la edad que has escrito.';
    if (mb_strlen($city) < 2 || mb_strlen($city) > 60) $errors[] = 'Escribe tu ciudad.';
    if (mb_strlen($body) > 400) $errors[] = 'El texto tiene más de 400 caracteres. Acórtalo un poco.';
    if ($errors) { foreach ($errors as $er) flash('error', $er); redirect('/amor#presentarme'); }

    q('INSERT INTO presentations (user_id, first_name, age, city, looking, interests, comm, body) VALUES (?,?,?,?,?,?,?,?)
       ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), age = VALUES(age), city = VALUES(city), looking = VALUES(looking), interests = VALUES(interests), comm = VALUES(comm), body = VALUES(body), updated_at = NOW()',
        [$me['id'], $name, $age, $city, $looking, implode(',', $interests), $comm, $body]);
    flash('ok', 'Tu presentación está publicada. Puedes cambiarla o borrarla cuando quieras.');
    redirect('/amor#gente');
}

function amor_delete(): void
{
    require_login();
    q('DELETE FROM presentations WHERE user_id = ?', [uid()]);
    flash('info', 'Tu presentación se ha borrado. Ya no aparece en la lista.');
    redirect('/amor');
}

function amor_greet(string $userId): void
{
    require_login();
    $to = (int) $userId;
    if ($to === uid() || !val('SELECT 1 FROM presentations WHERE user_id = ?', [$to])) abort(404);
    if (!my_presentation()) { flash('info', 'Antes de saludar a alguien, publica tu presentación. Así la otra persona sabe quién eres.'); redirect('/amor#presentarme'); }
    if (blocked_between(uid(), $to)) abort(404);
    if (val('SELECT 1 FROM greetings WHERE (from_id = ? AND to_id = ?) OR (from_id = ? AND to_id = ?)', [uid(), $to, $to, uid()])) {
        flash('info', 'Ya hay un saludo entre vosotros. Míralo en «Mensajes».');
        redirect('/mensajes');
    }
    if (!throttle('greet:' . uid(), 20, 86400)) { flash('error', 'Hoy has enviado muchos saludos. Mañana puedes enviar más.'); redirect('/amor#gente'); }
    q('INSERT INTO greetings (from_id, to_id, message) VALUES (?,?,?)', [uid(), $to, mb_substr(post('message'), 0, 300)]);
    flash('ok', 'Saludo enviado. Si la otra persona lo acepta, podréis escribiros en «Mensajes».');
    back('/amor#gente');
}

function amor_answer(string $greetId): void
{
    require_login();
    $g = row("SELECT * FROM greetings WHERE id = ? AND to_id = ? AND status = 'pending'", [(int) $greetId, uid()]);
    if (!$g) abort(404);
    if (post('do') !== 'accept') {
        q("UPDATE greetings SET status = 'declined' WHERE id = ?", [$g['id']]);
        flash('info', 'Saludo rechazado. La otra persona no recibe ningún aviso.');
        redirect('/mensajes');
    }
    q("UPDATE greetings SET status = 'accepted' WHERE id = ?", [$g['id']]);
    $a = min(uid(), (int) $g['from_id']); $b = max(uid(), (int) $g['from_id']);
    q('INSERT IGNORE INTO conversations (user_a, user_b) VALUES (?,?)', [$a, $b]);
    $cid = (int) val('SELECT id FROM conversations WHERE user_a = ? AND user_b = ?', [$a, $b]);
    q('INSERT INTO messages (conversation_id, sender_id, body, read_at) VALUES (?,?,?,NOW())', [$cid, $g['from_id'], $g['message'] !== '' ? $g['message'] : '¡Hola!']);
    flash('ok', 'Saludo aceptado. Ya podéis escribiros.');
    redirect('/mensajes/' . $cid);
}

function user_block(string $userId): void
{
    require_login();
    $other = (int) $userId;
    if ($other === uid() || !val('SELECT 1 FROM users WHERE id = ?', [$other])) abort(404);
    if (val('SELECT 1 FROM blocks WHERE blocker_id = ? AND blocked_id = ?', [uid(), $other])) {
        q('DELETE FROM blocks WHERE blocker_id = ? AND blocked_id = ?', [uid(), $other]);
        flash('info', 'Persona desbloqueada.');
    } else {
        q('INSERT INTO blocks (blocker_id, blocked_id) VALUES (?,?)', [uid(), $other]);
        q("DELETE FROM greetings WHERE status = 'pending' AND ((from_id = ? AND to_id = ?) OR (from_id = ? AND to_id = ?))", [uid(), $other, $other, uid()]);
        flash('ok', 'Persona bloqueada. No puede saludarte ni escribirte, y no ves su presentación.');
    }
    back('/mensajes');
}

function messages_index(): void
{
    require_login();
    $pending = rows("SELECT g.*, u.username, u.display_name, u.avatar_type, u.avatar_value, p.first_name, p.age, p.city, p.looking
                     FROM greetings g JOIN users u ON u.id = g.from_id LEFT JOIN presentations p ON p.user_id = g.from_id
                     WHERE g.to_id = ? AND g.status = 'pending' AND u.banned_at IS NULL ORDER BY g.created_at DESC", [uid()]);
    $convs = rows('SELECT c.id, c.last_message_at, u.id AS other_id, u.username, u.display_name, u.avatar_type, u.avatar_value,
                          (SELECT body FROM messages m WHERE m.conversation_id = c.id ORDER BY m.id DESC LIMIT 1) AS last_body,
                          (SELECT COUNT(*) FROM messages m WHERE m.conversation_id = c.id AND m.sender_id <> ? AND m.read_at IS NULL) AS unread
                   FROM conversations c JOIN users u ON u.id = IF(c.user_a = ?, c.user_b, c.user_a)
                   WHERE c.user_a = ? OR c.user_b = ? ORDER BY c.last_message_at DESC', [uid(), uid(), uid(), uid()]);
    view('amor/messages', ['title' => 'Mensajes', 'active' => 'perfil', 'tab' => 'mensajes', 'pending' => $pending, 'convs' => $convs]);
}

function find_conversation(int $id): array
{
    $c = row('SELECT * FROM conversations WHERE id = ? AND (user_a = ? OR user_b = ?)', [$id, uid(), uid()]);
    if (!$c) abort(404);
    $c['other'] = row('SELECT id, username, display_name, avatar_type, avatar_value, banned_at FROM users WHERE id = ?', [(int) $c['user_a'] === uid() ? $c['user_b'] : $c['user_a']]);
    $c['blocked'] = blocked_between(uid(), (int) $c['other']['id']);
    return $c;
}

function messages_show(string $id): void
{
    require_login();
    $c = find_conversation((int) $id);
    q('UPDATE messages SET read_at = NOW() WHERE conversation_id = ? AND sender_id <> ? AND read_at IS NULL', [$c['id'], uid()]);
    $messages = array_reverse(rows('SELECT * FROM messages WHERE conversation_id = ? ORDER BY id DESC LIMIT 200', [$c['id']]));
    $iBlocked = (bool) val('SELECT 1 FROM blocks WHERE blocker_id = ? AND blocked_id = ?', [uid(), $c['other']['id']]);
    view('amor/conversation', ['title' => 'Conversación con ' . $c['other']['display_name'], 'active' => 'perfil', 'tab' => 'mensajes', 'c' => $c, 'messages' => $messages, 'iBlocked' => $iBlocked]);
}

function messages_send(string $id): void
{
    require_login();
    $c = find_conversation((int) $id);
    $body = post('body');
    if ($c['blocked'] || $c['other']['banned_at']) { flash('error', 'No puedes escribir en esta conversación.'); redirect('/mensajes/' . $c['id']); }
    if (mb_strlen($body) < 1 || mb_strlen($body) > 2000) { flash('error', 'Escribe un mensaje de 2.000 caracteres como máximo.'); redirect('/mensajes/' . $c['id']); }
    if (!throttle('msg:' . uid(), 60, 600)) { flash('error', 'Has enviado muchos mensajes seguidos. Espera unos minutos.'); redirect('/mensajes/' . $c['id']); }
    $mid = insert('INSERT INTO messages (conversation_id, sender_id, body) VALUES (?,?,?)', [$c['id'], uid(), $body]);
    q('UPDATE conversations SET last_message_at = NOW() WHERE id = ?', [$c['id']]);
    redirect('/mensajes/' . $c['id'] . '#m-' . $mid);
}
