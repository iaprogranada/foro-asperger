<?php
$p = $pro ?? [];
$v = fn (string $k, string $d = ''): string => e(old($k, $p[$k] ?? $d));
$plan = old('plan', $p['plan'] ?? (isset(PLANS[$_GET['plan'] ?? '']) ? $_GET['plan'] : 'featured'));
$saved = $pro && !empty($pro['user_id']);
$verified = $saved && !empty($pro['verified_at']);
$paid = $saved && (in_array($pro['sub_status'], ['active', 'trialing'], true) || !empty($pro['manual_active']));
$canPay = stripe_ready() && $saved && stripe_price($pro['plan']) !== '';
?>
<div class="wrap page" style="max-width:980px">
  <div class="stack" style="gap:6px">
    <h1><?= $saved ? 'Mi ficha profesional' : 'Alta como profesional' ?></h1>
    <p class="lead">Rellena tu ficha. Después comprobamos tu número de colegiación y activas el plan.</p>
  </div>

  <?php if ($saved): ?>
  <section id="estado" class="card stack" style="border-radius:32px;gap:18px;scroll-margin-top:16px">
    <h2>Estado de tu alta</h2>
    <div class="grid" style="--min:250px">
      <div class="card tint c-mint stack" style="padding:20px;border-radius:22px;gap:8px"><span class="row"><span class="num"><?= icon('check', 20, 'stroke-width="2.6"') ?></span><b>1. Ficha guardada</b></span><span class="small">Puedes cambiarla cuando quieras.</span></div>
      <div class="card tint c-<?= $verified ? 'mint' : 'amber' ?> stack" style="padding:20px;border-radius:22px;gap:8px"><span class="row"><span class="num"><?= $verified ? icon('check', 20, 'stroke-width="2.6"') : '2' ?></span><b>2. Verificación</b></span><span class="small"><?= $verified ? 'Tu número de colegiación está comprobado.' : 'Estamos comprobando tu número de colegiación. Te avisamos por correo.' ?></span></div>
      <div class="card tint c-<?= $paid ? 'mint' : 'amber' ?> stack" style="padding:20px;border-radius:22px;gap:8px"><span class="row"><span class="num"><?= $paid ? icon('check', 20, 'stroke-width="2.6"') : '3' ?></span><b>3. Pago del plan</b></span><span class="small"><?= $paid ? 'Tu plan «' . e(PLANS[$pro['plan']][0]) . '» está activo.' . (!empty($pro['current_period_end']) ? ' Próxima renovación: ' . e(date('d/m/Y', strtotime($pro['current_period_end']))) . '.' : '') : 'Falta activar el plan «' . e(PLANS[$pro['plan']][0]) . '».' ?></span></div>
    </div>
    <?php if (!empty($pro['is_active'])): ?>
      <div class="flash c-mint"><?= icon('shield', 24) ?><span>Tu ficha está publicada en el directorio.</span></div>
    <?php endif; ?>
    <div class="row">
      <?php if (!$paid && $canPay): ?>
        <form method="post" action="<?= e(url('/profesionales/pagar')) ?>"><?= csrf_field() ?><button class="btn btn-gold btn-lg" type="submit"><?= icon('lock', 20) ?>Pagar el plan con tarjeta<?= plan_price($pro['plan']) !== '' ? ' · ' . e(plan_price($pro['plan'])) . ' al mes' : '' ?></button></form>
      <?php elseif (!$paid): ?>
        <p class="muted">El pago online todavía no está activado. El equipo del foro te escribirá por correo para activar tu plan.</p>
      <?php endif; ?>
      <?php if (!empty($pro['stripe_customer_id']) && stripe_ready()): ?>
        <form method="post" action="<?= e(url('/profesionales/facturacion')) ?>"><?= csrf_field() ?><button class="btn" type="submit">Ver mis facturas o darme de baja</button></form>
      <?php endif; ?>
      <?php if (!empty($pro['is_active'])): ?><a class="btn" href="<?= e(url('/profesionales/ver/' . user()['username'])) ?>">Ver mi ficha pública</a><?php endif; ?>
    </div>
  </section>
  <?php endif; ?>

  <?php if ($errors): ?><ul class="errors" role="alert"><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul><?php endif; ?>

  <form method="post" class="card stack-lg" style="border-radius:32px;padding:clamp(20px,4vw,36px)">
    <?= csrf_field() ?>
    <div class="stack">
      <h2 class="h-num"><span class="num">1</span>Tus datos profesionales</h2>
      <div class="grid" style="--min:300px">
        <div class="field"><label for="full_name">Nombre y apellidos (o nombre del centro)</label><input id="full_name" name="full_name" type="text" required maxlength="80" value="<?= $v('full_name') ?>"></div>
        <div class="field"><label for="profession">Profesión</label><input id="profession" name="profession" type="text" required maxlength="80" placeholder="Ejemplo: Psicóloga sanitaria" value="<?= $v('profession') ?>"></div>
        <div class="field"><label for="license_no">Número de colegiación</label><input id="license_no" name="license_no" type="text" required maxlength="40" value="<?= $v('license_no') ?>" aria-describedby="lic-h"><span class="help" id="lic-h">Se muestra en tu ficha. Lo comprobamos con tu colegio profesional.</span></div>
        <div class="field"><label for="city">Ciudad donde atiendes</label><input id="city" name="city" type="text" required maxlength="60" value="<?= $v('city') ?>"></div>
      </div>
      <label class="check" style="align-items:center"><input type="checkbox" name="online" value="1"<?= old('online', $p['online'] ?? 0) ? ' checked' : '' ?>><span><b>También atiendo online.</b></span></label>
      <div class="field"><label for="specialties">Especialidades, separadas por comas</label><input id="specialties" name="specialties" type="text" maxlength="190" placeholder="Ejemplo: Autismo en adultos, Diagnóstico, Mujeres" value="<?= $v('specialties') ?>"><span class="help">Máximo 8. Las personas te buscan por estas palabras.</span></div>
      <div class="field"><label for="bio">Descripción</label><textarea id="bio" name="bio" rows="5" maxlength="600" data-count="bio-n" aria-describedby="bio-n"><?= $v('bio') ?></textarea><span class="help" id="bio-n" aria-live="polite"></span></div>
    </div>

    <div class="stack">
      <h2 class="h-num"><span class="num">2</span>Cómo te piden cita</h2>
      <p class="muted">Rellena al menos uno. El botón «Pedir cita» usa el primero que encuentre, en este orden.</p>
      <div class="grid" style="--min:300px">
        <div class="field"><label for="booking_url">Enlace a tu agenda online</label><input id="booking_url" name="booking_url" type="url" maxlength="190" placeholder="https://" value="<?= $v('booking_url') ?>"></div>
        <div class="field"><label for="website">Tu página web</label><input id="website" name="website" type="url" maxlength="190" placeholder="https://" value="<?= $v('website') ?>"></div>
        <div class="field"><label for="phone">Teléfono</label><input id="phone" name="phone" type="tel" maxlength="30" value="<?= $v('phone') ?>"></div>
      </div>
    </div>

    <fieldset>
      <legend class="h-num" style="font-size:clamp(1.45rem,2.6vw,1.9rem);font-weight:800"><span class="num">3</span>Elige tu plan</legend>
      <div class="grid" style="--min:260px;gap:16px">
        <?php foreach (PLANS as $key => [$name, $features]): $price = plan_price($key); ?>
          <label class="opt c-<?= $key === 'featured' ? 'amber' : 'sand' ?>">
            <input type="radio" name="plan" value="<?= $key ?>"<?= $plan === $key ? ' checked' : '' ?>>
            <span style="align-items:flex-start"><span><b><?= e($name) ?></b><small><?= $price !== '' ? e($price) . ' al mes' : 'Precio a consultar' ?></small><small style="margin-top:8px"><?= e($features[1]) ?>. <?= e($features[2]) ?>.</small></span></span>
          </label>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <label class="check"><input type="checkbox" required<?= $saved ? ' checked' : '' ?>><span>Acepto las <a href="<?= e(url('/profesionales')) ?>#planes" target="_blank"><b>normas para profesionales</b></a>: no ofrezco mis servicios por mensaje privado y mis respuestas en el foro son orientación, no diagnóstico.</span></label>
    <div class="row" style="gap:14px"><button class="btn btn-primary btn-lg" type="submit"><?= icon('check', 22, 'stroke-width="2.6"') ?><?= $saved ? 'Guardar los cambios' : 'Guardar mi ficha y seguir' ?></button></div>
  </form>
</div>
