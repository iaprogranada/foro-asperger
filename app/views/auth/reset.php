<div class="wrap page" style="max-width:640px">
  <form method="post" class="card stack-lg" style="border-radius:36px;padding:clamp(20px,5vw,40px)">
    <?= csrf_field() ?>
    <h1>Elige una contraseña nueva</h1>
    <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <div class="field">
      <label for="password">Contraseña nueva</label>
      <div class="row" style="flex-wrap:nowrap">
        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password">
        <button type="button" class="btn btn-sm" style="min-height:54px" data-show-pass="password"><?= icon('eye', 22) ?><span>Mostrar</span></button>
      </div>
      <span class="help">Mínimo 8 caracteres.</span>
    </div>
    <div><button class="btn btn-primary btn-lg" type="submit">Guardar la contraseña</button></div>
  </form>
</div>
