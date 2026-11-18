// Foro Asperger · pequeñas mejoras. La web funciona también sin JavaScript.
(function () {
  'use strict';
  var root = document.documentElement;

  // Modo calma: se recuerda en una cookie durante un año.
  var calmBtn = document.querySelector('[data-calm]');
  function paintCalm() {
    if (!calmBtn) return;
    var on = root.classList.contains('calm');
    calmBtn.setAttribute('aria-pressed', on ? 'true' : 'false');
    calmBtn.querySelector('[data-calm-label]').textContent = on ? 'Modo calma: activado' : 'Modo calma';
  }
  if (calmBtn) {
    paintCalm();
    calmBtn.addEventListener('click', function () {
      var on = !root.classList.contains('calm');
      root.classList.toggle('calm', on);
      document.cookie = 'calma=' + (on ? '1' : '0') + ';path=/;max-age=31536000;samesite=lax';
      paintCalm();
    });
  }

  // Mostrar u ocultar la contraseña.
  document.querySelectorAll('[data-show-pass]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.getAttribute('data-show-pass'));
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.querySelector('span').textContent = show ? 'Ocultar' : 'Mostrar';
    });
  });

  // Contador de caracteres.
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var out = document.getElementById(el.getAttribute('data-count'));
    var max = parseInt(el.getAttribute('maxlength'), 10);
    function paint() { out.textContent = 'Has escrito ' + el.value.length + ' de ' + max + ' caracteres.'; }
    el.addEventListener('input', paint);
    paint();
  });

  // Confirmación antes de acciones que no se pueden deshacer.
  document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
      if (!window.confirm(form.getAttribute('data-confirm'))) ev.preventDefault();
    });
  });

  // «Responder a…» lleva al formulario y escribe la mención.
  document.querySelectorAll('[data-reply-to]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var box = document.getElementById('respuesta');
      if (!box) return;
      var name = '@' + btn.getAttribute('data-reply-to') + ' ';
      if (box.value.indexOf(name) === -1) box.value = name + box.value;
      box.focus();
      box.scrollIntoView({ block: 'center' });
    });
  });
})();
