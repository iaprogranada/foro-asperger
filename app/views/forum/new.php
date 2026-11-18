<?php $selSub = (string) old('sub', $preSub); $selTone = old('tone', 'serio'); ?>
<div class="wrap page" style="max-width:860px;padding-top:20px">
  <nav class="crumbs" aria-label="Ruta">
    <a href="<?= e(url('/')) ?>">Inicio</a><?= icon('chevron', 16, 'stroke-width="2.4"') ?>
    <a href="<?= e(url('/foro/' . $forum['slug'])) ?>"><?= e($forum['name']) ?></a><?= icon('chevron', 16, 'stroke-width="2.4"') ?><span aria-current="page">Hilo nuevo</span>
  </nav>
  <form method="post" class="card stack-lg" style="border-radius:32px;padding:clamp(20px,4vw,36px)">
    <?= csrf_field() ?>
    <div class="stack" style="gap:6px">
      <h1 style="font-size:clamp(1.8rem,3.4vw,2.4rem)">Abrir un hilo nuevo</h1>
      <p class="lead">Foro: <?= e($forum['name']) ?>. Sigue los tres pasos.</p>
    </div>
    <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>

    <?php if ($subs): ?>
    <fieldset>
      <legend class="h-num"><span class="num">1</span>¿En qué subforo va?</legend>
      <div class="choices">
        <?php foreach ($subs as $s): ?>
          <label class="choice"><input type="radio" name="sub" value="<?= (int) $s['id'] ?>" required<?= $selSub === (string) $s['id'] ? ' checked' : '' ?>><span><?= icon($s['icon'], 20) ?><?= e($s['name']) ?></span></label>
        <?php endforeach; ?>
      </div>
    </fieldset>
    <?php endif; ?>

    <div class="stack">
      <span class="label h-num"><span class="num">2</span>Escribe tu mensaje</span>
      <div class="field">
        <label for="title">Título</label>
        <input id="title" name="title" type="text" required minlength="5" maxlength="140" value="<?= e(old('title')) ?>" aria-describedby="title-h">
        <span class="help" id="title-h">Una frase que resuma el tema. Ejemplo: «¿Cómo preparáis una entrevista de trabajo?»</span>
      </div>
      <div class="field">
        <label for="body">Mensaje</label>
        <textarea id="body" name="body" rows="9" required minlength="10" maxlength="10000" aria-describedby="body-h"><?= e(old('body')) ?></textarea>
        <span class="help" id="body-h">Las frases cortas se leen mejor. Deja una línea en blanco entre párrafos.</span>
      </div>
    </div>

    <fieldset>
      <legend class="h-num"><span class="num">3</span>¿Con qué tono lo dices?</legend>
      <p class="help" style="margin-bottom:12px">Así nadie tiene que adivinarlo.</p>
      <div class="choices">
        <?php foreach (TONES as $k => $label): ?>
          <label class="choice"><input type="radio" name="tone" value="<?= $k ?>"<?= $selTone === $k ? ' checked' : '' ?>><span><?= e($label) ?></span></label>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <div class="row" style="gap:14px">
      <button class="btn btn-primary btn-lg" type="submit">Publicar el hilo</button>
      <a class="btn btn-lg btn-flat" href="<?= e(url('/foro/' . $forum['slug'])) ?>">Cancelar</a>
    </div>
  </form>
</div>
