<?php /** Fila de un hilo en una lista. Recibe $t. */ ?>
<a class="trow<?= $t['pinned'] ? ' pinned' : '' ?>" href="<?= e(thread_url($t)) ?>">
  <?php if ($t['pinned']): ?>
    <span class="avatar c-amber" style="width:52px;height:52px;background:var(--surface)"><?= icon('pin', 26) ?></span>
  <?php else: ?>
    <?= avatar($t, 52) ?>
  <?php endif; ?>
  <span class="grow">
    <span class="t"><?= e($t['title']) ?></span>
    <span class="meta">
      <?php if ($t['pinned']): ?><span>Hilo fijado</span><span aria-hidden="true">·</span><?php endif; ?>
      <span class="tag c-<?= color($t['sub_color']) ?>"><?= e($t['sub_name']) ?></span>
      <span><?= display_name($t) ?> · <?= e(time_ago($t['last_post_at'])) ?></span>
      <?php if ($t['locked']): ?><span class="tag"><?= icon('lock', 14) ?>Cerrado</span><?php endif; ?>
    </span>
  </span>
  <span class="n count"><?= icon('chat', 22) ?><?= (int) $t['reply_count'] ?><span class="sr"> <?= (int) $t['reply_count'] === 1 ? 'respuesta' : 'respuestas' ?></span></span>
</a>
