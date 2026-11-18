<?php
$me = user();
$forumSlug = $t['forum_slug'] ?: $t['sub_slug'];
$threadUrl = '/hilo/' . $t['id'] . '-' . $t['slug'];
$toneIcon = ['serio' => 'lines', 'broma' => 'spark', 'pregunta' => 'question', 'desahogo' => 'heart'];
?>
<div class="wrap page" style="padding-top:20px;gap:24px">
  <nav class="crumbs" aria-label="Ruta">
    <a href="<?= e(url('/')) ?>">Inicio</a><?= icon('chevron', 16, 'stroke-width="2.4"') ?>
    <?php if ($t['forum_slug']): ?>
      <a href="<?= e(url('/foro/' . $t['forum_slug'])) ?>"><?= e($t['forum_name']) ?></a><?= icon('chevron', 16, 'stroke-width="2.4"') ?>
      <a href="<?= e(url('/foro/' . $t['forum_slug'] . '/' . $t['sub_slug'])) ?>"><?= e($t['sub_name']) ?></a>
    <?php else: ?>
      <a href="<?= e(url('/foro/' . $t['sub_slug'])) ?>"><?= e($t['sub_name']) ?></a>
    <?php endif; ?>
  </nav>

  <div class="split">
    <div class="main stack" style="gap:24px">
      <div class="stack" style="gap:14px">
        <h1 style="font-size:clamp(1.8rem,3.2vw,2.5rem);line-height:1.15;overflow-wrap:anywhere"><?= e($t['title']) ?></h1>
        <div class="row" style="gap:10px;font-size:.94rem">
          <span class="tag c-<?= color($t['sub_color']) ?>" style="padding:4px 12px"><?= e($t['sub_name']) ?></span>
          <span class="row muted count" style="gap:6px;font-weight:700"><?= icon('chat', 20) ?><?= plural((int) $t['reply_count'], 'respuesta', 'respuestas') ?></span>
          <span class="row muted" style="gap:6px;font-weight:700"><?= icon('clock', 20) ?>Abierto <?= e(time_ago($t['created_at'])) ?></span>
          <?php if ($t['pinned']): ?><span class="tag c-amber"><?= icon('pin', 14) ?>Fijado</span><?php endif; ?>
          <?php if ($t['locked']): ?><span class="tag"><?= icon('lock', 14) ?>Cerrado</span><?php endif; ?>
        </div>
      </div>

      <?php foreach ($posts as $i => $p):
          $isMine = $me && (int) $p['user_id'] === (int) $me['id'];
          $ids = array_intersect_key(IDENTITIES, array_flip(csv_list($p['identities'])));
          unset($ids['no_decir']);
          $sub = $p['is_pro'] ? trim($p['profession'] . ' · ' . $p['pro_city'], ' ·') : implode(' · ', array_slice($ids, 0, 2));
      ?>
        <?php if ($i === 0 && $pg['page'] === 1 && $p['is_first']): ?>
          <?php /* Primer mensaje */ ?>
        <?php elseif ($i === 0 || ($i === 1 && $pg['page'] === 1)): ?>
          <h2 style="margin-top:8px">Respuestas</h2>
        <?php endif; ?>

        <article class="post<?= $p['is_pro'] ? ' is-pro' : '' ?>" id="post-<?= (int) $p['id'] ?>">
          <div class="who">
            <?= avatar($p, $p['is_first'] ? 60 : 52) ?>
            <div class="grow" style="flex:1 1 200px">
              <a class="name" href="<?= e(url('/u/' . $p['username'])) ?>"><?= display_name($p) ?></a>
              <div class="small muted"><?= e(trim($sub . ' · ' . time_ago($p['created_at']), ' ·')) ?><?= $p['edited_at'] ? ' · editado' : '' ?></div>
            </div>
            <?php if ($p['is_pro']): ?>
              <a class="pill gold" style="text-decoration:none" href="<?= e(url('/profesionales/ver/' . $p['username'])) ?>"><?= icon('shield', 18, 'stroke-width="2.2"') ?>Profesional verificado</a>
            <?php elseif (in_array($p['role'], ['mod', 'admin'], true)): ?>
              <span class="pill c-mint"><?= icon('shield', 18) ?>Moderación</span>
            <?php endif; ?>
            <span class="pill c-<?= $p['tone'] === 'broma' ? 'amber' : ($p['tone'] === 'pregunta' ? 'sky' : ($p['tone'] === 'desahogo' ? 'pink' : 'sand')) ?>"><?= icon($toneIcon[$p['tone']] ?? 'lines', 18) ?>Tono: <?= e(mb_strtolower(TONES[$p['tone']] ?? TONES['serio'])) ?></span>
          </div>

          <div class="body"><?= format_body($p['body']) ?></div>

          <div class="actions">
            <?php foreach (REACTIONS as $kind => [$label, $ic, $c]): $n = $counts[$p['id']][$kind] ?? 0; $on = !empty($mine[$p['id']][$kind]); ?>
              <form method="post" action="<?= e(url('/post/' . $p['id'] . '/reaccion')) ?>">
                <?= csrf_field() ?><input type="hidden" name="kind" value="<?= $kind ?>">
                <button class="react c-<?= $c ?><?= $on ? ' on' : '' ?>" type="submit" aria-pressed="<?= $on ? 'true' : 'false' ?>"><?= icon($ic, 20) ?><?= e($label) ?><span class="count"> · <?= $n ?></span></button>
              </form>
            <?php endforeach; ?>
            <span class="grow"></span>
            <?php if ($me && !$t['locked'] && !$isMine): ?>
              <button type="button" class="btn btn-link" data-reply-to="<?= e($p['username']) ?>">Responder a <?= display_name($p) ?></button>
            <?php endif; ?>
            <?php if ($isMine || is_mod()): ?>
              <a class="btn btn-link" href="<?= e(url('/post/' . $p['id'] . '/editar')) ?>"><?= icon('pencil', 20) ?>Editar</a>
              <form method="post" action="<?= e(url('/post/' . $p['id'] . '/borrar')) ?>" data-confirm="<?= $p['is_first'] ? '¿Borrar el hilo completo? No se puede deshacer.' : '¿Borrar este mensaje? No se puede deshacer.' ?>">
                <?= csrf_field() ?><button class="btn btn-link btn-danger" type="submit"><?= icon('trash', 20) ?>Borrar</button>
              </form>
            <?php endif; ?>
            <?php if ($me && !$isMine): ?>
              <details>
                <summary class="btn btn-link" style="display:inline-flex"><?= icon('flag', 20) ?>Avisar a moderación</summary>
                <form method="post" action="<?= e(url('/avisar')) ?>" class="stack" style="margin-top:12px;min-width:min(420px,80vw)">
                  <?= csrf_field() ?><input type="hidden" name="type" value="post"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                  <label for="reason-<?= (int) $p['id'] ?>"><b>¿Qué ocurre con este mensaje?</b></label>
                  <textarea id="reason-<?= (int) $p['id'] ?>" name="reason" rows="2" maxlength="500" style="min-height:80px"></textarea>
                  <div><button class="btn btn-sm btn-dark" type="submit">Enviar el aviso</button></div>
                </form>
              </details>
            <?php endif; ?>
          </div>
        </article>

        <?php if ($p['is_first'] && $pg['page'] === 1): ?>
          <?php if ($t['summary']): ?>
            <section class="card tint c-mint stack" style="gap:12px;padding:24px 28px" aria-labelledby="resumen">
              <h2 id="resumen" class="row" style="font-size:1.15rem;gap:10px"><?= icon('lines', 26, 'style="color:var(--ic)"') ?>Resumen del hilo</h2>
              <div class="prose" style="font-size:1rem;gap:6px"><?= format_body($t['summary']) ?></div>
            </section>
          <?php endif; ?>
          <?php if ($me): ?>
            <form method="post" action="<?= e(url('/hilo/' . $t['id'] . '/guardar')) ?>">
              <?= csrf_field() ?><button class="btn btn-sm" type="submit" aria-pressed="<?= $saved ? 'true' : 'false' ?>"><?= icon('bookmark', 20) ?><?= $saved ? 'Quitar de mis guardados' : 'Guardar este hilo' ?></button>
            </form>
          <?php endif; ?>
        <?php endif; ?>
      <?php endforeach; ?>

      <?php partial('pager', ['pg' => $pg]); ?>

      <?php if ($t['locked'] && !is_mod()): ?>
        <div class="card row"><?= icon('lock', 26) ?><b>Este hilo está cerrado. No admite más respuestas.</b></div>
      <?php elseif ($me): ?>
        <form id="responder" method="post" action="<?= e(url('/hilo/' . $t['id'] . '/responder')) ?>" class="card stack" style="border-color:var(--teal);gap:18px" aria-labelledby="responder-t">
          <?= csrf_field() ?>
          <h2 id="responder-t">Escribe tu respuesta</h2>
          <div class="field">
            <label for="respuesta">Tu mensaje</label>
            <textarea id="respuesta" name="body" rows="5" required maxlength="10000" placeholder="Escribe aquí. Las frases cortas se leen mejor."></textarea>
          </div>
          <fieldset>
            <legend>¿Con qué tono lo dices? Así nadie tiene que adivinarlo.</legend>
            <div class="choices">
              <?php foreach (TONES as $k => $label): ?>
                <label class="choice"><input type="radio" name="tone" value="<?= $k ?>"<?= $k === 'serio' ? ' checked' : '' ?>><span><?= e($label) ?></span></label>
              <?php endforeach; ?>
            </div>
          </fieldset>
          <div class="row" style="gap:14px">
            <button class="btn btn-primary btn-lg" type="submit">Publicar respuesta</button>
            <span class="help">Puedes editar tu mensaje después de publicarlo.</span>
          </div>
        </form>
      <?php else: ?>
        <div id="responder" class="card stack" style="align-items:flex-start">
          <h2>¿Quieres responder?</h2>
          <p class="muted">Para escribir necesitas una cuenta. Es gratis.</p>
          <div class="row"><a class="btn btn-primary" href="<?= e(url('/registro')) ?>">Crear mi cuenta</a><a class="btn" href="<?= e(url('/entrar')) ?>">Entrar</a></div>
        </div>
      <?php endif; ?>
    </div>

    <aside class="side stack" style="gap:20px">
      <?php if ($similar): ?>
      <div class="card stack" style="padding:24px;border-radius:26px;gap:6px">
        <h2 style="font-size:1.15rem;margin-bottom:8px">Otros hilos de este subforo</h2>
        <?php foreach ($similar as $s): ?>
          <a href="<?= e(thread_url($s)) ?>" style="min-height:44px;display:flex;align-items:center;font-weight:700;line-height:1.35;padding:6px 0"><?= e($s['title']) ?></a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
      <div class="card tint c-green stack" style="padding:24px;border-radius:26px;gap:14px">
        <span class="pic xs" style="width:52px;height:52px;border-radius:16px;background:var(--surface)"><?= icon('doc', 30) ?></span>
        <h2 style="font-size:1.15rem">Recursos para descargar</h2>
        <p class="small">Guías, plantillas y pictogramas que ayudan en el día a día.</p>
        <div><a class="btn btn-color btn-flat" style="min-height:48px" href="<?= e(url('/recursos')) ?>">Ver recursos</a></div>
      </div>
      <?php if (is_mod()): ?>
      <div class="card stack" style="padding:24px;border-radius:26px">
        <h2 style="font-size:1.15rem">Moderación</h2>
        <form method="post" action="<?= e(url('/hilo/' . $t['id'] . '/moderar')) ?>" class="row">
          <?= csrf_field() ?>
          <button class="btn btn-sm" name="do" value="pin"><?= icon('pin', 20) ?><?= $t['pinned'] ? 'Quitar fijado' : 'Fijar hilo' ?></button>
          <button class="btn btn-sm" name="do" value="lock"><?= icon('lock', 20) ?><?= $t['locked'] ? 'Abrir hilo' : 'Cerrar hilo' ?></button>
        </form>
        <form method="post" action="<?= e(url('/hilo/' . $t['id'] . '/moderar')) ?>" class="stack">
          <?= csrf_field() ?><input type="hidden" name="do" value="summary">
          <label for="summary"><b>Resumen del hilo</b></label>
          <textarea id="summary" name="summary" rows="4" placeholder="Dos o tres frases con lo más útil. Una por línea."><?= e((string) $t['summary']) ?></textarea>
          <div><button class="btn btn-sm btn-dark" type="submit">Guardar resumen</button></div>
        </form>
      </div>
      <?php endif; ?>
    </aside>
  </div>
</div>
