<div class="wrap page">
  <div class="stack">
    <h1>Buscar en los foros</h1>
    <form role="search" class="row" style="flex-wrap:nowrap;max-width:640px;gap:8px">
      <label class="sr" for="q">Palabras que quieres buscar</label>
      <input id="q" name="q" type="search" value="<?= e($term) ?>" placeholder="Por ejemplo: entrevista de trabajo" required minlength="2">
      <button class="btn btn-primary btn-flat" style="min-height:54px" type="submit"><?= icon('search', 22) ?>Buscar</button>
    </form>
  </div>
  <?php if ($term !== ''): ?>
    <div class="stack">
      <h2><?= $threads ? plural(count($threads), 'conversación encontrada', 'conversaciones encontradas') : 'No hay conversaciones con esas palabras' ?></h2>
      <?php if (!$threads): ?><p class="lead">Prueba con otra palabra o con menos palabras.</p><?php endif; ?>
      <div class="stack" style="gap:12px"><?php foreach ($threads as $t) partial('trow', ['t' => $t]); ?></div>
    </div>
  <?php endif; ?>
</div>
