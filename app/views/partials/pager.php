<?php /** Paginación. Recibe $pg (de paginate()). */ if ($pg['pages'] > 1): ?>
<nav class="pager" aria-label="Páginas">
  <?php if ($pg['page'] > 1): ?><a class="btn btn-sm" href="<?= e(page_url($pg['page'] - 1)) ?>">Página anterior</a><?php endif; ?>
  <span>Página <?= $pg['page'] ?> de <?= $pg['pages'] ?></span>
  <?php if ($pg['page'] < $pg['pages']): ?><a class="btn btn-sm" href="<?= e(page_url($pg['page'] + 1)) ?>">Página siguiente</a><?php endif; ?>
</nav>
<?php endif; ?>
