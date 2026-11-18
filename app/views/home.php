<?php
$me = user();
$tile = function (array $f, string $extra = ''): string {
    return '<a class="tile c-' . color($f['color']) . ' ' . $extra . '" href="' . e(url('/foro/' . $f['slug'])) . '">'
        . '<span class="pic' . ($extra === 'wide' ? '" style="width:84px;height:84px;border-radius:26px' : '') . '">' . icon($f['icon'], $extra === 'wide' ? 48 : 44) . '</span>'
        . ($extra === 'wide' ? '<span><b>' . e($f['name']) . '</b><small>' . e($f['description']) . '</small></span>'
            : '<b>' . e($f['name']) . '</b><small>' . e($f['description']) . '</small>')
        . '</a>';
};
?>
<section class="wrap hero">
  <div class="txt">
    <span class="pill c-mint"><?= icon('inf', 18) ?>Foro sobre autismo y neurodivergencia</span>
    <h1>Aquí puedes hablar <em>a tu manera.</em></h1>
    <p class="lead" style="font-size:1.24rem">Un foro en español para personas autistas, familias y profesionales. Es claro, es visual y cada persona responde cuando puede.</p>
    <div class="row" style="gap:14px">
      <?php if ($me): ?>
        <a class="btn btn-primary btn-lg" href="#foros">Elegir un foro <?= icon('arrow', 22) ?></a>
        <a class="btn btn-lg" href="<?= e(url('/perfil')) ?>">Ir a mi perfil</a>
      <?php else: ?>
        <a class="btn btn-primary btn-lg" href="<?= e(url('/registro')) ?>">Crear mi cuenta gratis <?= icon('arrow', 22) ?></a>
        <a class="btn btn-lg" href="#foros">Ver los foros</a>
      <?php endif; ?>
    </div>
    <?php if (!$me): ?>
      <p class="row muted" style="gap:8px"><?= icon('check', 20, 'style="color:var(--teal);stroke-width:2.4"') ?>Crear tu usuario es gratis. Solo necesitas un nombre y un correo.</p>
    <?php endif; ?>
  </div>
  <div class="board" role="img" aria-label="Tablero de pictogramas: comunidad, hablar, querer, familia, aprender y amistad">
    <div class="big"><?= icon('inf') ?><span>Comunidad</span></div>
    <div class="c-sky r1"><?= icon('chat-lines') ?><span>Hablar</span></div>
    <div class="c-pink"><?= icon('heart') ?><span>Querer</span></div>
    <div class="c-coral r2"><?= icon('house') ?><span>Familia</span></div>
    <div class="c-green"><?= icon('book') ?><span>Aprender</span></div>
    <div class="c-lav r3"><?= icon('users') ?><span>Amistad</span></div>
  </div>
</section>

<section id="foros" class="band">
  <div class="wrap">
    <div class="row between" style="align-items:flex-end;gap:20px">
      <div class="stack" style="gap:10px">
        <h2 style="font-size:clamp(1.9rem,3.4vw,2.6rem)">Elige tu foro</h2>
        <p class="lead">Toca una ficha para entrar. Dentro verás los subforos y las conversaciones.</p>
      </div>
      <form role="search" action="<?= e(url('/buscar')) ?>" class="row" style="flex:0 1 380px;flex-wrap:nowrap;gap:8px">
        <label class="sr" for="q">Buscar en los foros</label>
        <input id="q" name="q" type="search" placeholder="Buscar en los foros">
        <button class="btn btn-primary btn-flat" style="min-height:54px;padding:0 16px" type="submit" aria-label="Buscar"><?= icon('search', 24) ?></button>
      </form>
    </div>

    <div class="stack" style="gap:18px">
      <h3 class="h-num" style="font-size:1.2rem"><span class="num" style="width:34px;height:34px">1</span>Según quién eres</h3>
      <div class="grid"><?php foreach ($groups['quien'] as $f) echo $tile($f); ?></div>
    </div>
    <div class="stack" style="gap:18px">
      <h3 class="h-num" style="font-size:1.2rem"><span class="num" style="width:34px;height:34px">2</span>Según tu etapa de vida</h3>
      <div class="grid" style="--min:300px"><?php foreach ($groups['etapa'] as $f) echo $tile($f, 'wide'); ?></div>
    </div>
    <div class="stack" style="gap:18px">
      <h3 class="h-num" style="font-size:1.2rem"><span class="num" style="width:34px;height:34px">3</span>Según el tema</h3>
      <div class="grid"><?php foreach ($groups['tema'] as $f) echo $tile($f); ?></div>
    </div>
  </div>
</section>

