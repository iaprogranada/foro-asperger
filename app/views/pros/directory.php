<div class="wrap page">
  <section class="hero-band c-amber" style="padding:36px">
    <span class="pic lg"><?= icon('shield', 66, 'stroke-width="1.7"') ?></span>
    <div class="stack" style="flex:1 1 380px;min-width:0;gap:6px">
      <h1>Directorio de profesionales</h1>
      <p style="font-size:1.15rem;max-width:36em">Profesionales verificados que conocen el autismo y la neurodivergencia. Comprobamos su número de colegiación.</p>
    </div>
  </section>

  <form class="card row" style="align-items:flex-end;padding:22px;gap:16px" role="search">
    <div class="field" style="flex:1 1 220px"><label for="ciudad">Ciudad</label><input id="ciudad" name="ciudad" type="text" value="<?= e($city) ?>" placeholder="Por ejemplo: Granada"></div>
    <div class="field" style="flex:1 1 260px"><label for="q">Especialidad o nombre</label><input id="q" name="q" type="text" value="<?= e($term) ?>" placeholder="Por ejemplo: diagnóstico adultos"></div>
    <label class="check" style="align-items:center;min-height:54px"><input type="checkbox" name="online" value="1"<?= $online ? ' checked' : '' ?>><span><b>Atiende online</b></span></label>
    <button class="btn btn-primary btn-flat" style="min-height:54px" type="submit"><?= icon('search', 22) ?>Buscar</button>
  </form>

  <?php if (!$list): ?>
    <div class="card stack" style="align-items:flex-start">
      <h2><?= ($city !== '' || $term !== '' || $online) ? 'No hay profesionales con ese filtro' : 'Pronto habrá profesionales aquí' ?></h2>
      <p class="lead"><?= ($city !== '' || $term !== '' || $online) ? 'Prueba con otra ciudad o marca «Atiende online».' : 'Estamos verificando a los primeros profesionales.' ?></p>
      <a class="btn" href="<?= e(url('/profesionales')) ?>">Soy profesional y quiero aparecer aquí</a>
    </div>
  <?php endif; ?>
  <div class="grid fill" style="--min:340px;gap:24px"><?php foreach ($list as $p) partial('pro_card', ['p' => $p]); ?></div>
  <?php partial('pager', ['pg' => $pg]); ?>

  <p class="muted" style="max-width:48em">El foro comprueba la colegiación de cada profesional, pero no interviene en la relación entre profesional y paciente. Si tienes una urgencia, llama al 112.</p>
</div>
