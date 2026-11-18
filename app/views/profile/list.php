<div class="wrap page" style="padding-top:36px">
  <div class="split">
    <?php partial('sidenav', ['tab' => $tab]); ?>
    <div class="stack" style="flex:999 1 520px;min-width:0;gap:24px">
      <h1><?= e($title) ?></h1>
      <?php if (!$threads): ?><div class="card"><p class="lead"><?= e($empty) ?></p></div><?php endif; ?>
      <div class="stack" style="gap:12px"><?php foreach ($threads as $t) partial('trow', ['t' => $t]); ?></div>
      <?php partial('pager', ['pg' => $pg]); ?>
    </div>
  </div>
</div>
