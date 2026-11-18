<?php
$cta = $pro ? '/profesionales/alta' : (user() ? '/profesionales/alta' : '/registro?soy=pro');
$ctaLabel = $pro ? 'Ir a mi ficha profesional' : 'Darme de alta como profesional';
$demo = ['full_name' => 'Marta Ruiz', 'profession' => 'Psicóloga sanitaria', 'city' => 'Madrid', 'online' => 1, 'license_no' => 'M-00000', 'specialties' => 'Autismo en adultos, Diagnóstico, Mujeres', 'avatar_type' => 'icon', 'avatar_value' => 'leaf:amber', 'username' => ''];
?>
<div class="wrap page" style="gap:56px">
  <section class="card dark row" style="border-radius:40px;padding:clamp(24px,4vw,48px) clamp(20px,4vw,44px);gap:36px 56px">
    <div class="stack" style="flex:1 1 460px;min-width:0;align-items:flex-start;gap:18px">
      <span class="pill gold"><?= icon('lock', 18, 'stroke-width="2.2"') ?>Zona de pago · solo para profesionales</span>
      <h1 style="font-size:clamp(2.2rem,4.4vw,3.4rem)">Da a conocer tu consulta a la comunidad <span style="color:var(--gold)">neurodivergente.</span></h1>
      <p style="font-size:1.2rem;max-width:32em">Para profesionales de psicología, psiquiatría, logopedia y terapia ocupacional. Tu ficha verificada aparece en el directorio y tus respuestas llevan sello.</p>
      <div class="row" style="gap:14px;margin-top:4px">
        <a class="btn btn-gold btn-lg" href="<?= e(url($cta)) ?>"><?= e($ctaLabel) ?></a>
        <a class="btn btn-lg btn-flat" style="background:transparent;color:var(--surface);border-color:var(--field)" href="#planes">Ver los planes</a>
      </div>
      <a href="<?= e(url('/profesionales/directorio')) ?>" style="color:var(--surface)"><b>¿Buscas un profesional? Ver el directorio</b></a>
    </div>
    <div style="flex:0 1 400px;min-width:260px;color:var(--ink);transform:rotate(1.5deg);filter:drop-shadow(10px 10px 0 var(--gold))">
      <span class="small" style="display:block;color:#E4E9E5;font-weight:700;text-transform:uppercase;letter-spacing:.06em;margin-bottom:8px">Ejemplo: así se ve tu ficha</span>
      <?php partial('pro_card', ['p' => $demo, 'demo' => true]); ?>
    </div>
  </section>

  <section class="stack" style="gap:22px">
    <h2 style="font-size:clamp(1.8rem,3vw,2.3rem)">Qué incluye</h2>
    <div class="grid" style="--min:340px;gap:20px">
      <?php foreach ([
          ['card', 'amber', 'Ficha en el directorio', 'Las personas del foro te encuentran por ciudad y por especialidad.'],
          ['shield', 'mint', 'Sello de profesional verificado', 'Comprobamos tu número de colegiación antes de publicar la ficha.'],
          ['calendar', 'sky', 'Botón «Pedir cita»', 'Enlaza con tu web, tu teléfono o tu agenda online.'],
          ['upload', 'green', 'Publica recursos con tu nombre', 'Tus guías y plantillas aparecen en la zona de recursos.'],
          ['lock', 'lav', 'Foro privado entre profesionales', 'Para compartir formación, dudas y derivaciones.'],
          ['chat-lines', 'pink', 'Responde con tu sello', 'Tus respuestas en el foro muestran que eres profesional verificado.'],
      ] as [$ic, $c, $h, $p]): ?>
        <div class="card tint c-<?= $c ?> row" style="align-items:flex-start;flex-wrap:nowrap;gap:16px;padding:24px;border-radius:26px">
          <span class="pic sm" style="width:64px;height:64px"><?= icon($ic, 36) ?></span>
          <div class="stack" style="gap:4px"><h3 style="font-size:1.2rem;line-height:1.25"><?= e($h) ?></h3><p class="small"><?= e($p) ?></p></div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="stack" style="gap:22px">
    <h2 style="font-size:clamp(1.8rem,3vw,2.3rem)">Cómo darte de alta</h2>
    <ol class="grid" style="--min:250px;gap:20px;margin:0;padding:0;list-style:none">
      <?php foreach ([['Crea tu cuenta', 'Elige la opción «Soy profesional».'], ['Rellena tu ficha', 'Con tu número de colegiación. Lo comprobamos con tu colegio profesional.'], ['Elige un plan y paga', 'Con tarjeta. Puedes darte de baja cuando quieras.'], ['Publicamos tu ficha', 'Aparece en el directorio cuando está verificada y pagada.']] as $i => [$h, $p]): ?>
        <li class="card stack" style="padding:24px;border-radius:26px;gap:10px"><span class="num gold"><?= $i + 1 ?></span><b style="font-family:var(--head);font-size:1.12rem"><?= e($h) ?></b><span class="small muted"><?= e($p) ?></span></li>
      <?php endforeach; ?>
    </ol>
  </section>

  <section id="planes" class="stack" style="gap:22px;scroll-margin-top:16px">
    <h2 style="font-size:clamp(1.8rem,3vw,2.3rem)">Planes</h2>
    <div class="grid" style="--min:340px;gap:24px;align-items:stretch">
      <?php foreach (PLANS as $key => [$name, $features]): $price = plan_price($key); ?>
        <div class="card plan<?= $key === 'featured' ? ' best' : '' ?>">
          <div class="row between"><h3 style="font-size:1.4rem"><?= e($name) ?></h3><?php if ($key === 'featured'): ?><span class="pill gold">Recomendado</span><?php endif; ?></div>
          <p class="price"><?= $price !== '' ? e($price) . ' <small>al mes</small>' : '<small>Precio a consultar</small>' ?></p>
          <ul><?php foreach ($features as $f): ?><li><?= icon('check', 22) ?><span><?= e($f) ?></span></li><?php endforeach; ?></ul>
          <a class="btn <?= $key === 'featured' ? 'btn-primary' : 'btn-outline' ?>" href="<?= e(url($cta . (str_contains($cta, '?') ? '&' : '?') . 'plan=' . $key)) ?>">Elegir <?= e(mb_strtolower($name)) ?></a>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="card tint c-mint stack" style="border-radius:32px;padding:36px;gap:20px">
    <h2>Normas para profesionales</h2>
    <p style="font-size:1.06rem;max-width:44em">Esta comunidad es un espacio de confianza. Por eso todos los profesionales aceptan tres normas.</p>
    <div class="grid" style="--min:300px">
      <?php foreach (['No escribes a los miembros para ofrecer tus servicios. Ellos te escriben a ti.', 'Tus respuestas en el foro orientan. No son un diagnóstico ni un tratamiento.', 'Solo se anuncian profesionales verificados. Sin sello no hay publicidad.'] as $i => $txt): ?>
        <div class="row" style="align-items:flex-start;flex-wrap:nowrap;gap:14px;padding:20px;background:var(--surface);border-radius:22px"><span class="num"><?= $i + 1 ?></span><span><?= e($txt) ?></span></div>
      <?php endforeach; ?>
    </div>
  </section>
</div>
