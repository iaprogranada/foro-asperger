<?php
/** Zona de profesionales: presentación, alta, pago con Stripe, directorio y fichas. */

require_once APP . '/stripe.php';

const PRO_COLS = 'pr.*, u.username, u.display_name, u.avatar_type, u.avatar_value, ' . PRO_ACTIVE . ' AS is_active';

function my_pro(): ?array { return uid() ? row('SELECT ' . PRO_COLS . ' FROM professionals pr JOIN users u ON u.id = pr.user_id WHERE pr.user_id = ?', [uid()]) : null; }

function plan_price(string $plan): string
{
    $p = trim((string) cfg('stripe.display.' . $plan, ''));
    return $p !== '' ? $p . ' €' : '';
}

function pros_landing(): void
{
    view('pros/landing', ['title' => 'Zona de profesionales', 'active' => 'pros', 'pro' => my_pro(),
        'description' => 'Da a conocer tu consulta a la comunidad neurodivergente con una ficha profesional verificada.']);
}

function pros_directory(): void
{
    $city = trim((string) ($_GET['ciudad'] ?? '')); $term = trim((string) ($_GET['q'] ?? '')); $online = !empty($_GET['online']);
    $where = PRO_ACTIVE . ' AND u.banned_at IS NULL'; $params = [];
    if ($city !== '') { $where .= ' AND pr.city LIKE ?'; $params[] = '%' . addcslashes($city, '%_\\') . '%'; }
    if ($term !== '') { $like = '%' . addcslashes($term, '%_\\') . '%'; $where .= ' AND (pr.specialties LIKE ? OR pr.profession LIKE ? OR pr.full_name LIKE ?)'; array_push($params, $like, $like, $like); }
    if ($online) $where .= ' AND pr.online = 1';
    $pg = paginate((int) val("SELECT COUNT(*) FROM professionals pr JOIN users u ON u.id = pr.user_id WHERE {$where}", $params), 18);
    $list = rows('SELECT ' . PRO_COLS . " FROM professionals pr JOIN users u ON u.id = pr.user_id WHERE {$where} ORDER BY (pr.plan <> 'basic') DESC, pr.verified_at DESC LIMIT {$pg['per']} OFFSET {$pg['offset']}", $params);
    view('pros/directory', ['title' => 'Directorio de profesionales', 'active' => 'pros', 'list' => $list, 'pg' => $pg, 'city' => $city, 'term' => $term, 'online' => $online]);
}

function pros_show(string $username): void
{
    $p = row('SELECT ' . PRO_COLS . ', u.bio AS user_bio FROM professionals pr JOIN users u ON u.id = pr.user_id WHERE u.username = ? AND u.banned_at IS NULL', [$username]);
    if (!$p || (!$p['is_active'] && uid() !== (int) $p['user_id'] && !is_mod())) abort(404);
    $resources = rows("SELECT id, title, kind FROM resources WHERE uploader_id = ? AND status = 'published' ORDER BY created_at DESC LIMIT 6", [$p['user_id']]);
    view('pros/show', ['title' => $p['full_name'] . ' · ' . $p['profession'], 'active' => 'pros', 'p' => $p, 'resources' => $resources]);
}

function pros_apply(): void
{
    $me = require_login();
    $pro = my_pro();
    $errors = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $d = [
            'full_name' => post('full_name'), 'profession' => post('profession'), 'license_no' => post('license_no'), 'city' => post('city'),
            'online' => (int) !empty($_POST['online']), 'specialties' => implode(', ', array_slice(csv_list(post('specialties')), 0, 8)),
            'bio' => post('bio'), 'website' => post('website'), 'phone' => post('phone'), 'booking_url' => post('booking_url'),
            'plan' => isset(PLANS[post('plan')]) ? post('plan') : 'basic',
        ];
        if (mb_strlen($d['full_name']) < 3 || mb_strlen($d['full_name']) > 80) $errors[] = 'Escribe tu nombre y apellidos (o el nombre del centro).';
        if (mb_strlen($d['profession']) < 3 || mb_strlen($d['profession']) > 80) $errors[] = 'Escribe tu profesión. Ejemplo: Psicóloga sanitaria.';
        if (mb_strlen($d['license_no']) < 3 || mb_strlen($d['license_no']) > 40) $errors[] = 'Escribe tu número de colegiación. Lo necesitamos para verificarte.';
        if (mb_strlen($d['city']) < 2 || mb_strlen($d['city']) > 60) $errors[] = 'Escribe la ciudad donde atiendes.';
        if (mb_strlen($d['bio']) > 600) $errors[] = 'La descripción tiene más de 600 caracteres.';
        foreach (['website' => 'La dirección de tu web', 'booking_url' => 'El enlace para pedir cita'] as $k => $label) {
            if ($d[$k] !== '' && (!filter_var($d[$k], FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $d[$k]) || mb_strlen($d[$k]) > 190)) $errors[] = $label . ' debe empezar por https://';
        }
        if (mb_strlen($d['phone']) > 30) $errors[] = 'El teléfono es demasiado largo.';

        if (!$errors) {
            // Si cambia el nombre o el número de colegiación, hay que verificar de nuevo.
            $keepVerified = $pro && $pro['license_no'] === $d['license_no'] && $pro['full_name'] === $d['full_name'];
            if ($pro) {
                q('UPDATE professionals SET full_name=?, profession=?, license_no=?, city=?, online=?, specialties=?, bio=?, website=?, phone=?, booking_url=?, plan=?, updated_at=NOW()' . ($keepVerified ? '' : ', verified_at=NULL') . ' WHERE user_id=?',
                    [...array_values($d), $me['id']]);
            } else {
                q('INSERT INTO professionals (full_name, profession, license_no, city, online, specialties, bio, website, phone, booking_url, plan, user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)', [...array_values($d), $me['id']]);
                q("UPDATE users SET who = 'pro' WHERE id = ?", [$me['id']]);
            }
            flash('ok', 'Ficha guardada. Mira abajo los pasos que faltan.');
            redirect('/profesionales/alta#estado');
        }
        $pro = array_merge($pro ?? ['verified_at' => null, 'is_active' => 0, 'sub_status' => '', 'manual_active' => 0, 'stripe_customer_id' => null, 'current_period_end' => null], $d);
    }
    view('pros/apply', ['title' => 'Mi ficha profesional', 'active' => 'pros', 'pro' => $pro, 'errors' => $errors]);
}

