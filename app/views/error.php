<div class="wrap page" style="max-width:760px">
  <div class="card stack" style="align-items:flex-start">
    <span class="pic xs c-amber"><?= icon('question', 26) ?></span>
    <h1 style="font-size:2rem"><?= e($title) ?></h1>
    <p class="lead"><?= e($message ?: ($code === 404 ? 'Puede que la dirección esté mal escrita o que el contenido se haya borrado.' : 'Si crees que es un error, escríbenos desde la página de contacto.')) ?></p>
    <a class="btn btn-primary" href="<?= e(url('/')) ?>">Volver al inicio</a>
  </div>
</div>
