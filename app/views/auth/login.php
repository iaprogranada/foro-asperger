<div class="wrap page" style="max-width:640px">
  <form method="post" class="card stack-lg" style="border-radius:36px;padding:clamp(20px,5vw,40px)">
    <?= csrf_field() ?>
    <div class="stack" style="gap:6px">
      <h1>Entrar</h1>
      <p class="lead">Escribe tu usuario o tu correo, y tu contraseña.</p>
    </div>
    <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <div class="field">
      <label for="login">Usuario o correo electrónico</label>
      <input id="login" name="login" type="text" required autocomplete="username" value="<?= e(old('login')) ?>">
    </div>
    <div class="field">
      <label for="password">Contraseña</label>
      <div class="row" style="flex-wrap:nowrap">
        <input id="password" name="password" type="password" required autocomplete="current-password">
        <button type="button" class="btn btn-sm" style="min-height:54px" data-show-pass="password"><?= icon('eye', 22) ?><span>Mostrar</span></button>
      </div>
    </div>
    <div class="row" style="gap:16px">
      <button class="btn btn-primary btn-lg" type="submit">Entrar en mi cuenta</button>
      <a href="<?= e(url('/recuperar')) ?>"><b>He olvidado mi contraseña</b></a>
    </div>
    <p class="muted">¿Todavía no tienes cuenta? <a href="<?= e(url('/registro')) ?>"><b>Crear cuenta gratis</b></a></p>
  </form>
</div>
