<div class="wrap page" style="max-width:760px">
  <div class="stack">
    <h1>Instalar Foro Asperger</h1>
    <p class="lead">Rellena estos datos una sola vez. Al terminar, la web queda lista y entras como administrador.</p>
  </div>
  <?php if ($errors): ?><ul class="errors"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>
  <form method="post" class="stack-lg">
    <?= csrf_field() ?>
    <?php if (!$hasConfig): ?>
    <section class="card stack">
      <h2 class="h-num"><span class="num">1</span>Base de datos MySQL</h2>
      <p class="muted">En Hostinger: hPanel → Bases de datos → MySQL. Crea una base de datos y copia aquí sus datos.</p>
      <div class="field"><label for="base_url">Dirección de la web</label><input id="base_url" name="base_url" type="url" required value="<?= e(old('base_url', $guessUrl)) ?>"></div>
      <div class="grid" style="--min:240px">
        <div class="field"><label for="db_name">Nombre de la base de datos</label><input id="db_name" name="db_name" type="text" required value="<?= e(old('db_name')) ?>"></div>
        <div class="field"><label for="db_user">Usuario de la base de datos</label><input id="db_user" name="db_user" type="text" required value="<?= e(old('db_user')) ?>"></div>
        <div class="field"><label for="db_pass">Contraseña de la base de datos</label><input id="db_pass" name="db_pass" type="password" autocomplete="off"></div>
        <div class="field"><label for="db_host">Servidor</label><input id="db_host" name="db_host" type="text" value="<?= e(old('db_host', 'localhost')) ?>"><span class="help">Casi siempre es «localhost».</span></div>
      </div>
      <input type="hidden" name="db_port" value="<?= e(old('db_port', '3306')) ?>">
    </section>
    <?php endif; ?>
    <section class="card stack">
      <h2 class="h-num"><span class="num"><?= $hasConfig ? '1' : '2' ?></span>Tu cuenta de administración</h2>
      <div class="field"><label for="username">Nombre de usuario</label><input id="username" name="username" type="text" required value="<?= e(old('username')) ?>"></div>
      <div class="field"><label for="email">Correo electrónico</label><input id="email" name="email" type="email" required value="<?= e(old('email')) ?>"></div>
      <div class="field"><label for="password">Contraseña</label><input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"><span class="help">Mínimo 8 caracteres.</span></div>
    </section>
    <div><button class="btn btn-primary btn-lg" type="submit">Instalar la web <?= icon('arrow', 22) ?></button></div>
  </form>
</div>
