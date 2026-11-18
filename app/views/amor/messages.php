<div class="wrap page" style="padding-top:36px">
  <div class="split">
    <?php partial('sidenav', ['tab' => $tab]); ?>
    <div class="stack" style="flex:999 1 520px;min-width:0;gap:28px">
      <div class="stack" style="gap:6px">
        <h1>Mensajes</h1>
        <p class="lead">Aquí están los saludos que recibes y tus conversaciones privadas.</p>
      </div>

      <?php if ($pending): ?>
      <section class="stack">
        <h2>Saludos nuevos</h2>
        <?php foreach ($pending as $g): ?>
          <article class="card tint c-pink stack" style="padding:22px">
            <div class="row" style="gap:14px;flex-wrap:nowrap">
              <?= avatar($g, 60) ?>
              <div style="min-width:0">
                <h3 style="font-size:1.2rem"><?= e($g['first_name'] ?: $g['display_name']) ?><?= $g['age'] ? ', ' . (int) $g['age'] : '' ?></h3>
                <span class="muted"><?= e(trim(($g['city'] ?? '') . ($g['looking'] ? ' · Busca ' . LOOKING[$g['looking']][1] : ''), ' ·')) ?> · <?= e(time_ago($g['created_at'])) ?></span>
              </div>
            </div>
            <?php if ($g['message'] !== ''): ?><p class="card" style="border:0;padding:14px 18px;border-radius:16px;overflow-wrap:anywhere">«<?= e($g['message']) ?>»</p><?php endif; ?>
            <div class="row">
              <form method="post" action="<?= e(url('/amor/saludo/' . $g['id'] . '/responder')) ?>"><?= csrf_field() ?><input type="hidden" name="do" value="accept"><button class="btn btn-primary" type="submit"><?= icon('check', 20, 'stroke-width="2.6"') ?>Aceptar y hablar</button></form>
              <form method="post" action="<?= e(url('/amor/saludo/' . $g['id'] . '/responder')) ?>"><?= csrf_field() ?><input type="hidden" name="do" value="decline"><button class="btn btn-flat" type="submit">No, gracias</button></form>
              <a href="<?= e(url('/u/' . $g['username'])) ?>"><b>Ver su perfil</b></a>
            </div>
            <span class="help">Si dices «No, gracias», la otra persona no recibe ningún aviso.</span>
          </article>
        <?php endforeach; ?>
      </section>
      <?php endif; ?>

      <section class="stack">
        <h2>Conversaciones</h2>
        <?php if (!$convs): ?>
          <div class="card stack" style="align-items:flex-start">
            <p class="lead">Todavía no tienes conversaciones. Empiezan cuando alguien acepta un saludo en «Amor neurodivergente».</p>
            <a class="btn btn-color c-pink" href="<?= e(url('/amor')) ?>"><?= icon('heart', 20) ?>Ir a Amor neurodivergente</a>
          </div>
        <?php endif; ?>
        <div class="stack" style="gap:12px">
          <?php foreach ($convs as $c): ?>
            <a class="trow" href="<?= e(url('/mensajes/' . $c['id'])) ?>">
              <?= avatar($c, 52) ?>
              <span class="grow">
                <span class="t"><?= display_name($c) ?></span>
                <span class="meta"><span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:36ch"><?= e(mb_strimwidth((string) $c['last_body'], 0, 80, '…')) ?></span><span>· <?= e(time_ago($c['last_message_at'])) ?></span></span>
              </span>
              <?php if ($c['unread']): ?><span class="badge"><?= (int) $c['unread'] ?><span class="sr"> sin leer</span></span><?php endif; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    </div>
  </div>
</div>
