<?php $mail = cfg('mail_from', ''); ?>
<div class="wrap page" style="max-width:860px">
  <div class="stack" style="gap:6px">
    <h1>Contacto</h1>
    <p class="lead">Elige la opción que se parece más a lo que necesitas.</p>
  </div>
  <div class="grid" style="--min:340px">
    <div class="card tint c-coral stack" style="align-items:flex-start;border-radius:26px">
      <span class="pic sm"><?= icon('flag', 34) ?></span>
      <h2 style="font-size:1.25rem">Un mensaje del foro me molesta</h2>
      <p>Pulsa «Avisar a moderación» debajo de ese mensaje. Es la forma más rápida: una persona del equipo lo revisa.</p>
    </div>
    <div class="card tint c-sky stack" style="align-items:flex-start;border-radius:26px">
      <span class="pic sm"><?= icon('mail', 34) ?></span>
      <h2 style="font-size:1.25rem">Tengo una duda o una propuesta</h2>
      <p>Escríbenos por correo. Respondemos en unos días.</p>
      <?php if ($mail): ?><a class="btn btn-color" href="mailto:<?= e($mail) ?>"><?= e($mail) ?></a><?php endif; ?>
    </div>
    <div class="card tint c-amber stack" style="align-items:flex-start;border-radius:26px">
      <span class="pic sm"><?= icon('shield', 34) ?></span>
      <h2 style="font-size:1.25rem">Soy profesional</h2>
      <p>Toda la información sobre la ficha verificada y los planes está en la zona de profesionales.</p>
      <a class="btn btn-color" href="<?= e(url('/profesionales')) ?>">Ir a la zona de profesionales</a>
    </div>
    <div class="card dark stack" style="align-items:flex-start;border-radius:26px">
      <span class="pic sm" style="background:var(--gold);color:var(--ink)"><?= icon('phone', 34) ?></span>
      <h2 style="font-size:1.25rem">Es una urgencia</h2>
      <p>El foro no puede atender urgencias. Llama al <b>112</b>. Si piensas en hacerte daño, llama al <b>024</b>: es gratis y atiende las 24 horas.</p>
    </div>
  </div>
</div>