function pros_checkout(): void
{
    $me = require_login();
    $pro = my_pro();
    if (!$pro) redirect('/profesionales/alta');
    $price = stripe_price($pro['plan']);
    if (!stripe_ready() || $price === '') { flash('info', 'El pago online todavía no está activado. El equipo del foro te escribirá por correo.'); redirect('/profesionales/alta#estado'); }
    if (in_array($pro['sub_status'], ['active', 'trialing'], true)) { flash('info', 'Tu suscripción ya está activa.'); redirect('/profesionales/alta#estado'); }

    $params = [
        'mode' => 'subscription',
        'line_items' => [['price' => $price, 'quantity' => 1]],
        'success_url' => abs_url('/profesionales/gracias'),
        'cancel_url' => abs_url('/profesionales/alta#estado'),
        'client_reference_id' => (string) $me['id'],
        'metadata' => ['user_id' => (string) $me['id'], 'plan' => $pro['plan']],
        'subscription_data' => ['metadata' => ['user_id' => (string) $me['id'], 'plan' => $pro['plan']]],
        'locale' => 'es',
    ];
    if ($pro['stripe_customer_id']) $params['customer'] = $pro['stripe_customer_id']; else $params['customer_email'] = $me['email'];
    try {
        $session = stripe_api('POST', 'checkout/sessions', $params);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        flash('error', 'No se pudo abrir la página de pago. Inténtalo dentro de unos minutos.');
        redirect('/profesionales/alta#estado');
    }
    redirect($session['url']);
}

function pros_portal(): void
{
    require_login();
    $pro = my_pro();
    if (!$pro || !$pro['stripe_customer_id'] || !stripe_ready()) redirect('/profesionales/alta#estado');
    try {
        $s = stripe_api('POST', 'billing_portal/sessions', ['customer' => $pro['stripe_customer_id'], 'return_url' => abs_url('/profesionales/alta#estado')]);
    } catch (Throwable $e) {
        error_log($e->getMessage());
        flash('error', 'No se pudo abrir la página de facturación. Inténtalo dentro de unos minutos.');
        redirect('/profesionales/alta#estado');
    }
    redirect($s['url']);
}

function pros_thanks(): void
{
    require_login();
    view('pros/thanks', ['title' => 'Pago recibido', 'active' => 'pros', 'pro' => my_pro()]);
}

/** Stripe avisa aquí de pagos, renovaciones y bajas. No lleva sesión ni CSRF: se valida con la firma. */
function stripe_webhook(): void
{
    $payload = (string) file_get_contents('php://input');
    if (!stripe_verify($payload, $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '', (string) cfg('stripe.webhook_secret', ''))) {
        http_response_code(400);
        exit('firma no valida');
    }
    $event = json_decode($payload, true) ?: [];
    $o = $event['data']['object'] ?? [];
    switch ($event['type'] ?? '') {
        case 'checkout.session.completed':
            $userId = (int) ($o['client_reference_id'] ?? ($o['metadata']['user_id'] ?? 0));
            if ($userId && ($o['mode'] ?? '') === 'subscription') {
                $status = 'active'; $end = null;
                try { $sub = stripe_api('GET', 'subscriptions/' . $o['subscription']); $status = $sub['status']; $end = stripe_period_end($sub); } catch (Throwable $e) { error_log($e->getMessage()); }
                q('UPDATE professionals SET stripe_customer_id = ?, stripe_subscription_id = ?, sub_status = ?, current_period_end = ? WHERE user_id = ?',
                    [$o['customer'] ?? null, $o['subscription'] ?? null, $status, $end, $userId]);
            }
            break;
        case 'customer.subscription.created':
        case 'customer.subscription.updated':
        case 'customer.subscription.deleted':
            $status = ($event['type'] === 'customer.subscription.deleted') ? 'canceled' : (string) ($o['status'] ?? '');
            $n = q('UPDATE professionals SET sub_status = ?, current_period_end = ? WHERE stripe_subscription_id = ?', [$status, stripe_period_end($o), $o['id'] ?? ''])->rowCount();
            if (!$n && !empty($o['metadata']['user_id']) && $status !== 'canceled') {
                q('UPDATE professionals SET stripe_customer_id = ?, stripe_subscription_id = ?, sub_status = ?, current_period_end = ? WHERE user_id = ?',
                    [$o['customer'] ?? null, $o['id'], $status, stripe_period_end($o), (int) $o['metadata']['user_id']]);
            }
            break;
    }
    header('Content-Type: application/json');
    echo '{"received":true}';
}
