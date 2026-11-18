<?php
$col = color(($sub ?? $forum)['color']);
$base = '/foro/' . $forum['slug'];
$here = $base . ($sub ? '/' . $sub['slug'] : '');
$newUrl = url($base . '/nuevo' . ($sub ? '?sub=' . $sub['id'] : ''));
?>
<div class="wrap page" style="padding-top:20px;gap:28px">
  <nav class="crumbs" aria-label="Ruta">
    <a href="<?= e(url('/')) ?>">Inicio</a><?= icon('chevron', 16, 'stroke-width="2.4"') ?>
    <?php if ($sub): ?>
      <a href="<?= e(url($base)) ?>"><?= e($forum['name']) ?></a><?= icon('chevron', 16, 'stroke-width="2.4"') ?><span aria-current="page"><?= e($sub['name']) ?></span>
    <?php else: ?>
      <span aria-current="page"><?= e($forum['name']) ?></span>
    <?php endif; ?>
  </nav>

  <section class="hero-band c-<?= $col ?>">
    <span class="pic lg"><?= icon(($sub ?? $forum)['icon'], 66, 'stroke-width="1.7"') ?></span>
    <div class="stack" style="flex:1 1 380px;min-width:0;gap:6px">
      <h1><?= e(($sub ?? $forum)['name']) ?></h1>
      <p style="font-size:1.15rem;max-width:34em"><?= e(($sub ?? $forum)['description']) ?>.<?= $sub ? '' : ' Elige un subforo o lee las conversaciones de abajo.' ?></p>
    </div>
    <a class="btn btn-primary" href="<?= e($newUrl) ?>"><?= icon('plus', 20, 'stroke-width="2.4"') ?>Abrir un hilo nuevo</a>
  </section>

  <div class="split">
    <div class="main stack-lg" style="gap:36px">
      <?php if ($subs): ?>
      <section class="stack">
        <h2>Subforos</h2>
        <div class="grid" style="--min:180px;gap:16px">
          <?php foreach ($subs as $s): ?>
            <a class="tile compact c-<?= color($s['color']) ?><?= $sub && $sub['id'] === $s['id'] ? ' on' : '' ?>" href="<?= e(url($base . '/' . $s['slug'])) ?>"<?= $sub && $sub['id'] === $s['id'] ? ' aria-current="page"' : '' ?>>
              <span class="pic sm"><?= icon($s['icon'], 34) ?></span><b><?= e($s['name']) ?></b><small><?= e($s['description']) ?></small>
            </a>
          <?php endforeach; ?>
        </div>
        <?php if ($sub): ?><p><a href="<?= e(url($base)) ?>"><b>Ver las conversaciones de todos los subforos</b></a></p><?php endif; ?>
      </section>
      <?php endif; ?>

      <section id="hilos" class="stack">
        <div class="row between">
          <h2>Conversaciones</h2>
          <div class="tabs" role="group" aria-label="Ordenar conversaciones">
            <?php foreach (['recientes' => 'Más recientes', 'sin-respuesta' => 'Sin respuesta', 'utiles' => 'Más útiles'] as $k => $label): ?>
              <a class="btn btn-sm<?= $order === $k ? ' btn-dark' : '' ?>"<?= $order === $k ? ' aria-current="true"' : '' ?> href="<?= e(url($here) . '?orden=' . $k . '#hilos') ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <?php if (!$threads): ?>
          <div class="card stack" style="align-items:flex-start">
            <h3>Todavía no hay conversaciones aquí</h3>
            <p class="muted">Puedes abrir la primera. Un saludo o una pregunta sencilla es un buen comienzo.</p>
            <a class="btn btn-primary" href="<?= e($newUrl) ?>"><?= icon('plus', 20, 'stroke-width="2.4"') ?>Abrir un hilo nuevo</a>
          </div>
        <?php endif; ?>
        <div class="stack" style="gap:12px"><?php foreach ($threads as $t) partial('trow', ['t' => $t]); ?></div>
        <?php partial('pager', ['pg' => $pg]); ?>
      </section>
    </div>

    <aside class="side stack" style="gap:20px">
      <div class="card stack" style="padding:24px;border-radius:26px">
        <h2 style="font-size:1.15rem">Normas de este foro</h2>
        <?php foreach ([['heart', 'mint', 'Trata a los demás con respeto.'], ['lines', 'sky', 'Escribe claro. Si es una broma, dilo.'], ['lock', 'amber', 'No compartas datos privados de nadie.'], ['flag', 'coral', 'Si algo te molesta, avisa a moderación.']] as [$ic, $c, $txt]): ?>
          <div class="row" style="flex-wrap:nowrap"><span class="pic xs c-<?= $c ?>"><?= icon($ic, 24) ?></span><span><?= e($txt) ?></span></div>
        <?php endforeach; ?>
        <a href="<?= e(url('/normas')) ?>"><b>Leer todas las normas</b></a>
      </div>
      <div class="card dark stack" style="padding:24px;border-radius:26px;gap:14px">
        <span class="pic xs" style="width:52px;height:52px;border-radius:16px;background:var(--gold);color:var(--ink)"><?= icon('shield', 30) ?></span>
        <h2 style="font-size:1.15rem">¿Buscas ayuda profesional?</h2>
        <p class="small">Consulta el directorio de profesionales verificados de psicología, psiquiatría y terapia.</p>
        <div><a class="btn btn-gold btn-flat" style="min-height:48px" href="<?= e(url('/profesionales/directorio')) ?>">Ver profesionales</a></div>
      </div>
    </aside>
  </div>
</div>