<section class="wrap stack-lg" style="padding-block:72px;gap:32px">
  <div class="stack" style="gap:10px">
    <h2 style="font-size:clamp(1.9rem,3.4vw,2.6rem)">Además del foro</h2>
    <p class="lead">Tres zonas más. Cada una tiene su color y su dibujo.</p>
  </div>
  <div class="row" style="align-items:stretch;gap:24px">
    <div class="card tint pop c-pink stack" style="flex:2 1 520px;min-width:0;border-radius:36px;padding:36px;gap:22px">
      <div class="row" style="gap:16px;flex-wrap:nowrap">
        <span class="pic"><?= icon('heart', 42) ?></span>
        <div><h3 style="font-weight:800;font-size:1.75rem">Amor neurodivergente</h3><b style="font-family:var(--head);color:var(--tx)">Empecemos por la amistad</b></div>
      </div>
      <p style="font-size:1.06rem;max-width:36em">Preséntate y conoce a personas como tú. Puedes buscar amistad, un grupo de amigos o a alguien especial.</p>
      <div class="card row" style="border:0;border-radius:24px;padding:22px 24px;gap:10px 8px;font-size:1.1rem">
        <span>Me llamo</span><span class="blank c-pink">Lucía</span><span>, tengo</span><span class="blank c-amber">29 años</span><span>, soy de</span><span class="blank c-sky">Granada</span><span>y busco</span><span class="blank c-green">un grupo de amigos</span><span>.</span>
      </div>
      <div><a class="btn btn-color" href="<?= e(url('/amor')) ?>">Conocer gente <?= icon('arrow', 20) ?></a></div>
    </div>
    <div class="card tint pop c-green stack" style="flex:1 1 320px;min-width:0;border-radius:36px;padding:36px;gap:20px">
      <span class="pic"><?= icon('doc', 42) ?></span>
      <h3 style="font-weight:800;font-size:1.65rem">Recursos para descargar</h3>
      <p style="font-size:1.06rem">Guías, plantillas y pictogramas que ayudan en el día a día.</p>
      <div class="row" style="gap:10px"><span class="blank" style="background:var(--surface)">Guías</span><span class="blank" style="background:var(--surface)">Plantillas</span><span class="blank" style="background:var(--surface)">Pictogramas</span></div>
      <div style="margin-top:auto"><a class="btn btn-color" href="<?= e(url('/recursos')) ?>">Ver recursos <?= icon('arrow', 20) ?></a></div>
    </div>
  </div>
  <div class="card dark row" style="border-radius:36px;padding:36px;gap:24px 32px">
    <span class="pic c-amber" style="width:84px;height:84px;border-radius:26px;background:var(--gold);color:var(--ink)"><?= icon('shield', 48) ?></span>
    <div class="stack" style="flex:1 1 420px;min-width:0;gap:8px">
      <div class="row"><h3 style="font-weight:800;font-size:1.65rem">Zona de profesionales</h3><span class="pill gold"><?= icon('lock', 16) ?>Zona de pago</span></div>
      <p style="font-size:1.06rem;max-width:40em">Para profesionales de psicología, psiquiatría y terapia. Tu ficha verificada es visible para toda la comunidad.</p>
    </div>
    <div class="row">
      <a class="btn btn-gold" href="<?= e(url('/profesionales')) ?>">Soy profesional <?= icon('arrow', 20) ?></a>
      <a class="btn" href="<?= e(url('/profesionales/directorio')) ?>">Buscar un profesional</a>
    </div>
  </div>
</section>

<section class="band">
  <div class="wrap" style="gap:32px">
    <h2 style="font-size:clamp(1.9rem,3.4vw,2.6rem)">Cómo funciona</h2>
    <div class="grid" style="--min:300px;gap:24px">
      <?php foreach ([['user', 'Crea tu usuario', 'Elige un nombre, una foto o un dibujo y escribe tu biografía. Puedes cambiarlo cuando quieras.'], ['grid', 'Elige una ficha', 'Cada foro es una ficha con un dibujo. Dentro hay subforos más concretos.'], ['chat-lines', 'Lee y escribe', 'Abre un hilo o responde a otros. No hay prisa: responde cuando te venga bien.']] as $i => [$ic, $h, $p]): ?>
        <div class="card stack" style="background:var(--ground);gap:14px">
          <div class="row" style="gap:14px"><span class="num gold" style="width:56px;height:56px"><?= $i + 1 ?></span><?= icon($ic, 44, 'style="color:var(--teal)"') ?></div>
          <h3 style="font-size:1.3rem"><?= e($h) ?></h3>
          <p class="muted"><?= e($p) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
    <h2 style="margin-top:24px">Un espacio pensado para ti</h2>
    <div class="grid" style="--min:250px">
      <?php foreach ([['lines', 'mint', 'Lenguaje claro', 'Frases cortas y botones que dicen lo que hacen.'], ['moon', 'sky', 'Modo calma', 'Menos color y sin animaciones, con un botón.'], ['calendar', 'amber', 'Todo en su sitio', 'El menú y los botones están siempre en el mismo lugar.'], ['shield', 'coral', 'Respeto', 'Normas claras. Puedes avisar o bloquear a quien las rompa.']] as [$ic, $col, $h, $p]): ?>
        <div class="row c-<?= $col ?>" style="align-items:flex-start;flex-wrap:nowrap;gap:14px;padding:20px;border-radius:22px;background:var(--bg)">
          <?= icon($ic, 34, 'style="color:var(--ic)"') ?>
          <div><b style="font-family:var(--head);font-size:1.06rem;display:block"><?= e($h) ?></b><span class="small"><?= e($p) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php if (!$me): ?>
<section class="wrap" style="padding-block:72px">
  <div class="cta">
    <div class="stack" style="flex:1 1 420px;min-width:0;gap:8px">
      <h2 style="font-size:clamp(1.9rem,3.4vw,2.6rem)">Crea tu usuario y preséntate</h2>
      <p style="font-size:1.15rem">Tardas dos minutos. Después eliges tu primer foro.</p>
    </div>
    <a class="btn btn-dark btn-lg" href="<?= e(url('/registro')) ?>">Crear mi cuenta gratis <?= icon('arrow', 22) ?></a>
  </div>
</section>
<?php else: ?><div style="height:64px"></div><?php endif; ?>
