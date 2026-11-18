<?php $mail = cfg('mail_from', ''); ?>
<div class="wrap page" style="max-width:860px">
  <div class="stack" style="gap:6px">
    <h1>Privacidad</h1>
    <p class="lead">Qué datos guardamos, para qué, y cómo puedes borrarlos.</p>
  </div>
  <div class="flash c-amber"><?= icon('question', 24) ?><span>Texto base pendiente de revisión legal. Completa los datos entre corchetes antes de abrir la web al público.</span></div>
  <div class="card prose">
    <h2>Quién es el responsable</h2>
    <p>[NOMBRE O RAZÓN SOCIAL DEL TITULAR], con NIF [NIF] y domicilio en [DIRECCIÓN]. Correo de contacto: <?= e($mail ?: '[CORREO]') ?>.</p>

    <h2>Qué datos guardamos</h2>
    <ul>
      <li><b>Para tu cuenta:</b> nombre de usuario, correo electrónico y contraseña (cifrada; no podemos verla).</li>
      <li><b>Lo que tú escribes:</b> tu perfil, tus hilos, tus respuestas, tus mensajes privados y tu presentación en «Amor neurodivergente».</li>
      <li><b>Datos opcionales:</b> cómo te identificas (por ejemplo, «autista» o «TDAH»). Son datos de salud. Solo los guardamos si tú los marcas y puedes quitarlos cuando quieras.</li>
      <li><b>Profesionales:</b> nombre, profesión y número de colegiación, que se muestran en la ficha pública. El pago lo gestiona Stripe: nosotros no vemos ni guardamos tu tarjeta.</li>
    </ul>

    <h2>Para qué los usamos</h2>
    <p>Solo para que el foro funcione: que puedas entrar, escribir y recibir avisos. No vendemos datos ni mostramos publicidad de terceros.</p>

    <h2>Quién ve qué</h2>
    <ul>
      <li>Tu correo no lo ve nadie más.</li>
      <li>Tu perfil y tus mensajes en los foros los puede leer cualquier persona.</li>
      <li>Las presentaciones de «Amor neurodivergente» solo las ven personas con cuenta.</li>
      <li>Los mensajes privados solo los ven las dos personas de la conversación. Moderación puede revisarlos únicamente si una de ellas envía un aviso.</li>
    </ul>

    <h2>Cookies</h2>
    <p>Usamos dos cookies propias y necesarias: una mantiene tu sesión abierta y otra recuerda si tienes activado el modo calma. No usamos cookies de publicidad ni de seguimiento.</p>

    <h2>Tus derechos</h2>
    <p>Puedes ver y cambiar tus datos en «Mi perfil». Puedes borrar tu cuenta y todo tu contenido en «Mi perfil → Cuenta y privacidad». Para cualquier otra petición (acceso, rectificación, supresión, oposición, limitación o portabilidad), escribe a <?= e($mail ?: '[CORREO]') ?>. También puedes reclamar ante la Agencia Española de Protección de Datos (aepd.es).</p>

    <h2>Edad mínima</h2>
    <p>Para crear una cuenta tienes que tener [EDAD MÍNIMA] años o más. La zona «Amor neurodivergente» es solo para mayores de 18 años.</p>
  </div>
</div>
