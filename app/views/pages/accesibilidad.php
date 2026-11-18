<div class="wrap page" style="max-width:860px">
  <div class="stack" style="gap:6px">
    <h1>Accesibilidad</h1>
    <p class="lead">Esta web está pensada para personas autistas y neurodivergentes. Esto es lo que hemos hecho.</p>
  </div>
  <div class="grid" style="--min:340px">
    <?php foreach ([
        ['moon', 'sky', 'Modo calma', 'Un botón arriba baja el color de toda la web. Se queda guardado.'],
        ['eye', 'lav', 'Sin sorpresas', 'Nada se mueve solo. No hay vídeos ni sonidos que empiecen sin que tú lo pidas.'],
        ['lines', 'mint', 'Lenguaje claro', 'Frases cortas, sin dobles sentidos. Los botones dicen lo que hacen.'],
        ['calendar', 'amber', 'Todo en su sitio', 'El menú está siempre arriba y en el mismo orden en todas las páginas.'],
        ['grid', 'green', 'Dibujos y colores', 'Cada foro tiene un pictograma y un color para reconocerlo sin leer.'],
        ['book', 'coral', 'Letras fáciles de leer', 'Usamos Lexend y Atkinson Hyperlegible, dos tipografías creadas para leer mejor.'],
        ['spark', 'pink', 'Indicador de tono', 'Cada mensaje dice si va en serio, si es broma o si es una pregunta sincera.'],
        ['settings', 'sand', 'Tú ajustas la web', 'En tu perfil puedes agrandar el texto, quitar animaciones y ocultar los contadores.'],
    ] as [$ic, $c, $h, $p]): ?>
      <div class="card tint c-<?= $c ?> row" style="align-items:flex-start;flex-wrap:nowrap;gap:14px;padding:22px;border-radius:24px">
        <span class="pic sm"><?= icon($ic, 34) ?></span>
        <div class="stack" style="gap:4px"><h2 style="font-size:1.15rem"><?= e($h) ?></h2><p class="small"><?= e($p) ?></p></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="card stack">
    <h2 style="font-size:1.25rem">También para lectores de pantalla y teclado</h2>
    <p>Puedes usar toda la web con el teclado. El contraste del texto cumple el nivel AA de las pautas WCAG 2.1. La web funciona aunque tu navegador no tenga JavaScript.</p>
    <p>¿Algo te resulta difícil de usar? <a href="<?= e(url('/contacto')) ?>"><b>Cuéntanoslo</b></a> y lo arreglamos.</p>
  </div>
</div>
