<?php $whoSel = old('who', $_GET['soy'] ?? 'nd'); ?>
<div class="wrap page">
  <div class="split">
    <form method="post" class="card main stack-lg" style="border-radius:36px;padding:clamp(20px,4vw,40px)">
      <?= csrf_field() ?>
      <ol class="steps" aria-label="Pasos del registro">
        <li class="on" aria-current="step"><span class="num">1</span>Tus datos</li>
        <li class="bar" aria-hidden="true"></li>
        <li><span class="num">2</span>Tu perfil</li>
        <li class="bar" aria-hidden="true"></li>
        <li><span class="num">3</span>Tu primer foro</li>
      </ol>
      <div class="stack" style="gap:6px">
        <h1>Crea tu usuario</h1>
        <p class="lead">Son tres pasos. Tardas unos dos minutos.</p>
      </div>
      <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>

      <fieldset>
        <legend style="font-size:1.15rem">¿Quién eres?</legend>
        <div class="grid" style="--min:250px;gap:16px">
          <?php foreach (WHO as $key => [$label, $ic, $col]): ?>
            <label class="opt c-<?= $col ?>">
              <input type="radio" name="who" value="<?= $key ?>"<?= $whoSel === $key ? ' checked' : '' ?>>
              <span><span class="pic sm"><?= icon($ic, 38) ?></span><span><b><?= e($label) ?></b><?= $key === 'pro' ? '<small>Zona de pago, con verificación</small>' : '' ?></span></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <div class="field">
        <label for="username">Nombre de usuario</label>
        <input id="username" name="username" type="text" required minlength="3" maxlength="30" autocomplete="username" aria-describedby="username-h" value="<?= e(old('username')) ?>">
        <span class="help" id="username-h">Puede ser inventado. No hace falta que uses tu nombre real. Sin espacios.</span>
      </div>
      <div class="field">
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" required autocomplete="email" aria-describedby="email-h" value="<?= e(old('email')) ?>">
        <span class="help" id="email-h">Solo lo usamos para avisarte. Nadie más lo ve.</span>
      </div>
      <div class="field">
        <label for="password">Contraseña</label>
        <div class="row" style="flex-wrap:nowrap">
          <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" aria-describedby="password-h">
          <button type="button" class="btn btn-sm" style="min-height:54px" data-show-pass="password"><?= icon('eye', 22) ?><span>Mostrar</span></button>
        </div>
        <span class="help" id="password-h">Mínimo 8 caracteres.</span>
      </div>
      <label class="check"><input type="checkbox" name="normas" value="1" required<?= old('normas') ? ' checked' : '' ?>><span>He leído las <a href="<?= e(url('/normas')) ?>" target="_blank"><b>normas de la comunidad</b></a> y las acepto.</span></label>
      <div class="row" style="gap:16px">
        <button class="btn btn-primary btn-lg" type="submit">Crear mi cuenta y seguir <?= icon('arrow', 22) ?></button>
        <span class="help">Siguiente paso: tu foto y tu biografía.</span>
      </div>
      <p class="muted">¿Ya tienes cuenta? <a href="<?= e(url('/entrar')) ?>"><b>Entrar</b></a></p>
    </form>

    <aside class="side card tint pop c-mint stack" style="border-radius:36px;padding:36px;gap:22px">
      <h2>Con tu cuenta puedes</h2>
      <?php foreach ([['chat-lines', 'sky', 'Escribir en los foros'], ['doc', 'green', 'Descargar recursos'], ['heart', 'pink', 'Presentarte en Amor neurodivergente'], ['moon', 'lav', 'Guardar tus ajustes de modo calma'], ['user', 'amber', 'Tener tu perfil con foto y biografía']] as [$ic, $col, $txt]): ?>
        <div class="row" style="gap:14px;flex-wrap:nowrap"><span class="pic sm c-<?= $col ?>"><?= icon($ic, 34) ?></span><b><?= e($txt) ?></b></div>
      <?php endforeach; ?>
    </aside>
  </div>
</div>
