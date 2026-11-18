<?php
$ids = array_intersect_key(IDENTITIES, array_flip(csv_list($u['identities']))); unset($ids['no_decir']);
$tagCols = ['mint', 'coral', 'sky', 'amber', 'pink', 'lav'];
$isMe = uid() === (int) $u['id'];
?>
<div class="wrap page">
  <div class="split">
    <aside class="side stack" style="flex:1 1 300px;gap:16px">
      <div class="card pop c-lav stack center" style="align-items:center;gap:14px;border-color:var(--bd)">
        <?= avatar($u, 120) ?>
        <h1 style="font-size:1.7rem;overflow-wrap:anywhere"><?= display_name($u) ?></h1>
        <?php if ($u['is_pro']): ?><a class="pill gold" style="text-decoration:none" href="<?= e(url('/profesionales/ver/' . $u['username'])) ?>"><?= icon('shield', 18) ?>Profesional verificado</a><?php endif; ?>
        <?php if ($ids): ?><div class="row" style="justify-content:center;gap:8px"><?php $i = 0; foreach ($ids as $label): ?><span class="tag c-<?= $tagCols[$i++ % 6] ?>" style="padding:5px 12px"><?= e($label) ?></span><?php endforeach; ?></div><?php endif; ?>
        <p class="muted" style="overflow-wrap:anywhere"><?= $u['bio'] !== '' ? e($u['bio']) : 'Esta persona todavía no ha escrito su biografía.' ?></p>
        <span class="small muted">En el foro desde <?= e(time_ago($u['created_at'])) ?></span>
        <?php if ($isMe): ?><a class="btn btn-primary" href="<?= e(url('/perfil')) ?>"><?= icon('pencil', 20) ?>Editar mi perfil</a><?php endif; ?>
      </div>
      <?php if (uid() && !$isMe): ?>
        <div class="card stack" style="padding:20px;gap:10px">
          <form method="post" action="<?= e(url('/bloquear/' . $u['id'])) ?>">
            <?= csrf_field() ?><button class="btn btn-sm" type="submit" style="width:100%"><?= icon('lock', 20) ?><?= $blocked ? 'Desbloquear a esta persona' : 'Bloquear a esta persona' ?></button>
          </form>
          <details>
            <summary class="btn btn-sm" style="display:flex"><?= icon('flag', 20) ?>Avisar a moderación</summary>
            <form method="post" action="<?= e(url('/avisar')) ?>" class="stack" style="margin-top:12px">
              <?= csrf_field() ?><input type="hidden" name="type" value="user"><input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
              <label for="reason"><b>¿Qué ocurre con esta persona?</b></label>
              <textarea id="reason" name="reason" rows="3" maxlength="500"></textarea>
              <div><button class="btn btn-sm btn-dark" type="submit">Enviar el aviso</button></div>
            </form>
          </details>
        </div>
      <?php endif; ?>
    </aside>
    <div class="main stack">
      <h2>Hilos de <?= display_name($u) ?></h2>
      <?php if (!$threads): ?><div class="card"><p class="muted">Todavía no ha abierto ningún hilo.</p></div><?php endif; ?>
      <div class="stack" style="gap:12px"><?php foreach ($threads as $t) partial('trow', ['t' => $t]); ?></div>
    </div>
  </div>
</div>
