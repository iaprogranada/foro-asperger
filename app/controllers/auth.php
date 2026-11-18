<?php
/** Registro, acceso y recuperación de contraseña. */

function auth_register(): void
{
    if (user()) redirect('/perfil');
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = post('username'); $email = strtolower(post('email')); $pass = (string) ($_POST['password'] ?? '');
        $who = array_key_exists(post('who'), WHO) ? post('who') : 'nd';

        if (!throttle('reg:' . client_ip(), 6, 3600)) $errors[] = 'Has creado varias cuentas seguidas. Espera una hora y vuelve a intentarlo.';
        if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) $errors[] = 'El nombre de usuario debe tener entre 3 y 30 caracteres. Puedes usar letras sin tilde, números, punto, guion y guion bajo.';
        elseif (val('SELECT 1 FROM users WHERE username = ?', [$username])) $errors[] = 'Ese nombre de usuario ya existe. Elige otro.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Escribe un correo electrónico válido. Ejemplo: nombre@correo.com';
        elseif (val('SELECT 1 FROM users WHERE email = ?', [$email])) $errors[] = 'Ya hay una cuenta con ese correo. Puedes entrar o recuperar la contraseña.';
        if (strlen($pass) < 8) $errors[] = 'La contraseña debe tener 8 caracteres como mínimo.';
        if (empty($_POST['normas'])) $errors[] = 'Para crear la cuenta tienes que aceptar las normas de la comunidad.';

        if (!$errors) {
            $avatar = AVATAR_ICONS[array_rand(AVATAR_ICONS)] . ':' . COLORS[array_rand(COLORS)];
            $id = insert('INSERT INTO users (username, email, password_hash, display_name, who, avatar_value) VALUES (?,?,?,?,?,?)',
                [$username, $email, password_hash($pass, PASSWORD_DEFAULT), $username, $who, $avatar]);
            session_regenerate_id(true);
            $_SESSION['uid'] = $id;
            flash('ok', 'Tu cuenta está creada. Ahora puedes elegir tu foto y escribir tu biografía.');
            redirect($who === 'pro' ? '/profesionales/alta' : '/perfil?paso=2');
        }
    }
    view('auth/register', ['title' => 'Crear cuenta', 'errors' => $errors]);
}

function auth_login(): void
{
    if (user()) redirect('/perfil');
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $login = strtolower(post('login')); $pass = (string) ($_POST['password'] ?? '');
        if (!throttle('login:' . client_ip(), 12, 900)) {
            $errors[] = 'Demasiados intentos. Espera 15 minutos y vuelve a probar.';
        } else {
            $u = row('SELECT * FROM users WHERE email = ? OR LOWER(username) = ?', [$login, $login]);
            if (!$u || !password_verify($pass, $u['password_hash'])) {
                $errors[] = 'El usuario o la contraseña no coinciden. Revisa los dos datos.';
            } elseif ($u['banned_at']) {
                $errors[] = 'Esta cuenta está suspendida. Si crees que es un error, escribe a moderación desde la página de contacto.';
            } else {
                session_regenerate_id(true);
                $_SESSION['uid'] = (int) $u['id'];
                $to = $_SESSION['after_login'] ?? '/';
                unset($_SESSION['after_login']);
                flash('ok', 'Hola, ' . ($u['display_name'] ?: $u['username']) . '. Has entrado en tu cuenta.');
                redirect($to);
            }
        }
    }
    view('auth/login', ['title' => 'Entrar', 'errors' => $errors]);
}

function auth_logout(): void
{
    unset($_SESSION['uid']);
    session_regenerate_id(true);
    flash('info', 'Has cerrado la sesión.');
    redirect('/');
}

function auth_forgot(): void
{
    $sent = false;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = strtolower(post('email'));
        if (throttle('forgot:' . client_ip(), 5, 3600) && ($u = row('SELECT id, email, display_name FROM users WHERE email = ?', [$email]))) {
            $token = bin2hex(random_bytes(32));
            q('REPLACE INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 2 HOUR))', [$u['id'], hash('sha256', $token)]);
            send_mail($u['email'], 'Cambiar tu contraseña de Foro Asperger',
                "Hola, {$u['display_name']}.\n\nPara elegir una contraseña nueva, abre este enlace:\n" . abs_url('/restablecer/' . $token)
                . "\n\nEl enlace funciona durante 2 horas.\nSi no lo has pedido tú, no hagas nada: tu contraseña sigue igual.\n");
        }
        $sent = true; // Siempre la misma respuesta: no revelamos si el correo existe.
    }
    view('auth/forgot', ['title' => 'Recuperar contraseña', 'sent' => $sent]);
}

function auth_reset(string $token): void
{
    $r = row('SELECT user_id FROM password_resets WHERE token_hash = ? AND expires_at > NOW()', [hash('sha256', $token)]);
    if (!$r) abort(404, 'Este enlace ya no funciona. Pide uno nuevo desde «Recuperar contraseña».');
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pass = (string) ($_POST['password'] ?? '');
        if (strlen($pass) < 8) $errors[] = 'La contraseña debe tener 8 caracteres como mínimo.';
        if (!$errors) {
            q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($pass, PASSWORD_DEFAULT), $r['user_id']]);
            q('DELETE FROM password_resets WHERE user_id = ?', [$r['user_id']]);
            flash('ok', 'Contraseña cambiada. Ya puedes entrar con la nueva.');
            redirect('/entrar');
        }
    }
    view('auth/reset', ['title' => 'Nueva contraseña', 'errors' => $errors]);
}
