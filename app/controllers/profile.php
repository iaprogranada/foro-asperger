<?php
/** Zona privada: perfil, mis hilos, guardados, cuenta. Y el perfil público. */

const AVATAR_CHOICES = ['inf:mint', 'planet:lav', 'leaf:green', 'spark:sky', 'headphones:amber', 'star:coral', 'heart:pink', 'tree:sand', 'bolt:sky', 'key:green'];

function profile_edit(): void
{
    $me = require_login();
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = post('display_name'); $bio = post('bio');
        if (mb_strlen($name) < 1 || mb_strlen($name) > 40) $errors[] = 'El nombre debe tener entre 1 y 40 caracteres.';
        if (mb_strlen($bio) > 300) $errors[] = 'La biografía tiene más de 300 caracteres. Acórtala un poco.';

        $ids = array_values(array_intersect(array_keys(IDENTITIES), (array) ($_POST['identities'] ?? [])));
        $type = $me['avatar_type']; $value = $me['avatar_value'];

        if (!empty($_FILES['photo']['name'])) {
            $file = save_avatar($_FILES['photo']);
            if (!$file) {
                $errors[] = 'No se pudo usar esa foto. Tiene que ser una imagen JPG, PNG o WebP de menos de 5 MB.';
            } else {
                if ($type === 'photo') @unlink(ROOT . '/uploads/avatars/' . basename($value));
                $type = 'photo'; $value = $file;
            }
        } elseif (in_array(post('avatar'), AVATAR_CHOICES, true)) {
            if ($type === 'photo') @unlink(ROOT . '/uploads/avatars/' . basename($value));
            $type = 'icon'; $value = post('avatar');
        }

        if (!$errors) {
            q('UPDATE users SET display_name = ?, bio = ?, identities = ?, avatar_type = ?, avatar_value = ?, pref_calm = ?, pref_noanim = ?, pref_bigtext = ?, pref_hidecounts = ? WHERE id = ?',
                [$name, $bio, implode(',', $ids), $type, $value, (int) !empty($_POST['pref_calm']), (int) !empty($_POST['pref_noanim']), (int) !empty($_POST['pref_bigtext']), (int) !empty($_POST['pref_hidecounts']), $me['id']]);
            setcookie('calma', !empty($_POST['pref_calm']) ? '1' : '0', ['expires' => time() + 31536000, 'path' => '/', 'samesite' => 'Lax']);
            flash('ok', 'Cambios guardados.');
            redirect(post('paso') === '2' ? '/#foros' : '/perfil');
        }
        $me = array_merge($me, ['display_name' => $name, 'bio' => $bio, 'identities' => implode(',', $ids)]);
    }
    view('profile/edit', ['title' => 'Mi perfil', 'active' => 'perfil', 'tab' => 'perfil', 'me' => $me, 'errors' => $errors, 'step2' => ($_GET['paso'] ?? '') === '2']);
}

function profile_threads(): void
{
    $me = require_login();
    $pg = paginate((int) val('SELECT COUNT(*) FROM threads WHERE user_id = ? AND deleted_at IS NULL', [$me['id']]), 20);
    $threads = rows('SELECT ' . THREAD_COLS . ' ' . THREAD_FROM . " WHERE t.user_id = ? AND t.deleted_at IS NULL ORDER BY t.last_post_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", [$me['id']]);
    view('profile/list', ['title' => 'Mis hilos', 'active' => 'perfil', 'tab' => 'hilos', 'threads' => $threads, 'pg' => $pg,
        'empty' => 'Todavía no has abierto ningún hilo. Elige un foro en la portada y pulsa «Abrir un hilo nuevo».']);
}

function profile_bookmarks(): void
{
    $me = require_login();
    $pg = paginate((int) val('SELECT COUNT(*) FROM bookmarks b JOIN threads t ON t.id = b.thread_id WHERE b.user_id = ? AND t.deleted_at IS NULL', [$me['id']]), 20);
    $threads = rows('SELECT ' . THREAD_COLS . ' ' . THREAD_FROM . " JOIN bookmarks b ON b.thread_id = t.id WHERE b.user_id = ? AND t.deleted_at IS NULL ORDER BY b.created_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", [$me['id']]);
    view('profile/list', ['title' => 'Guardados', 'active' => 'perfil', 'tab' => 'guardados', 'threads' => $threads, 'pg' => $pg,
        'empty' => 'No tienes hilos guardados. Dentro de un hilo, pulsa «Guardar este hilo» para encontrarlo aquí.']);
}

function profile_account(): void
{
    $me = require_login();
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $current = (string) ($_POST['current'] ?? '');
        if (!password_verify($current, $me['password_hash'])) {
            $errors[] = 'La contraseña actual no es correcta.';
        } elseif (post('do') === 'password') {
            $new = (string) ($_POST['password'] ?? '');
            if (strlen($new) < 8) $errors[] = 'La contraseña nueva debe tener 8 caracteres como mínimo.';
            else { q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $me['id']]); flash('ok', 'Contraseña cambiada.'); redirect('/perfil/cuenta'); }
        } elseif (post('do') === 'email') {
            $email = strtolower(post('email'));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Escribe un correo electrónico válido.';
            elseif (val('SELECT 1 FROM users WHERE email = ? AND id <> ?', [$email, $me['id']])) $errors[] = 'Ese correo ya lo usa otra cuenta.';
            else { q('UPDATE users SET email = ? WHERE id = ?', [$email, $me['id']]); flash('ok', 'Correo cambiado.'); redirect('/perfil/cuenta'); }
        } elseif (post('do') === 'delete') {
            if (is_admin()) {
                $errors[] = 'La cuenta de administración no se puede borrar desde aquí.';
            } else {
                if ($me['avatar_type'] === 'photo') @unlink(ROOT . '/uploads/avatars/' . basename($me['avatar_value']));
                q('DELETE FROM users WHERE id = ?', [$me['id']]);
                unset($_SESSION['uid']);
                flash('info', 'Tu cuenta y todos tus datos se han borrado.');
                redirect('/');
            }
        }
    }
    view('profile/account', ['title' => 'Cuenta y privacidad', 'active' => 'perfil', 'tab' => 'cuenta', 'me' => $me, 'errors' => $errors]);
}

function profile_public(string $username): void
{
    $u = row('SELECT u.*, ' . PRO_ACTIVE . ' AS is_pro, pr.profession FROM users u LEFT JOIN professionals pr ON pr.user_id = u.id WHERE u.username = ? AND u.banned_at IS NULL', [$username]);
    if (!$u) abort(404);
    $proOk = (is_pro() || is_mod()) ? '' : ' AND f.pro_only = 0';
    $threads = rows('SELECT ' . THREAD_COLS . ' ' . THREAD_FROM . " WHERE t.user_id = ? AND t.deleted_at IS NULL{$proOk} ORDER BY t.last_post_at DESC LIMIT 10", [$u['id']]);
    $blocked = uid() ? (bool) val('SELECT 1 FROM blocks WHERE blocker_id = ? AND blocked_id = ?', [uid(), $u['id']]) : false;
    view('profile/public', ['title' => $u['display_name'], 'u' => $u, 'threads' => $threads, 'blocked' => $blocked]);
}
