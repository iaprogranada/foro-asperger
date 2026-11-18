<div class="wrap page" style="max-width:640px">
  <?php if ($sent): ?>
    <div class="card tint c-mint stack" style="align-items:flex-start">
      <span class="pic sm"><?= icon('mail', 34) ?></span>
      <h1 style="font-size:2rem">Revisa tu correo</h1>
      <p class="lead" style="color:var(--ink)">Si hay una cuenta con ese correo, te hemos enviado un enlace para cambiar la contraseña. Funciona durante 2 horas. Mira también en la carpeta de correo no deseado.</p>
      <a class="btn" href="<?= e(url('/entrar')) ?>">Volver a «Entrar»</a>
    </div>
  <?php else: ?>
    <form method="post" class="card stack-lg" style="border-radius:36px;padding:clamp(20px,5vw,40px)">
      <?= csrf_field() ?>
      <div class="stack" style="gap:6px">
        <h1>Recuperar contraseña</h1>
        <p class="lead">Escribe el correo de tu cuenta. Te enviamos un enlace para elegir una contraseña nueva.</p>
      </div>
      <div class="field"><label for="email">Correo electrónico</label><input id="email" name="email" type="email" required autocomplete="email"></div>
      <div><button class="btn btn-primary btn-lg" type="submit">Enviarme el enlace</button></div>
    </form>
  <?php endif; ?>
</div>
