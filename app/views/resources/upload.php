<div class="wrap page" style="max-width:860px">
  <form method="post" enctype="multipart/form-data" class="card stack-lg" style="border-radius:32px;padding:clamp(20px,4vw,36px)">
    <?= csrf_field() ?>
    <div class="stack" style="gap:6px">
      <h1 style="font-size:clamp(1.8rem,3.4vw,2.4rem)">Publicar un recurso</h1>
      <p class="lead"><?= is_mod() ? 'Se publica al momento.' : 'El equipo del foro lo revisa antes de publicarlo. Aparece con tu nombre.' ?></p>
    </div>
    <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>
    <div class="field"><label for="title">Título</label><input id="title" name="title" type="text" required minlength="5" maxlength="120" value="<?= e(old('title')) ?>"></div>
    <div class="field"><label for="description">Descripción corta</label><textarea id="description" name="description" rows="3" maxlength="300" aria-describedby="d-h"><?= e(old('description')) ?></textarea><span class="help" id="d-h">Para qué sirve y a quién ayuda. Máximo 300 caracteres.</span></div>
    <fieldset>
      <legend>Tipo de recurso</legend>
      <div class="choices"><?php foreach (RES_KINDS as $k => [$label, , $ic]): ?><label class="choice"><input type="radio" name="kind" value="<?= $k ?>"<?= old('kind', 'guia') === $k ? ' checked' : '' ?>><span><?= icon($ic, 20) ?><?= e($label) ?></span></label><?php endforeach; ?></div>
    </fieldset>
    <fieldset>
      <legend>¿Para quién es?</legend>
      <div class="choices"><?php foreach (AUDIENCES as $k => $label): ?><label class="choice"><input type="radio" name="audience" value="<?= $k ?>"<?= old('audience', 'todos') === $k ? ' checked' : '' ?>><span><?= e($label) ?></span></label><?php endforeach; ?></div>
    </fieldset>
    <div class="field"><label for="file">Archivo</label><input id="file" name="file" type="file" required accept=".pdf,.docx,.odt,.pptx,.zip,.png,.jpg,.jpeg" aria-describedby="f-h"><span class="help" id="f-h">PDF, Word, OpenDocument, PowerPoint, ZIP, PNG o JPG. Máximo 15 MB.</span></div>
    <div class="row" style="gap:14px"><button class="btn btn-primary btn-lg" type="submit"><?= icon('upload', 22) ?>Publicar el recurso</button><a class="btn btn-lg btn-flat" href="<?= e(url('/recursos')) ?>">Cancelar</a></div>
  </form>
</div>
