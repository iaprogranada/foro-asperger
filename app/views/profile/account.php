<div class="wrap page" style="padding-top:36px">
  <div class="split">
    <?php partial('sidenav', ['tab' => $tab]); ?>
    <div class="stack" style="flex:999 1 520px;min-width:0;gap:24px">
      <div class="stack" style="gap:6px">
        <h1>Cuenta y privacidad</h1>
        <p class="lead">Para cambiar cualquier dato de esta página tienes que escribir tu contraseña actual.</p>
      </div>
      <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>

      <form method="post" class="card stack">
        <?= csrf_field() ?><input type="hidden" name="do" value="email">
        <h2 class="row" style="font-size:1.3rem;gap:10px"><?= icon('mail', 26, 'style="color:var(--teal)"') ?>Correo electrónico</h2>
        <div class="field"><label for="email">Correo</label><input id="email" name="email" type="email" required value="<?= e($me['email']) ?>" style="max-width:420px"><span class="help">Nadie más lo ve.</span></div>
        <div class="field"><label for="c1">Contraseña actual</label><input id="c1" name="current" type="password" required autocomplete="current-password" style="max-width:420px"></div>
        <div><button class="btn btn-primary" type="submit">Cambiar el correo</button></div>
      </form>

      <form method="post" class="card stack">
        <?= csrf_field() ?><input type="hidden" name="do" value="password">
        <h2 class="row" style="font-size:1.3rem;gap:10px"><?= icon('lock', 26, 'style="color:var(--teal)"') ?>Contraseña</h2>
        <div class="field"><label for="c2">Contraseña actual</label><input id="c2" name="current" type="password" required autocomplete="current-password" style="max-width:420px"></div>
        <div class="field"><label for="password">Contraseña nueva</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" style="max-width:420px"><span class="help">Mínimo 8 caracteres.</span></div>
        <div><button class="btn btn-primary" type="submit">Cambiar la contraseña</button></div>
      </form>

      <form method="post" class="card tint c-coral stack" data-confirm="¿Borrar tu cuenta y todos tus mensajes? No se puede deshacer.">
        <?= csrf_field() ?><input type="hidden" name="do" value="delete">
        <h2 class="row" style="font-size:1.3rem;gap:10px"><?= icon('trash', 26, 'style="color:var(--ic)"') ?>Borrar mi cuenta</h2>
        <p>Se borran tu perfil, tus hilos, tus respuestas, tus mensajes privados y tu presentación. No se puede deshacer.</p>
        <div class="field"><label for="c3">Contraseña actual</label><input id="c3" name="current" type="password" required autocomplete="current-password" style="max-width:420px;background:var(--surface)"></div>
        <div><button class="btn btn-color" type="submit">Borrar mi cuenta para siempre</button></div>
      </form>
    </div>
  </div>
</div>
