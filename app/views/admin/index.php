<?php
$tabs = ['avisos' => ['Avisos', 'flag'], 'profesionales' => ['Profesionales', 'shield'], 'recursos' => ['Recursos', 'doc'], 'usuarios' => ['Usuarios', 'users']];
$act = function (string $do, int $id, string $label, string $class = 'btn btn-sm', array $extra = [], string $confirm = ''): string {
    $h = '<form method="post" action="' . e(url('/admin/accion')) . '" style="display:inline"' . ($confirm ? ' data-confirm="' . e($confirm) . '"' : '') . '>' . csrf_field()
        . '<input type="hidden" name="do" value="' . $do . '"><input type="hidden" name="id" value="' . $id . '">';
    foreach ($extra as $k => $v) $h .= '<input type="hidden" name="' . e($k) . '" value="' . e($v) . '">';
    return $h . '<button class="' . $class . '" type="submit">' . e($label) . '</button></form>';
};
?>
<div class="wrap page">
  <div class="stack" style="gap:6px">
    <h1>Administración</h1>
    <p class="lead">Avisos de la comunidad, profesionales por verificar, recursos y usuarios.</p>
  </div>
  <nav class="tabs" aria-label="Secciones de administración">
    <?php foreach ($tabs as $k => [$label, $ic]): ?>
      <a class="btn btn-sm<?= $tab === $k ? ' btn-dark' : '' ?>" style="min-height:52px;padding:0 20px" href="<?= e(url('/admin?tab=' . $k)) ?>"<?= $tab === $k ? ' aria-current="page"' : '' ?>><?= icon($ic, 22) ?><?= e($label) ?> <span class="badge" style="background:<?= $counts[$k] && $k !== 'usuarios' ? '#862C68' : 'var(--field)' ?>"><?= $counts[$k] ?></span></a>
    <?php endforeach; ?>
  </nav>

  <?php if ($tab === 'avisos'): ?>
    <?php if (!$reports): ?><div class="card"><p class="lead">No hay avisos pendientes.</p></div><?php endif; ?>
    <div class="stack">
      <?php foreach ($reports as $r): ?>
        <article class="card stack" style="padding:22px;gap:10px">
          <div class="row between"><b><?= e($r['what']) ?></b><span class="muted small">Avisa <?= e($r['reporter']) ?> · <?= e(time_ago($r['created_at'])) ?></span></div>
          <?php if ($r['reason'] !== ''): ?><p class="card" style="padding:12px 16px;border-radius:14px;background:var(--ground)">Motivo: <?= e($r['reason']) ?></p><?php endif; ?>
          <div class="row">
            <?php if ($r['link']): ?><a class="btn btn-sm" href="<?= e(url($r['link'])) ?>">Ver</a><?php endif; ?>
            <?php if ($r['post_id']): ?>
              <form method="post" action="<?= e(url('/post/' . $r['post_id'] . '/borrar')) ?>" style="display:inline" data-confirm="¿Borrar el mensaje?"><?= csrf_field() ?><button class="btn btn-sm btn-danger" type="submit">Borrar el mensaje</button></form>
            <?php endif; ?>
            <?= $act('report_close', (int) $r['id'], 'Cerrar el aviso', 'btn btn-sm btn-dark') ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

  <?php elseif ($tab === 'profesionales'): ?>
    <p class="muted">Una ficha se publica cuando está <b>verificada</b> y además tiene el <b>pago activo</b> (por Stripe o activado a mano).</p>
    <?php if (!$pros): ?><div class="card"><p class="lead">Todavía no hay solicitudes de profesionales.</p></div><?php endif; ?>
    <div class="stack">
      <?php foreach ($pros as $p): ?>
        <article class="card stack" style="padding:22px;gap:10px">
          <div class="row between">
            <b style="font-family:var(--head);font-size:1.15rem"><?= e($p['full_name']) ?> · <?= e($p['profession']) ?></b>
            <span class="tag c-<?= $p['is_active'] ? 'mint' : 'amber' ?>"><?= $p['is_active'] ? 'Publicada' : 'No publicada' ?></span>
          </div>
          <div class="muted">N.º de colegiación: <b><?= e($p['license_no']) ?></b> · <?= e($p['city']) ?> · Plan: <?= e(PLANS[$p['plan']][0]) ?> · <a href="<?= e(url('/u/' . $p['username'])) ?>"><?= e($p['username']) ?></a> · <?= e($p['email']) ?></div>
          <div class="row small">
            <span class="tag c-<?= $p['verified_at'] ? 'mint' : 'coral' ?>"><?= $p['verified_at'] ? 'Verificado' : 'Sin verificar' ?></span>
            <span class="tag c-<?= in_array($p['sub_status'], ['active', 'trialing'], true) ? 'mint' : 'sand' ?>">Stripe: <?= e($p['sub_status'] ?: 'sin suscripción') ?></span>
            <?php if ($p['manual_active']): ?><span class="tag c-sky">Activado a mano</span><?php endif; ?>
          </div>
          <?php if (is_admin()): ?>
          <div class="row">
            <?= $act('pro_verify', (int) $p['user_id'], $p['verified_at'] ? 'Quitar verificación' : 'Marcar como verificado', 'btn btn-sm' . ($p['verified_at'] ? '' : ' btn-primary')) ?>
            <?= $act('pro_manual', (int) $p['user_id'], $p['manual_active'] ? 'Quitar activación manual' : 'Activar sin Stripe') ?>
            <a class="btn btn-sm" href="<?= e(url('/profesionales/ver/' . $p['username'])) ?>">Ver la ficha</a>
          </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>

  <?php elseif ($tab === 'recursos'): ?>
    <div><a class="btn btn-primary" href="<?= e(url('/recursos/subir')) ?>"><?= icon('upload', 20) ?>Publicar un recurso</a></div>
    <?php if (!$resources): ?><div class="card"><p class="lead">Todavía no hay recursos. Publica el primero.</p></div><?php endif; ?>
    <div class="stack">
      <?php foreach ($resources as $r): ?>
        <article class="card stack" style="padding:22px;gap:10px">
          <div class="row between">
            <b style="font-family:var(--head);font-size:1.1rem"><?= e($r['title']) ?></b>
            <span class="row"><?php if ($r['featured']): ?><span class="tag gold">Destacado</span><?php endif; ?><span class="tag c-<?= $r['status'] === 'published' ? 'mint' : 'amber' ?>"><?= $r['status'] === 'published' ? 'Publicado' : 'Pendiente de revisar' ?></span></span>
          </div>
          <div class="muted small"><?= e(RES_KINDS[$r['kind']][0]) ?> · <?= e(AUDIENCES[$r['audience']]) ?> · <?= e($r['original_name']) ?> (<?= e(human_size((int) $r['size'])) ?>) · subido por <?= e($r['username']) ?> · <?= (int) $r['downloads'] ?> descargas</div>
          <div class="row">
            <a class="btn btn-sm" href="<?= e(url('/recursos/' . $r['id'] . '/descargar')) ?>">Descargar para revisar</a>
            <?= $act('res_publish', (int) $r['id'], $r['status'] === 'published' ? 'Ocultar' : 'Publicar', 'btn btn-sm' . ($r['status'] === 'published' ? '' : ' btn-primary')) ?>
            <?= $act('res_feature', (int) $r['id'], $r['featured'] ? 'Quitar destacado' : 'Destacar', 'btn btn-sm', ['on' => $r['featured'] ? '0' : '1']) ?>
            <?= $act('res_delete', (int) $r['id'], 'Borrar', 'btn btn-sm btn-danger', [], '¿Borrar este recurso y su archivo?') ?>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

  <?php else: ?>
    <form class="row" style="max-width:560px;flex-wrap:nowrap;gap:8px" role="search">
      <input type="hidden" name="tab" value="usuarios">
      <label class="sr" for="q">Buscar por usuario o correo</label>
      <input id="q" name="q" type="search" value="<?= e($term) ?>" placeholder="Buscar por usuario o correo">
      <button class="btn btn-primary btn-flat" style="min-height:54px" type="submit">Buscar</button>
    </form>
    <div class="card table-box" style="padding:12px 20px">
      <table class="table">
        <thead><tr><th scope="col">Usuario</th><th scope="col">Correo</th><th scope="col">Alta</th><th scope="col">Estado</th><th scope="col">Acciones</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><a href="<?= e(url('/u/' . $u['username'])) ?>"><b><?= e($u['username']) ?></b></a></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e(date('d/m/Y', strtotime($u['created_at']))) ?></td>
            <td><?= $u['banned_at'] ? '<span class="tag c-coral">Suspendida</span>' : '<span class="tag c-' . ($u['role'] === 'member' ? 'sand' : 'mint') . '">' . ['member' => 'Miembro', 'mod' => 'Moderación', 'admin' => 'Administración'][$u['role']] . '</span>' ?></td>
            <td>
              <?php if ($u['role'] !== 'admin'): ?>
                <?= $act('ban', (int) $u['id'], $u['banned_at'] ? 'Reactivar' : 'Suspender', 'btn btn-sm' . ($u['banned_at'] ? '' : ' btn-danger'), [], $u['banned_at'] ? '' : '¿Suspender esta cuenta?') ?>
                <?php if (is_admin()): ?><?= $act('role', (int) $u['id'], $u['role'] === 'mod' ? 'Quitar moderación' : 'Hacer moderador', 'btn btn-sm', ['role' => $u['role'] === 'mod' ? 'member' : 'mod']) ?><?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
