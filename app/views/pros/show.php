<?php
$specCols = ['mint', 'sky', 'pink', 'amber', 'lav', 'green'];
$citaHref = $p['booking_url'] ?: ($p['website'] ?: ($p['phone'] !== '' ? 'tel:' . preg_replace('/[^\d+]/', '', $p['phone']) : ''));
?>
<div class="wrap page" style="max-width:980px">
  <nav class="crumbs" aria-label="Ruta"><a href="<?= e(url('/profesionales/directorio')) ?>">Directorio de profesionales</a><?= icon('chevron', 16, 'stroke-width="2.4"') ?><span aria-current="page"><?= e($p['full_name']) ?></span></nav>
  <?php if (!$p['is_active']): ?><div class="flash c-amber"><?= icon('question', 24) ?><span>Vista previa. Esta ficha todavía no es pública: falta la verificación o el pago.</span></div><?php endif; ?>

  <section class="card stack" style="border-radius:36px;padding:clamp(20px,4vw,40px);gap:22px;border-color:#E9CC84;box-shadow:0 8px 0 #E9CC84">
    <div class="row" style="gap:20px">
      <?= avatar($p, 112) ?>
      <div class="stack" style="flex:1 1 320px;min-width:0;gap:8px">
        <h1 style="font-size:clamp(1.9rem,3.6vw,2.6rem);overflow-wrap:anywhere"><?= e($p['full_name']) ?></h1>
        <p style="font-size:1.15rem"><?= e($p['profession']) ?></p>
        <div class="row muted" style="gap:14px"><span class="row" style="gap:6px"><?= icon('pin', 20) ?><?= e($p['city']) ?></span><?php if ($p['online']): ?><span class="row" style="gap:6px"><?= icon('video', 20) ?>Atiende online</span><?php endif; ?></div>
      </div>
    </div>
    <span class="pill gold" style="align-self:flex-start;border-radius:12px;font-size:1rem"><?= icon('shield', 20, 'stroke-width="2.2"') ?>Profesional verificado · colegiación n.º <?= e($p['license_no']) ?></span>
    <?php if ($p['specialties'] !== ''): ?><div class="row" style="gap:8px"><?php foreach (csv_list($p['specialties']) as $i => $s): ?><span class="tag c-<?= $specCols[$i % 6] ?>" style="padding:6px 14px;font-size:1rem"><?= e($s) ?></span><?php endforeach; ?></div><?php endif; ?>
    <?php if ($p['bio'] !== ''): ?><div class="prose"><?= format_body($p['bio']) ?></div><?php endif; ?>
    <div class="row" style="gap:14px">
      <?php if ($citaHref): ?><a class="btn btn-primary btn-lg" href="<?= e($citaHref) ?>" rel="nofollow noopener"<?= str_starts_with($citaHref, 'http') ? ' target="_blank"' : '' ?>><?= icon('calendar', 22) ?>Pedir cita</a><?php endif; ?>
      <?php if ($p['website'] && $p['website'] !== $citaHref): ?><a class="btn btn-lg" href="<?= e($p['website']) ?>" rel="nofollow noopener" target="_blank"><?= icon('globe', 22) ?>Ver su web</a><?php endif; ?>
      <?php if ($p['phone'] !== '' && !str_starts_with((string) $citaHref, 'tel:')): ?><a class="btn btn-lg" href="tel:<?= e(preg_replace('/[^\d+]/', '', $p['phone'])) ?>"><?= icon('phone', 22) ?><?= e($p['phone']) ?></a><?php endif; ?>
    </div>
    <p class="help">El botón te lleva a la web, la agenda o el teléfono de este profesional. El foro no gestiona las citas.</p>
  </section>

  <?php if ($resources): ?>
  <section class="stack">
    <h2>Recursos publicados por <?= e($p['full_name']) ?></h2>
    <div class="stack" style="gap:12px">
      <?php foreach ($resources as $r): ?>
        <a class="trow" href="<?= e(url('/recursos/' . $r['id'] . '/descargar')) ?>"><span class="pic xs c-green"><?= icon('doc', 24) ?></span><span class="grow"><span class="t"><?= e($r['title']) ?></span><span class="meta"><?= e(RES_KINDS[$r['kind']][0]) ?></span></span><span class="n"><?= icon('download', 22) ?>Descargar</span></a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <p><a href="<?= e(url('/u/' . $p['username'])) ?>"><b>Ver su perfil y sus hilos en el foro</b></a></p>
</div>
