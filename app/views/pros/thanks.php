<div class="wrap page" style="max-width:760px">
  <div class="card tint c-mint stack" style="align-items:flex-start;border-radius:32px">
    <span class="pic sm"><?= icon('check', 34, 'stroke-width="2.4"') ?></span>
    <h1 style="font-size:2rem">Pago recibido. Gracias.</h1>
    <p class="lead" style="color:var(--ink)">Tu plan se activa en unos segundos. <?= $pro && $pro['verified_at'] ? 'Tu ficha ya aparece en el directorio.' : 'Tu ficha aparecerá en el directorio cuando terminemos de comprobar tu número de colegiación. Te avisamos por correo.' ?></p>
    <div class="row"><a class="btn btn-primary" href="<?= e(url('/profesionales/alta#estado')) ?>">Ver el estado de mi alta</a><a class="btn" href="<?= e(url('/foro/profesionales')) ?>">Ir al foro de profesionales</a></div>
  </div>
</div>
