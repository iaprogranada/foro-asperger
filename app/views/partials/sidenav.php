<?php
/** Menú de la zona privada. Recibe $tab. */
$items = [
    'perfil'    => ['/perfil', 'Mi perfil', 'user'],
    'hilos'     => ['/perfil/hilos', 'Mis hilos', 'chat'],
    'mensajes'  => ['/mensajes', 'Mensajes', 'mail'],
    'guardados' => ['/perfil/guardados', 'Guardados', 'bookmark'],
    'amor'      => ['/amor#presentarme', 'Mi presentación', 'heart'],
    'cuenta'    => ['/perfil/cuenta', 'Cuenta y privacidad', 'lock'],
];
if (user()['who'] === 'pro' || !empty(user()['pro_plan'])) $items['pro'] = ['/profesionales/alta', 'Mi ficha profesional', 'shield'];
if (is_mod()) $items['admin'] = ['/admin', 'Administración', 'settings'];
$n = unread_count();
?>
<nav class="card sidenav" aria-label="Mi zona privada" style="flex:1 1 220px;min-width:0;padding:16px">
  <?php foreach ($items as $key => [$href, $label, $ic]): ?>
    <a href="<?= e(url($href)) ?>"<?= $tab === $key ? ' class="on" aria-current="page"' : '' ?>><?= icon($ic, 24) ?><?= e($label) ?><?php if ($key === 'mensajes' && $n): ?> <span class="badge"><?= $n ?></span><?php endif; ?></a>
  <?php endforeach; ?>
  <form method="post" action="<?= e(url('/salir')) ?>">
    <?= csrf_field() ?>
    <button class="plain" type="submit" style="display:flex;align-items:center;gap:12px;min-height:50px;padding:0 14px;border-radius:16px;color:#8A3B24;font-weight:700;width:100%"><?= icon('logout', 24) ?>Cerrar sesión</button>
  </form>
</nav>
