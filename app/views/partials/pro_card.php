<?php /** Ficha de profesional en una lista. Recibe $p y, opcionalmente, $demo. */
$demo = $demo ?? false;
$specCols = ['mint', 'sky', 'pink', 'amber', 'lav', 'green'];
?>
<article class="card person" style="padding:26px">
  <div class="row" style="gap:14px;flex-wrap:nowrap">
    <?= avatar($p, 68) ?>
    <div style="min-width:0">
      <h3 style="font-size:1.25rem;overflow-wrap:anywhere"><?= e($p['full_name']) ?></h3>
      <span class="muted"><?= e($p['profession']) ?> · <?= e($p['city']) ?><?= $p['online'] ? ' y online' : '' ?></span>
    </div>
  </div>
  <span class="pill gold" style="align-self:flex-start;border-radius:12px"><?= icon('shield', 18, 'stroke-width="2.2"') ?>Profesional verificado · n.º <?= e($p['license_no']) ?></span>
  <?php if ($p['specialties'] !== ''): ?>
    <div class="row" style="gap:8px"><?php foreach (csv_list($p['specialties']) as $i => $s): ?><span class="tag c-<?= $specCols[$i % 6] ?>" style="padding:5px 12px"><?= e($s) ?></span><?php endforeach; ?></div>
  <?php endif; ?>
  <?php if ($demo): ?>
    <span class="btn btn-primary btn-flat" style="cursor:default">Ver la ficha y pedir cita</span>
  <?php else: ?>
    <a class="btn btn-primary" href="<?= e(url('/profesionales/ver/' . $p['username'])) ?>">Ver la ficha y pedir cita</a>
  <?php endif; ?>
</article>
