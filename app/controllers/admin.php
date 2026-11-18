<?php
/** Panel de administración y moderación. */

function admin_index(): void
{
    require_mod();
    $tab = in_array($_GET['tab'] ?? '', ['avisos', 'profesionales', 'recursos', 'usuarios'], true) ? $_GET['tab'] : 'avisos';
    $data = ['title' => 'Administración', 'tab' => $tab, 'active' => 'perfil'];
    $data['counts'] = [
        'avisos' => (int) val("SELECT COUNT(*) FROM reports WHERE status = 'open'"),
        'profesionales' => (int) val('SELECT COUNT(*) FROM professionals WHERE verified_at IS NULL'),
        'recursos' => (int) val("SELECT COUNT(*) FROM resources WHERE status = 'pending'"),
        'usuarios' => (int) val('SELECT COUNT(*) FROM users'),
    ];

    if ($tab === 'avisos') {
        $reports = rows("SELECT r.*, u.username AS reporter FROM reports r JOIN users u ON u.id = r.reporter_id WHERE r.status = 'open' ORDER BY r.created_at DESC LIMIT 100");
        foreach ($reports as &$r) {
            if ($r['target_type'] === 'post') {
                $p = row('SELECT p.id, p.body, p.thread_id, p.deleted_at, t.slug, u.username FROM posts p JOIN threads t ON t.id = p.thread_id JOIN users u ON u.id = p.user_id WHERE p.id = ?', [$r['target_id']]);
                $r['what'] = $p ? 'Mensaje de ' . $p['username'] . ($p['deleted_at'] ? ' (ya borrado)' : '') . ': «' . mb_strimwidth($p['body'], 0, 160, '…') . '»' : 'Mensaje que ya no existe';
                $r['link'] = $p ? '/hilo/' . $p['thread_id'] . '-' . $p['slug'] . '#post-' . $p['id'] : '';
                $r['post_id'] = $p && !$p['deleted_at'] ? $p['id'] : 0;
                $r['user'] = $p['username'] ?? '';
            } else {
                $u = row('SELECT username FROM users WHERE id = ?', [$r['target_id']]);
                $labels = ['user' => 'Persona', 'presentation' => 'Presentación de', 'message' => 'Mensajes privados de'];
                $r['what'] = $labels[$r['target_type']] . ' ' . ($u['username'] ?? 'una cuenta borrada');
                $r['link'] = $u ? '/u/' . $u['username'] : '';
                $r['post_id'] = 0;
                $r['user'] = $u['username'] ?? '';
            }
        }
        $data['reports'] = $reports;
    } elseif ($tab === 'profesionales') {
        $data['pros'] = rows('SELECT pr.*, u.username, u.email, ' . PRO_ACTIVE . ' AS is_active FROM professionals pr JOIN users u ON u.id = pr.user_id ORDER BY (pr.verified_at IS NULL) DESC, pr.created_at DESC LIMIT 200');
    } elseif ($tab === 'recursos') {
        $data['resources'] = rows("SELECT r.*, u.username FROM resources r JOIN users u ON u.id = r.uploader_id ORDER BY (r.status = 'pending') DESC, r.created_at DESC LIMIT 200");
    } else {
        $term = trim((string) ($_GET['q'] ?? ''));
        $like = '%' . addcslashes($term, '%_\\') . '%';
        $data['term'] = $term;
        $data['users'] = rows('SELECT id, username, email, display_name, role, who, banned_at, created_at, last_seen_at FROM users WHERE username LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT 100', [$like, $like]);
    }
    view('admin/index', $data);
}

function admin_action(): void
{
    require_mod();
    $do = post('do'); $id = (int) post('id');
    $adminOnly = ['role', 'pro_verify', 'pro_manual'];
    if (in_array($do, $adminOnly, true) && !is_admin()) abort(403);

    switch ($do) {
        case 'report_close':
            q("UPDATE reports SET status = 'closed', resolved_by = ? WHERE id = ?", [uid(), $id]);
            flash('ok', 'Aviso cerrado.');
            break;
        case 'ban':
            $u = row('SELECT id, role, banned_at FROM users WHERE id = ?', [$id]);
            if ($u && $u['role'] !== 'admin' && (int) $u['id'] !== uid()) {
                q('UPDATE users SET banned_at = ' . ($u['banned_at'] ? 'NULL' : 'NOW()') . ' WHERE id = ?', [$id]);
                flash('ok', $u['banned_at'] ? 'Cuenta reactivada.' : 'Cuenta suspendida. Esa persona ya no puede entrar.');
            }
            break;
        case 'role':
            $role = in_array(post('role'), ['member', 'mod'], true) ? post('role') : 'member';
            q("UPDATE users SET role = ? WHERE id = ? AND role <> 'admin'", [$role, $id]);
            flash('ok', 'Permisos cambiados.');
            break;
        case 'pro_verify':
            $p = row('SELECT pr.user_id, pr.verified_at, pr.full_name, u.email FROM professionals pr JOIN users u ON u.id = pr.user_id WHERE pr.user_id = ?', [$id]);
            if ($p) {
                q('UPDATE professionals SET verified_at = ' . ($p['verified_at'] ? 'NULL' : 'NOW()') . ' WHERE user_id = ?', [$id]);
                if (!$p['verified_at']) {
                    send_mail($p['email'], 'Tu colegiación está verificada', "Hola, {$p['full_name']}.\n\nHemos comprobado tu número de colegiación.\nSi tu plan ya está pagado, tu ficha aparece en el directorio.\n\nPuedes ver el estado aquí:\n" . abs_url('/profesionales/alta') . "\n");
                }
                flash('ok', $p['verified_at'] ? 'Verificación retirada.' : 'Profesional verificado. Le hemos avisado por correo.');
            }
            break;
        case 'pro_manual':
            q('UPDATE professionals SET manual_active = 1 - manual_active WHERE user_id = ?', [$id]);
            flash('ok', 'Activación manual cambiada.');
            break;
        case 'res_publish':
            q("UPDATE resources SET status = IF(status = 'published', 'pending', 'published') WHERE id = ?", [$id]);
            flash('ok', 'Recurso actualizado.');
            break;
        case 'res_feature':
            q('UPDATE resources SET featured = 0');
            if (post('on') === '1') q("UPDATE resources SET featured = 1, status = 'published' WHERE id = ?", [$id]);
            flash('ok', 'Recurso destacado actualizado.');
            break;
        case 'res_delete':
            $r = row('SELECT file_name FROM resources WHERE id = ?', [$id]);
            if ($r) { @unlink(ROOT . '/storage/resources/' . basename($r['file_name'])); q('DELETE FROM resources WHERE id = ?', [$id]); flash('ok', 'Recurso borrado.'); }
            break;
        default:
            abort(404);
    }
    back('/admin');
}
