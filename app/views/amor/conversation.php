<?php $o = $c['other']; ?>
<div class="wrap page" style="padding-top:36px">
  <div class="split">
    <?php partial('sidenav', ['tab' => $tab]); ?>
    <div class="stack" style="flex:999 1 520px;min-width:0;gap:20px">
      <nav class="crumbs" aria-label="Ruta"><a href="<?= e(url('/mensajes')) ?>">Mensajes</a><?= icon('chevron', 16, 'stroke-width="2.4"') ?><span aria-current="page"><?= display_name($o) ?></span></nav>
      <div class="row between">
        <div class="row" style="gap:14px"><?= avatar($o, 60) ?><h1 style="font-size:1.8rem"><a href="<?= e(url('/u/' . $o['username'])) ?>" style="color:inherit;text-decoration:none"><?= display_name($o) ?></a></h1></div>
        <div class="row" style="gap:4px">
          <form method="post" action="<?= e(url('/bloquear/' . $o['id'])) ?>"><?= csrf_field() ?><button class="btn btn-link" type="submit"><?= icon('lock', 20) ?><?= $iBlocked ? 'Desbloquear' : 'Bloquear' ?></button></form>
          <form method="post" action="<?= e(url('/avisar')) ?>"><?= csrf_field() ?><input type="hidden" name="type" value="user"><input type="hidden" name="id" value="<?= (int) $o['id'] ?>"><input type="hidden" name="reason" value="Aviso desde una conversación privada"><button class="btn btn-link" type="submit"><?= icon('flag', 20) ?>Avisar a moderación</button></form>
        </div>
      </div>

      <div class="card stack" style="gap:10px;padding:24px" aria-label="Mensajes de la conversación">
        <?php foreach ($messages as $m): $mineMsg = (int) $m['sender_id'] === uid(); ?>
          <div id="m-<?= (int) $m['id'] ?>" class="bubble<?= $mineMsg ? ' mine' : '' ?>">
            <span class="sr"><?= $mineMsg ? 'Tú' : display_name($o) ?>: </span><?= nl2br(e($m['body']), false) ?>
            <div class="small muted" style="margin-top:4px"><?= e(time_ago($m['created_at'])) ?></div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if ($c['blocked'] || $o['banned_at']): ?>
        <div class="card row"><?= icon('lock', 26) ?><b><?= $iBlocked ? 'Has bloqueado a esta persona. Para escribir, desbloquéala.' : 'No puedes escribir en esta conversación.' ?></b></div>
      <?php else: ?>
        <form method="post" class="card stack" style="border-color:var(--teal)">
          <?= csrf_field() ?>
          <div class="field"><label for="body">Tu mensaje</label><textarea id="body" name="body" rows="3" required maxlength="2000"></textarea></div>
          <div class="row"><button class="btn btn-primary" type="submit">Enviar mensaje</button><span class="help">No hay prisa. La otra persona responde cuando puede.</span></div>
        </form>
      <?php endif; ?>
    </div>
  </div>
</div>
