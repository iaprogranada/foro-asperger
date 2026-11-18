<?php
$link = function (array $over) use ($kind, $aud, $term): string {
    $qs = array_filter(array_merge(['tipo' => $kind, 'para' => $aud, 'q' => $term], $over), 'strlen');
    return url('/recursos') . ($qs ? '?' . http_build_query($qs) : '');
};
$fmt = fn (array $r): string => strtoupper(pathinfo($r['original_name'], PATHINFO_EXTENSION)) ?: 'Archivo';
?>
<div class="wrap page">
  <section class="hero-band c-green" style="padding:36px">
    <span class="pic lg"><?= icon('doc', 66, 'stroke-width="1.7"') ?></span>
    <div class="stack" style="flex:1 1 380px;min-width:0;gap:6px">
      <h1>Recursos para descargar</h1>
      <p style="font-size:1.15rem;max-width:34em">Guías, plantillas y pictogramas para el día a día. Elige un tipo y pulsa «Descargar».</p>
    </div>
    <form role="search" class="field" style="flex:1 1 300px;min-width:0">
      <label for="q">Buscar un recurso</label>
      <div class="row" style="flex-wrap:nowrap;gap:8px">
        <?php if ($kind): ?><input type="hidden" name="tipo" value="<?= e($kind) ?>"><?php endif; ?>
        <input id="q" name="q" type="search" value="<?= e($term) ?>" placeholder="Por ejemplo: agenda visual" style="background:var(--surface)">
        <button class="btn btn-color btn-flat" style="min-height:54px;padding:0 16px" type="submit" aria-label="Buscar"><?= icon('search', 24, 'stroke-width="2.2"') ?></button>
      </div>
    </form>
  </section>

  <div class="row between">
    <div class="tabs" role="group" aria-label="Tipo de recurso">
      <a class="btn btn-sm<?= !$kind && !$aud ? ' btn-dark' : '' ?>" style="min-height:52px;padding:0 20px" href="<?= e(url('/recursos')) ?>"><?= icon('grid', 22) ?>Todos</a>
      <?php foreach (RES_KINDS as $k => [, $pluralLabel, $ic]): ?>
        <a class="btn btn-sm<?= $kind === $k ? ' btn-dark' : '' ?>" style="min-height:52px;padding:0 20px" href="<?= e($link(['tipo' => $k, 'para' => ''])) ?>"><?= icon($ic, 22) ?><?= e($pluralLabel) ?></a>
      <?php endforeach; ?>
      <a class="btn btn-sm<?= $aud === 'familias' ? ' btn-dark' : '' ?>" style="min-height:52px;padding:0 20px" href="<?= e($link(['para' => 'familias', 'tipo' => ''])) ?>"><?= icon('house', 22) ?>Para familias</a>
      <a class="btn btn-sm<?= $aud === 'trabajo' ? ' btn-dark' : '' ?>" style="min-height:52px;padding:0 20px" href="<?= e($link(['para' => 'trabajo', 'tipo' => ''])) ?>"><?= icon('briefcase', 22) ?>Para el trabajo</a>
    </div>
    <?php if ($canUpload): ?><a class="btn btn-primary" href="<?= e(url('/recursos/subir')) ?>"><?= icon('upload', 20) ?>Publicar un recurso</a><?php endif; ?>
  </div>

  <?php if ($featured): $c = color($featured['color']); ?>
  <section class="card row c-<?= $c ?>" style="padding:0;overflow:hidden;border-radius:32px;align-items:stretch;gap:0" aria-labelledby="destacado">
    <div class="cover" style="flex:1 1 320px;height:auto;min-height:260px;padding:28px">
      <div class="sheet" style="width:170px;height:220px;border-radius:16px;box-shadow:10px 10px 0 var(--bd);flex-direction:column;gap:14px"><?= icon($featured['icon'], 72, 'stroke-width="1.6"') ?><span style="width:90px;height:8px;border-radius:4px;background:var(--bd)"></span><span style="width:64px;height:8px;border-radius:4px;background:var(--bd)"></span></div>
    </div>
    <div class="stack" style="flex:2 1 420px;min-width:0;padding:36px;align-items:flex-start;gap:14px">
      <span class="pill gold">Recurso destacado</span>
      <h2 id="destacado" style="font-size:clamp(1.6rem,2.6vw,2.1rem)"><?= e($featured['title']) ?></h2>
      <p style="font-size:1.06rem;max-width:36em"><?= e($featured['description']) ?></p>
      <div class="row" style="gap:8px"><span class="tag" style="padding:6px 12px"><?= e(RES_KINDS[$featured['kind']][0]) ?></span><span class="tag" style="padding:6px 12px">Formato <?= e($fmt($featured)) ?> · <?= e(human_size((int) $featured['size'])) ?></span><span class="tag" style="padding:6px 12px"><?= e(AUDIENCES[$featured['audience']]) ?></span></div>
      <a class="btn btn-primary btn-lg" href="<?= e(url('/recursos/' . $featured['id'] . '/descargar')) ?>"><?= icon('download', 22, 'stroke-width="2.2"') ?>Descargar en <?= e($fmt($featured)) ?></a>
    </div>
  </section>
  <?php endif; ?>

  <section class="stack" style="gap:18px">
    <h2><?= $term !== '' ? 'Resultados de «' . e($term) . '»' : ($kind ? e(RES_KINDS[$kind][1]) : ($aud ? e(AUDIENCES[$aud]) : 'Todos los recursos')) ?></h2>
    <?php if (!$list && !$featured): ?>
      <div class="card stack" style="align-items:flex-start">
        <h3><?= ($kind || $aud || $term !== '') ? 'No hay recursos con ese filtro' : 'Pronto habrá recursos aquí' ?></h3>
        <p class="muted"><?= ($kind || $aud || $term !== '') ? 'Prueba con «Todos» o con otra palabra.' : 'El equipo del foro está preparando las primeras guías y plantillas.' ?></p>
      </div>
    <?php endif; ?>
    <div class="grid fill" style="--min:340px;gap:24px">
      <?php foreach ($list as $i => $r): $c = color($r['color']); ?>
        <article class="res c-<?= $c ?>">
          <div class="cover"><div class="sheet"<?= $i % 2 ? ' style="transform:rotate(3deg)"' : '' ?>><?= icon($r['icon'], 52) ?></div></div>
          <div class="in">
            <span class="tag" style="padding:4px 12px"><?= e(RES_KINDS[$r['kind']][0]) ?> · <?= e($fmt($r)) ?></span>
            <h3 style="font-size:1.2rem;line-height:1.25"><?= e($r['title']) ?></h3>
            <p class="muted small"><?= e($r['description']) ?></p>
            <?php if ($r['by_pro']): ?><span class="small">Por <a href="<?= e(url('/profesionales/ver/' . $r['username'])) ?>"><b><?= display_name($r) ?></b></a>, profesional verificado</span><?php endif; ?>
            <a class="btn btn-outline btn-flat" style="min-height:48px" href="<?= e(url('/recursos/' . $r['id'] . '/descargar')) ?>"><?= icon('download', 20, 'stroke-width="2.2"') ?>Descargar</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
    <?php if (!user() && ($list || $featured)): ?><p class="muted">Para descargar necesitas entrar en tu cuenta. <a href="<?= e(url('/registro')) ?>"><b>Crear cuenta gratis</b></a></p><?php endif; ?>
  </section>

  <section class="card dark row" style="border-radius:32px;padding:32px 36px;gap:20px 32px">
    <span class="pic sm" style="width:72px;height:72px;border-radius:22px;background:var(--gold);color:var(--ink)"><?= icon('upload', 40) ?></span>
    <div class="stack" style="flex:1 1 420px;min-width:0;gap:4px">
      <h2 style="font-size:1.4rem">¿Eres profesional y has creado un recurso?</h2>
      <p>Los profesionales verificados pueden publicar aquí sus guías y plantillas, con su nombre.</p>
    </div>
    <a class="btn btn-gold" href="<?= e(url('/profesionales')) ?>">Ver la zona de profesionales</a>
  </section>
</div>
