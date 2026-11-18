<?php
$myIds = csv_list($me['identities']);
$shownIds = array_intersect_key(IDENTITIES, array_flip($myIds)); unset($shownIds['no_decir']);
$tagCols = ['mint', 'coral', 'sky', 'amber', 'pink', 'lav'];
?>
<div class="wrap page" style="padding-top:36px">
  <div class="split">
    <?php partial('sidenav', ['tab' => $tab]); ?>

    <form method="post" enctype="multipart/form-data" class="stack" style="flex:999 1 520px;min-width:0;gap:24px">
      <?= csrf_field() ?>
      <?php if ($step2): ?>
        <input type="hidden" name="paso" value="2">
        <ol class="steps" aria-label="Pasos del registro">
          <li class="done"><span class="num"><?= icon('check', 20, 'stroke-width="2.6"') ?></span>Tus datos</li><li class="bar" aria-hidden="true"></li>
          <li class="on" aria-current="step"><span class="num">2</span>Tu perfil</li><li class="bar" aria-hidden="true"></li>
          <li><span class="num">3</span>Tu primer foro</li>
        </ol>
      <?php endif; ?>
      <div class="stack" style="gap:6px">
        <h1><?= $step2 ? 'Prepara tu perfil' : 'Mi perfil' ?></h1>
        <p class="lead">Aquí cambias tu foto, tu nombre y tu biografía.<?= $step2 ? ' Todo es opcional: puedes hacerlo más tarde.' : '' ?></p>
      </div>
      <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>

      <section class="card stack" style="gap:20px" aria-labelledby="t-foto">
        <h2 id="t-foto" class="row" style="font-size:1.3rem;gap:10px"><?= icon('camera', 26, 'style="color:var(--teal)"') ?>Foto de perfil</h2>
        <div class="row" style="gap:20px">
          <?= avatar($me, 120) ?>
          <div class="field" style="flex:1 1 260px">
            <label for="photo">Subir una foto</label>
            <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" aria-describedby="photo-h">
            <span class="help" id="photo-h">JPG, PNG o WebP. Máximo 5 MB. La recortamos en cuadrado.</span>
          </div>
        </div>
        <fieldset>
          <legend>O elige un dibujo. No hace falta enseñar tu cara.</legend>
          <div class="row">
            <?php foreach (AVATAR_CHOICES as $choice): [$ic, $c] = explode(':', $choice); ?>
              <label class="avatar-pick c-<?= $c ?>"><input type="radio" name="avatar" value="<?= $choice ?>" aria-label="Dibujo <?= $ic ?>"<?= $me['avatar_type'] === 'icon' && $me['avatar_value'] === $choice ? ' checked' : '' ?>><span><?= icon($ic, 38) ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>
      </section>

      <section class="card stack" style="gap:10px" aria-labelledby="t-nombre">
        <h2 id="t-nombre" class="row" style="font-size:1.3rem;gap:10px;margin-bottom:6px"><?= icon('pencil', 26, 'style="color:var(--teal)"') ?>Nombre</h2>
        <div class="field">
          <label for="display_name">Nombre que ven los demás</label>
          <input id="display_name" name="display_name" type="text" required maxlength="40" style="max-width:420px" value="<?= e($me['display_name']) ?>" aria-describedby="name-h">
          <span class="help" id="name-h">Puedes cambiarlo cuando quieras. Tu nombre de usuario para entrar sigue siendo «<?= e($me['username']) ?>».</span>
        </div>
      </section>

      <section class="card stack" style="gap:10px" aria-labelledby="t-bio">
        <h2 id="t-bio" class="row" style="font-size:1.3rem;gap:10px;margin-bottom:6px"><?= icon('lines', 26, 'style="color:var(--teal)"') ?>Biografía</h2>
        <div class="field">
          <label for="bio">Cuenta algo sobre ti</label>
          <textarea id="bio" name="bio" rows="4" maxlength="300" data-count="bio-n" aria-describedby="bio-h bio-n"><?= e($me['bio']) ?></textarea>
          <span class="help" id="bio-h">Máximo 300 caracteres. Ideas: qué te gusta, cómo prefieres comunicarte.</span>
          <span class="help" id="bio-n" aria-live="polite"></span>
        </div>
      </section>

      <section class="card stack" style="gap:14px">
        <fieldset>
          <legend class="row" style="font-size:1.3rem;gap:10px"><?= icon('inf', 26, 'style="color:var(--teal)"') ?>Me identifico como</legend>
          <p class="muted" style="margin-bottom:14px">Es opcional. Elige todas las que quieras. Se ve en tu perfil.</p>
          <div class="choices">
            <?php foreach (IDENTITIES as $k => $label): ?>
              <label class="choice"><input type="checkbox" name="identities[]" value="<?= $k ?>"<?= in_array($k, $myIds, true) ? ' checked' : '' ?>><span><?= e($label) ?></span></label>
            <?php endforeach; ?>
          </div>
        </fieldset>
      </section>

      <section id="sensorial" class="card tint c-sky stack" style="gap:6px" aria-labelledby="t-sens">
        <h2 id="t-sens" class="row" style="font-size:1.3rem;gap:10px;margin-bottom:8px"><?= icon('moon', 26, 'style="color:var(--ic)"') ?>Ajustes sensoriales</h2>
        <?php foreach ([
            'pref_calm' => ['Modo calma siempre activado.', 'Colores más suaves en toda la web.'],
            'pref_noanim' => ['Sin animaciones.', 'Nada se mueve en la pantalla.'],
            'pref_bigtext' => ['Texto más grande.', 'Aumenta el tamaño de la letra.'],
            'pref_hidecounts' => ['Ocultar los contadores.', 'No se ven los números de respuestas ni de reacciones.'],
        ] as $k => [$b, $txt]): ?>
          <label class="check" style="align-items:center;min-height:52px"><input type="checkbox" name="<?= $k ?>" value="1"<?= $me[$k] ? ' checked' : '' ?>><span><b><?= e($b) ?></b> <?= e($txt) ?></span></label>
        <?php endforeach; ?>
      </section>

      <div class="row" style="gap:14px">
        <button class="btn btn-primary btn-lg" type="submit"><?= icon('check', 22, 'stroke-width="2.6"') ?><?= $step2 ? 'Guardar y elegir mi primer foro' : 'Guardar los cambios' ?></button>
        <?php if ($step2): ?><a class="btn btn-lg btn-flat" href="<?= e(url('/#foros')) ?>">Hacerlo más tarde</a><?php endif; ?>
      </div>
    </form>

    <aside class="side stack" style="gap:12px" aria-labelledby="t-vista">
      <h2 id="t-vista" class="muted" style="font-size:1.05rem;font-weight:700">Así te ven los demás</h2>
      <div class="card pop c-lav stack center" style="align-items:center;gap:14px;border-color:var(--bd);padding:26px">
        <?= avatar($me, 104) ?>
        <b style="font-family:var(--head);font-weight:800;font-size:1.4rem;overflow-wrap:anywhere"><?= display_name($me) ?></b>
        <?php if ($shownIds): ?>
          <div class="row" style="justify-content:center;gap:8px"><?php $i = 0; foreach ($shownIds as $label): ?><span class="tag c-<?= $tagCols[$i++ % 6] ?>" style="padding:5px 12px"><?= e($label) ?></span><?php endforeach; ?></div>
        <?php endif; ?>
        <p class="muted small" style="overflow-wrap:anywhere"><?= $me['bio'] !== '' ? e($me['bio']) : 'Todavía no has escrito tu biografía.' ?></p>
        <a href="<?= e(url('/u/' . $me['username'])) ?>"><b>Ver mi perfil público</b></a>
      </div>
    </aside>
  </div>
</div>
