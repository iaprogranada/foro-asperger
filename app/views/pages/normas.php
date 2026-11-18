<div class="wrap page" style="max-width:860px">
  <div class="stack" style="gap:6px">
    <h1>Normas de la comunidad</h1>
    <p class="lead">Son pocas y claras. Sirven para que todas las personas estén a gusto.</p>
  </div>
  <div class="stack">
    <?php foreach ([
        ['heart', 'mint', 'Trata a los demás con respeto', 'No se permiten insultos, burlas ni desprecios. Tampoco hacia personas que no están en el foro.'],
        ['lines', 'sky', 'Escribe claro', 'Usa frases cortas. Si es una broma o una ironía, dilo con el indicador de tono. Así nadie tiene que adivinarlo.'],
        ['clock', 'lav', 'Cada persona responde a su ritmo', 'No exijas respuestas rápidas. Hay personas que necesitan horas o días para contestar.'],
        ['lock', 'amber', 'Cuida la privacidad', 'No publiques datos privados tuyos ni de otras personas: teléfonos, direcciones, fotos de menores o informes médicos.'],
        ['shield', 'green', 'El foro no sustituye a un profesional', 'Aquí se comparten experiencias. Nadie puede dar un diagnóstico por un foro. Para eso, consulta el directorio de profesionales.'],
        ['bolt', 'coral', 'Sin publicidad', 'No se permite anunciar productos ni servicios. Los profesionales verificados tienen su propia zona.'],
        ['flag', 'pink', 'Si algo te molesta, avisa', 'Pulsa «Avisar a moderación». Lo revisa una persona del equipo. También puedes bloquear a quien no quieras leer.'],
    ] as [$ic, $c, $h, $p]): ?>
      <div class="card row" style="align-items:flex-start;flex-wrap:nowrap;gap:16px;padding:22px">
        <span class="pic sm c-<?= $c ?>" style="background:var(--bg)"><?= icon($ic, 34) ?></span>
        <div class="stack" style="gap:4px"><h2 style="font-size:1.25rem"><?= e($h) ?></h2><p><?= e($p) ?></p></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="card tint c-amber stack">
    <h2 style="font-size:1.25rem">Qué pasa si alguien no cumple las normas</h2>
    <p>Primero, moderación borra el mensaje y avisa a la persona. Si se repite, la cuenta se suspende.</p>
    <p><b>Si estás en peligro o piensas en hacerte daño, llama al 112 o al 024 (línea de atención a la conducta suicida, gratis y disponible las 24 horas en España).</b></p>
  </div>
</div>
