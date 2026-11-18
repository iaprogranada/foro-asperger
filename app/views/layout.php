<?php
/** Plantilla común: cabecera, avisos, contenido y pie. */
$me = user();
$active = $active ?? '';
$title = $title ?? '';
$calm = $me ? (bool) $me['pref_calm'] : false;
if (isset($_COOKIE['calma'])) $calm = $_COOKIE['calma'] === '1';
$htmlClass = trim(($calm ? 'calm ' : '') . (!empty($me['pref_noanim']) ? 'noanim ' : '') . (!empty($me['pref_bigtext']) ? 'bigtext ' : '') . (!empty($me['pref_hidecounts']) ? 'hidecounts' : ''));
$nav = [
    'foros'    => ['/#foros', 'Foros', 'chat', 'mint'],
    'recursos' => ['/recursos', 'Recursos', 'doc', 'green'],
    'amor'     => ['/amor', 'Amor neurodivergente', 'heart', 'pink'],
    'pros'     => ['/profesionales', 'Profesionales', 'shield', 'amber'],
];
$site = cfg('site_name', 'Foro Asperger');
?>
<!doctype html>
<html lang="es" class="<?= e($htmlClass) ?>">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ? $title . ' · ' . $site : $site . ' · Foro sobre autismo y neurodivergencia') ?></title>
<meta name="description" content="<?= e($description ?? 'Un foro en español para personas autistas, familias y profesionales. Claro, visual y sin prisa.') ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:wght@400;700&family=Lexend:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<link rel="icon" href="<?= e(url('/assets/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body>
<a class="sr" href="#contenido">Saltar al contenido</a>
<header class="site-header">
  <div class="wrap">
    <a class="logo" href="<?= e(url('/')) ?>"><span class="mark"><?= icon('inf', 32) ?></span><span>foro<b>asperger</b></span></a>
    <?php if (!empty($GLOBALS['config'])): ?>
    <nav class="nav" aria-label="Principal">
      <?php foreach ($nav as $key => [$href, $label, $ic, $col]): ?>
        <a href="<?= e(url($href)) ?>" class="c-<?= $col ?><?= $active === $key ? ' on' : '' ?>"<?= $active === $key ? ' aria-current="page"' : '' ?>><?= icon($ic, 22) ?><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <div class="tools">
      <button type="button" class="btn btn-sm" data-calm aria-pressed="<?= $calm ? 'true' : 'false' ?>"><?= icon('moon', 20) ?><span data-calm-label><?= $calm ? 'Modo calma: activado' : 'Modo calma' ?></span></button>
      <?php if ($me): ?>
        <?php if (($n = unread_count()) > 0): ?>
          <a class="btn btn-sm" href="<?= e(url('/mensajes')) ?>"><?= icon('mail', 20) ?>Mensajes <span class="badge"><?= $n ?></span></a>
        <?php endif; ?>
        <a class="me-pill<?= $active === 'perfil' ? ' on' : '' ?>" href="<?= e(url('/perfil')) ?>"><?= avatar($me, 34) ?>Mi perfil</a>
      <?php else: ?>
        <a class="btn btn-sm btn-link" style="color:var(--teal)" href="<?= e(url('/entrar')) ?>">Entrar</a>
        <a class="btn btn-sm btn-primary" href="<?= e(url('/registro')) ?>">Crear cuenta</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
</header>

<main id="contenido">
  <?php $fl = flashes(); if ($fl): ?>
    <div class="wrap stack" style="padding-top:20px" role="status">
      <?php foreach ($fl as [$type, $msg]): ?>
        <div class="flash c-<?= $type === 'error' ? 'coral' : ($type === 'ok' ? 'mint' : 'sky') ?>"><?= icon($type === 'error' ? 'flag' : ($type === 'ok' ? 'check' : 'question'), 24) ?><span><?= e($msg) ?></span></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <?= $content ?>
</main>

<footer class="site-footer">
  <div class="wrap">
    <div class="stack" style="max-width:30em;gap:10px">
      <span class="logo"><span>foro<b>asperger</b>.com</span></span>
      <span class="small">Comunidad sobre autismo y neurodivergencia. El foro no sustituye la atención de un profesional sanitario.</span>
    </div>
    <nav aria-label="Pie de página">
      <a href="<?= e(url('/normas')) ?>">Normas de la comunidad</a>
      <a href="<?= e(url('/accesibilidad')) ?>">Accesibilidad</a>
      <a href="<?= e(url('/privacidad')) ?>">Privacidad</a>
      <a href="<?= e(url('/contacto')) ?>">Contacto</a>
      <?php if (is_mod()): ?><a href="<?= e(url('/admin')) ?>">Administración</a><?php endif; ?>
    </nav>
  </div>
</footer>
<script src="<?= e(asset('js/app.js')) ?>" defer></script>
</body>
</html>
