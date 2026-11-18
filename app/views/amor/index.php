<?php
$me = user();
$m = $mine ?? [];
$myInterests = csv_list($m['interests'] ?? '');
$others = array_diff($myInterests, INTERESTS);
$filterUrl = fn (string $busca): string => url('/amor') . '?' . http_build_query(array_filter(['busca' => $busca, 'ciudad' => $city, 'edad' => $age], 'strlen')) . '#gente';
$lookCol = ['amistad' => 'mint', 'grupo' => 'sky', 'pareja' => 'pink'];
?>
<div class="wrap page" style="gap:40px">
  <section class="hero-band c-pink" style="border-radius:40px;padding:clamp(24px,4vw,44px) clamp(20px,4vw,40px);gap:32px 48px">
    <div class="stack" style="flex:1 1 440px;min-width:0;align-items:flex-start">
      <span class="pill" style="background:var(--surface);font-family:var(--head);font-size:.95rem">Empecemos por la amistad</span>
      <h1 style="font-size:clamp(2.3rem,4.6vw,3.6rem)">Amor neurodivergente</h1>
      <p style="font-size:1.2rem;max-width:32em">Un lugar para conocer a personas neurodivergentes como tú. Tú decides qué buscas, qué cuentas y a qué ritmo.</p>
      <div class="row" style="gap:14px;margin-top:4px">
        <a class="btn btn-color btn-lg" href="<?= e($me ? '#presentarme' : url('/registro')) ?>"><?= icon('pencil', 22, 'stroke-width="2.2"') ?><?= $mine ? 'Cambiar mi presentación' : 'Escribir mi presentación' ?></a>
        <a class="btn btn-lg btn-flat" style="border-color:var(--bd)" href="#gente">Ver a otras personas</a>
      </div>
    </div>
    <div aria-hidden="true" class="row" style="flex:0 1 420px;min-width:240px;justify-content:center;flex-wrap:nowrap;gap:14px">
      <div class="c-sky" style="flex:1 1 0;aspect-ratio:1;max-width:160px;background:var(--bg);border:2px solid var(--bd);border-radius:32px;box-shadow:0 6px 0 var(--bd);display:flex;align-items:center;justify-content:center;color:var(--ic);transform:rotate(-4deg)"><?= icon('user', 80) ?></div>
      <div style="width:76px;height:76px;border-radius:50%;background:var(--surface);border:2px solid var(--bd);display:flex;align-items:center;justify-content:center;color:var(--ic);flex:none"><?= icon('heart', 42, 'stroke-width="2.1"') ?></div>
      <div class="c-amber" style="flex:1 1 0;aspect-ratio:1;max-width:160px;background:var(--bg);border:2px solid var(--bd);border-radius:32px;box-shadow:0 6px 0 var(--bd);display:flex;align-items:center;justify-content:center;color:var(--ic);transform:rotate(4deg)"><?= icon('user', 80) ?></div>
    </div>
  </section>

  <?php if ($me): ?>
  <section id="presentarme" class="stack" style="gap:18px;scroll-margin-top:16px">
    <h2 class="h-num"><span class="num">1</span><?= $mine ? 'Tu presentación' : 'Preséntate completando la frase' ?></h2>
    <form method="post" action="<?= e(url('/amor/presentacion')) ?>" class="card stack" style="border-radius:32px;padding:clamp(20px,3vw,32px);gap:26px">
      <?= csrf_field() ?>
      <div class="sentence">
        <label class="c-pink">Me llamo <input type="text" name="first_name" required maxlength="40" style="width:160px" value="<?= e($m['first_name'] ?? '') ?>"></label>
        <label class="c-amber">, tengo <input type="number" name="age" required min="18" max="110" style="width:96px" value="<?= e($m['age'] ?? '') ?>"> años</label>
        <label class="c-sky">, soy de <input type="text" name="city" required maxlength="60" style="width:180px" value="<?= e($m['city'] ?? '') ?>"></label>
        <label class="c-green">y busco
          <select name="looking"><?php foreach (LOOKING as $k => [, $phrase]): ?><option value="<?= $k ?>"<?= ($m['looking'] ?? 'amistad') === $k ? ' selected' : '' ?>><?= e($phrase) ?></option><?php endforeach; ?></select>
        </label>
      </div>
      <div class="field">
        <label for="body">Cuenta algo más (opcional)</label>
        <textarea id="body" name="body" rows="3" maxlength="400" data-count="body-n" placeholder="Qué te gusta hacer, cómo es un buen plan para ti…"><?= e($m['body'] ?? '') ?></textarea>
        <span class="help" id="body-n" aria-live="polite"></span>
      </div>
      <fieldset>
        <legend>Me gusta hablar de… (elige los temas que quieras)</legend>
        <div class="choices">
          <?php foreach (INTERESTS as $int): ?>
            <label class="choice"><input type="checkbox" name="interests[]" value="<?= e($int) ?>"<?= in_array($int, $myInterests, true) ? ' checked' : '' ?>><span><?= e($int) ?></span></label>
          <?php endforeach; ?>
        </div>
        <div class="field" style="margin-top:14px;max-width:520px">
          <label for="other_interests" style="font-family:var(--body);font-size:1rem">Otros temas, separados por comas</label>
          <input id="other_interests" name="other_interests" type="text" maxlength="120" value="<?= e(implode(', ', $others)) ?>" placeholder="Por ejemplo: mapas, trenes, cerámica">
        </div>
      </fieldset>
      <fieldset>
        <legend>Prefiero comunicarme por…</legend>
        <div class="choices">
          <?php foreach (COMM as $k => [$label, , $ic]): ?>
            <label class="choice"><input type="radio" name="comm" value="<?= $k ?>"<?= ($m['comm'] ?? 'texto') === $k ? ' checked' : '' ?>><span><?= icon($ic, 20) ?><?= e($label) ?></span></label>
          <?php endforeach; ?>
        </div>
      </fieldset>
      <div class="row" style="gap:14px">
        <button class="btn btn-color c-pink btn-lg" type="submit"><?= $mine ? 'Guardar mi presentación' : 'Publicar mi presentación' ?></button>
        <span class="help">Solo la ven las personas que han entrado en su cuenta. Es solo para mayores de 18 años.</span>
      </div>
    </form>
    <?php if ($mine): ?>
      <form method="post" action="<?= e(url('/amor/presentacion/borrar')) ?>" data-confirm="¿Borrar tu presentación? Dejarás de aparecer en la lista.">
        <?= csrf_field() ?><button class="btn btn-link btn-danger" type="submit"><?= icon('trash', 20) ?>Borrar mi presentación</button>
      </form>
    <?php endif; ?>
  </section>
  <?php endif; ?>

  <section id="gente" class="stack" style="gap:18px;scroll-margin-top:16px">
    <h2 class="h-num"><span class="num"><?= $me ? '2' : '1' ?></span>¿Qué buscas?</h2>
    <div class="grid" style="--min:300px;gap:20px" role="group" aria-label="Filtrar por lo que buscan">
      <?php foreach (LOOKING as $k => [$label, , $desc, $ic, $c]): ?>
        <a class="tile wide c-<?= $c ?><?= $looking === $k ? ' on' : '' ?>" href="<?= e($filterUrl($looking === $k ? '' : $k)) ?>"<?= $looking === $k ? ' aria-current="true"' : '' ?>>
          <span class="pic" style="width:84px;height:84px;border-radius:26px"><?= icon($ic, 48) ?></span><span><b><?= e($label) ?></b><small><?= e($desc) ?></small></span>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="stack" style="gap:18px">
    <div class="row between">
      <h2 class="h-num"><span class="num"><?= $me ? '3' : '2' ?></span>Conoce a otras personas</h2>
      <?php if ($me): ?>
      <form class="row" action="<?= e(url('/amor')) ?>#gente">
        <?php if ($looking): ?><input type="hidden" name="busca" value="<?= e($looking) ?>"><?php endif; ?>
        <label class="row" style="gap:8px;font-weight:700">Ciudad <input type="text" name="ciudad" value="<?= e($city) ?>" style="width:170px;min-height:48px;background:var(--surface)" placeholder="Todas"></label>
        <label class="row" style="gap:8px;font-weight:700">Edad
          <select name="edad" style="width:auto;min-height:48px;background:var(--surface)">
            <option value="">Todas</option><option value="18-30"<?= $age === '18-30' ? ' selected' : '' ?>>18 a 30</option><option value="31-50"<?= $age === '31-50' ? ' selected' : '' ?>>31 a 50</option><option value="51"<?= $age === '51' ? ' selected' : '' ?>>Más de 50</option>
          </select></label>
        <button class="btn btn-sm btn-dark" style="min-height:48px" type="submit">Filtrar</button>
      </form>
      <?php endif; ?>
    </div>

    <?php if (!$me): ?>
      <div class="card stack" style="align-items:flex-start">
        <span class="pic xs c-pink"><?= icon('lock', 26) ?></span>
        <h3 style="font-size:1.3rem">Las presentaciones solo se ven con una cuenta</h3>
        <p class="lead">Así protegemos a las personas que se presentan aquí. Crear tu cuenta es gratis.</p>
        <div class="row"><a class="btn btn-primary" href="<?= e(url('/registro')) ?>">Crear mi cuenta</a><a class="btn" href="<?= e(url('/entrar')) ?>">Entrar</a></div>
      </div>
    <?php elseif (!$people): ?>
      <div class="card stack" style="align-items:flex-start">
        <h3 style="font-size:1.3rem"><?= ($looking || $city !== '' || $age) ? 'No hay presentaciones con ese filtro' : 'Todavía no hay presentaciones' ?></h3>
        <p class="lead"><?= ($looking || $city !== '' || $age) ? 'Prueba a quitar algún filtro.' : 'Puedes ser la primera persona en presentarse.' ?></p>
      </div>
    <?php else: ?>
      <div class="grid fill" style="--min:340px;gap:24px">
        <?php foreach ($people as $p): $state = $sent[$p['user_id']] ?? ''; ?>
          <article class="card person" style="padding:24px">
            <div class="row" style="gap:14px;flex-wrap:nowrap">
              <?= avatar($p, 72) ?>
              <div style="min-width:0">
                <h3 style="font-size:1.3rem;overflow-wrap:anywhere"><?= e($p['first_name']) ?>, <?= (int) $p['age'] ?></h3>
                <span class="row muted" style="gap:6px"><?= icon('pin', 18) ?><?= e($p['city']) ?></span>
              </div>
            </div>
            <span class="tag c-<?= $lookCol[$p['looking']] ?>" style="align-self:flex-start;padding:6px 14px;font-size:1rem">Busca <?= e(LOOKING[$p['looking']][1]) ?></span>
            <?php if ($p['body'] !== ''): ?><p style="overflow-wrap:anywhere"><?= e($p['body']) ?></p><?php endif; ?>
            <?php if ($p['interests'] !== ''): ?><div class="row" style="gap:8px"><?php foreach (csv_list($p['interests']) as $int): ?><span class="tag" style="padding:5px 12px"><?= e($int) ?></span><?php endforeach; ?></div><?php endif; ?>
            <span class="row muted small" style="gap:8px"><?= icon(COMM[$p['comm']][2], 20) ?><?= e(COMM[$p['comm']][1]) ?></span>

            <?php if ($state === 'accepted'): ?>
              <a class="btn btn-flat" href="<?= e(url('/mensajes')) ?>"><?= icon('mail', 20) ?>Ya podéis escribiros</a>
            <?php elseif ($state === 'sent'): ?>
              <span class="btn btn-flat" style="cursor:default" aria-disabled="true"><?= icon('check', 20) ?>Saludo enviado</span>
            <?php elseif ($state === 'received'): ?>
              <a class="btn btn-primary" href="<?= e(url('/mensajes')) ?>">Te ha saludado. Ver el saludo</a>
            <?php else: ?>
              <form method="post" action="<?= e(url('/amor/saludo/' . $p['user_id'])) ?>" class="stack" style="margin-top:auto;gap:10px">
                <?= csrf_field() ?>
                <label class="sr" for="msg-<?= (int) $p['user_id'] ?>">Mensaje de saludo para <?= e($p['first_name']) ?> (opcional)</label>
                <input id="msg-<?= (int) $p['user_id'] ?>" type="text" name="message" maxlength="300" placeholder="Escribe un saludo (opcional)">
                <button class="btn btn-primary" type="submit">Enviar un saludo a <?= e($p['first_name']) ?></button>
              </form>
            <?php endif; ?>
            <div class="row" style="gap:4px">
              <form method="post" action="<?= e(url('/bloquear/' . $p['user_id'])) ?>" data-confirm="¿Bloquear a esta persona? Dejarás de ver su presentación."><?= csrf_field() ?><button class="btn btn-link small" type="submit"><?= icon('lock', 18) ?>Bloquear</button></form>
              <form method="post" action="<?= e(url('/avisar')) ?>"><?= csrf_field() ?><input type="hidden" name="type" value="presentation"><input type="hidden" name="id" value="<?= (int) $p['user_id'] ?>"><button class="btn btn-link small" type="submit"><?= icon('flag', 18) ?>Avisar a moderación</button></form>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
      <?php partial('pager', ['pg' => $pg]); ?>
    <?php endif; ?>
  </section>

  <section class="card dark stack" style="border-radius:32px;padding:36px;gap:22px">
    <h2>Tu seguridad va primero</h2>
    <div class="grid" style="--min:240px">
      <?php foreach ([['+18', 'Esta zona es solo para personas mayores de 18 años.'], ['eye', 'Tú eliges qué datos se ven. La foto es opcional.'], ['check', 'Nadie puede escribirte hasta que aceptas su saludo.'], ['flag', 'Puedes bloquear a una persona y avisar a moderación con un botón.']] as [$ic, $txt]): ?>
        <div class="row" style="align-items:flex-start;flex-wrap:nowrap"><span class="pic xs" style="width:48px;height:48px;border-radius:16px;background:var(--gold);color:var(--ink);font-family:var(--head);font-weight:800;font-size:.9rem"><?= $ic === '+18' ? '+18' : icon($ic, 26) ?></span><span style="color:#E4E9E5"><?= e($txt) ?></span></div>
      <?php endforeach; ?>
    </div>
  </section>
</div>
