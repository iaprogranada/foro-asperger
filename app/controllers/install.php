<?php
/** Instalador web: crea config.php, las tablas, los foros y la cuenta de administración. */

function install_run(): void
{
    $hasConfig = !empty($GLOBALS['config']);
    $installed = false;
    if ($hasConfig) {
        try { $installed = (int) val("SELECT COUNT(*) FROM users WHERE role = 'admin'") > 0; } catch (Throwable $e) { $installed = false; }
    }
    if ($installed) {
        flash('info', 'La web ya está instalada.');
        redirect('/');
    }

    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = post('username'); $email = post('email'); $pass = (string) ($_POST['password'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) $errors[] = 'El nombre de usuario debe tener entre 3 y 30 letras o números, sin espacios.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'El correo electrónico no es válido.';
        if (strlen($pass) < 8) $errors[] = 'La contraseña debe tener 8 caracteres como mínimo.';

        if (!$hasConfig) {
            $config = require ROOT . '/config.sample.php';
            $config['base_url'] = rtrim(post('base_url'), '/');
            $config['mail_from'] = $email;
            $config['db'] = ['host' => post('db_host', 'localhost'), 'port' => (int) post('db_port', '3306'), 'name' => post('db_name'), 'user' => post('db_user'), 'pass' => (string) ($_POST['db_pass'] ?? '')];
            $GLOBALS['config'] = $config;
        }

        if (!$errors) {
            try { db(); } catch (Throwable $e) {
                $errors[] = 'No se pudo conectar con la base de datos. Revisa el nombre, el usuario y la contraseña.';
                if (!$hasConfig) $GLOBALS['config'] = [];
            }
        }

        if (!$errors) {
            foreach (array_filter(array_map('trim', explode(";\n", preg_replace('/^--.*$/m', '', file_get_contents(APP . '/schema.sql'))))) as $sql) {
                db()->exec($sql);
            }
            if (!$hasConfig) {
                $php = "<?php\n// Creado por el instalador el " . date('d/m/Y H:i') . ". Puedes editarlo a mano (ver config.sample.php).\nreturn " . var_export($GLOBALS['config'], true) . ";\n";
                if (@file_put_contents(ROOT . '/config.php', $php) === false) {
                    $errors[] = 'No se pudo escribir config.php. Copia config.sample.php como config.php, rellena los datos y vuelve a abrir /instalar.';
                }
            }
        }

        if (!$errors) {
            $id = insert("INSERT INTO users (username, email, password_hash, display_name, role, who, avatar_value) VALUES (?,?,?,?,'admin','nd','inf:amber')",
                [$username, strtolower($email), password_hash($pass, PASSWORD_DEFAULT), $username]);
            require APP . '/seed.php';
            seed_forums($id);
            session_regenerate_id(true);
            $_SESSION['uid'] = $id;
            flash('ok', 'Instalación terminada. Ya puedes usar el foro. Esta es tu cuenta de administración.');
            redirect('/');
        }
    }

    $scheme = (($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
    view('install', ['title' => 'Instalar', 'errors' => $errors, 'hasConfig' => $hasConfig, 'guessUrl' => $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE]);
}
