<div class="wrap page" style="max-width:860px">
  <form method="post" class="card stack-lg" style="border-radius:32px;padding:clamp(20px,4vw,36px)">
    <?= csrf_field() ?>
    <h1 style="font-size:clamp(1.8rem,3.4vw,2.4rem)">Editar mensaje</h1>
    <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <?php if ($p['is_first']): ?>
      <div class="field"><label for="title">Título del hilo</label><input id="title" name="title" type="text" required minlength="5" maxlength="140" value="<?= e(old('title', $p['title'])) ?>"></div>
    <?php endif; ?>
    <div class="field"><label for="body">Mensaje</label><textarea id="body" name="body" rows="10" required maxlength="10000"><?= e(old('body', $p['body'])) ?></textarea></div>
    <div class="row" style="gap:14px">
      <button class="btn btn-primary btn-lg" type="submit"><?= icon('check', 22, 'stroke-width="2.6"') ?>Guardar los cambios</button>
      <a class="btn btn-lg btn-flat" href="<?= e(url('/hilo/' . $p['thread_id'] . '-' . $p['slug'] . '#post-' . $p['id'])) ?>">Cancelar</a>
    </div>
  </form>
</div>
