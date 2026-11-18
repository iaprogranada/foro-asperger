// Prueba de extremo a extremo de Foro Asperger con un navegador real.
const { chromium } = require('playwright');
const fs = require('fs');
const B = 'http://127.0.0.1:8080';
const SHOTS = __dirname + '/shots';
fs.mkdirSync(SHOTS, { recursive: true });
fs.writeFileSync(__dirname + '/guia.pdf', '%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF\n');

const problems = [];
let step = '';
const ok = (m) => console.log('  ok  ' + m);
function check(cond, msg) { if (cond) ok(msg); else { problems.push(step + ': ' + msg); console.log('  FALLO ' + msg); } }

async function newUser(browser, name) {
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  page.on('response', (r) => { if (r.status() >= 500) problems.push(name + ' → ' + r.status() + ' ' + r.url()); });
  page.on('pageerror', (e) => problems.push(name + ' JS: ' + e.message));
  page.on('dialog', (d) => d.accept());
  return { ctx, page };
}
async function scanPhpErrors(page, label) {
  const html = await page.content();
  const m = html.match(/(Warning|Notice|Fatal error|Deprecated)<\/b>:[^<]*/);
  if (m) problems.push(label + ' PHP: ' + m[0]);
  if (html.includes('Algo ha fallado')) problems.push(label + ': página de error');
}
async function register(page, username, who) {
  await page.goto(B + '/registro');
  await page.locator(`input[name=who][value=${who}]`).check({ force: true });
  await page.fill('#username', username);
  await page.fill('#email', username + '@example.com');
  await page.fill('#password', 'clave-segura-1');
  await page.locator('input[name=normas]').check();
  await page.click('button[type=submit]');
  await page.waitForLoadState();
}

(async () => {
  const browser = await chromium.launch();

  // --- Lucía: registro, perfil, hilo ---
  step = 'lucia';
  const L = await newUser(browser, 'lucia');
  await register(L.page, 'lucia', 'nd');
  check(L.page.url().includes('/perfil?paso=2'), 'tras el registro va al paso 2 del perfil');
  await L.page.fill('#display_name', 'Lucía');
  await L.page.fill('#bio', 'Autista. Me gusta caminar por la sierra y los juegos de mesa.');
  await L.page.locator('input[name="identities[]"][value=autista]').check({ force: true });
  await L.page.locator('input[name=avatar][value="planet:lav"]').check({ force: true });
  await L.page.screenshot({ path: SHOTS + '/perfil.png', fullPage: true });
  await L.page.click('button[type=submit]:has-text("Guardar")');
  await L.page.waitForLoadState();
  check(L.page.url().endsWith('/#foros'), 'tras guardar el perfil va a elegir foro');
  await scanPhpErrors(L.page, 'home');

  await L.page.click('a.tile:has-text("Asperger")');
  await L.page.waitForLoadState();
  check((await L.page.locator('.tile.compact').count()) === 8, 'el foro Asperger tiene 8 subforos');
  await L.page.click('a:has-text("Abrir un hilo nuevo")');
  await L.page.locator('label.choice:has-text("Presentaciones") input').check({ force: true });
  await L.page.fill('#title', 'Me diagnosticaron con 41 años. ¿Por dónde empiezo?');
  await L.page.fill('#body', 'Hola. Me llamo Lucía.\n\nLa semana pasada recibí el diagnóstico. Ahora entiendo muchas cosas.\n\n¿Qué hicisteis vosotros las primeras semanas? Más información en https://example.com/guia.');
  await L.page.locator('input[name=tone][value=pregunta]').check({ force: true });
  await L.page.click('button:has-text("Publicar el hilo")');
  await L.page.waitForLoadState();
  const threadUrl = L.page.url();
  check(/\/hilo\/\d+-me-diagnosticaron/.test(threadUrl), 'el hilo se crea con dirección legible: ' + threadUrl.replace(B, ''));
  check((await L.page.locator('.post .body a[rel*=nofollow]').count()) === 1, 'los enlaces del mensaje se convierten en enlaces seguros');
  check((await L.page.locator('.post .body p').count()) === 3, 'los párrafos se respetan');

  // --- Álvaro: responde y reacciona ---
  step = 'alvaro';
  const A = await newUser(browser, 'alvaro');
  await register(A.page, 'alvaro', 'nd');
  await A.page.goto(threadUrl);
  await A.page.fill('#respuesta', 'A mí me diagnosticaron con 36. Las primeras semanas descansé y leí mucho.');
  await A.page.click('button:has-text("Publicar respuesta")');
  await A.page.waitForLoadState();
  check((await A.page.locator('article.post').count()) === 2, 'la respuesta aparece en el hilo');
  await A.page.locator('article.post').first().locator('button.react:has-text("Me pasa igual")').click();
  await A.page.waitForLoadState();
  check((await A.page.locator('article.post').first().locator('button.react.on').count()) === 1, 'la reacción queda marcada');
  await A.page.locator('article.post').first().locator('summary:has-text("Avisar")').click();
  await A.page.locator('article.post').first().locator('textarea[name=reason]').fill('Prueba de aviso');
  await A.page.locator('article.post').first().locator('button:has-text("Enviar el aviso")').click();
  await A.page.waitForLoadState();
  check(await A.page.locator('.flash:has-text("Aviso enviado")').count() === 1, 'el aviso a moderación se envía');
  await A.page.screenshot({ path: SHOTS + '/hilo.png', fullPage: true });
  await scanPhpErrors(A.page, 'hilo');

  // XSS: el contenido de usuario se escapa.
  await A.page.goto(threadUrl);
  await A.page.fill('#respuesta', '<script>window.__xss=1</script><img src=x onerror="window.__xss=1"> hola');
  await A.page.click('button:has-text("Publicar respuesta")');
  await A.page.waitForLoadState();
  check((await A.page.evaluate(() => window.__xss)) === undefined, 'el HTML escrito por usuarios no se ejecuta');

  // --- Amor neurodivergente ---
  step = 'amor';
  for (const [u, name, age, city, look] of [[L, 'Lucía', '29', 'Granada', 'grupo'], [A, 'Álvaro', '34', 'Bilbao', 'amistad']]) {
    await u.page.goto(B + '/amor');
    await u.page.fill('input[name=first_name]', name);
    await u.page.fill('input[name=age]', age);
    await u.page.fill('input[name=city]', city);
    await u.page.selectOption('select[name=looking]', look);
    await u.page.fill('#body', 'Busco gente tranquila para hablar.');
    await u.page.locator('input[name="interests[]"][value="Senderismo"]').check({ force: true });
    await u.page.click('button:has-text("Publicar mi presentación")');
    await u.page.waitForLoadState();
  }
  await A.page.goto(B + '/amor');
  check((await A.page.locator('article.person').count()) === 1, 'Álvaro ve la presentación de Lucía (y no la suya)');
  await A.page.screenshot({ path: SHOTS + '/amor.png', fullPage: true });
  await A.page.fill('article.person input[name=message]', 'Hola, Lucía. A mí también me gusta el senderismo.');
  await A.page.click('button:has-text("Enviar un saludo a Lucía")');
  await A.page.waitForLoadState();
  check(await A.page.locator('text=Saludo enviado').count() >= 1, 'el saludo se envía');

  // Menores de 18: rechazado
  const M = await newUser(browser, 'menor');
  await register(M.page, 'menor17', 'nd');
  await M.page.goto(B + '/amor');
  await M.page.fill('input[name=first_name]', 'Menor');
  await M.page.evaluate(() => document.querySelector('input[name=age]').removeAttribute('min'));
  await M.page.fill('input[name=age]', '17');
  await M.page.fill('input[name=city]', 'Madrid');
  await M.page.click('button:has-text("Publicar mi presentación")');
  await M.page.waitForLoadState();
  check(await M.page.locator('.flash:has-text("18 años")').count() === 1, 'no se admite una presentación con menos de 18 años');
  await M.ctx.close();

  await L.page.goto(B + '/mensajes');
  check(await L.page.locator('text=Saludos nuevos').count() === 1, 'Lucía ve el saludo pendiente');
  await L.page.click('button:has-text("Aceptar y hablar")');
  await L.page.waitForLoadState();
  check(/\/mensajes\/\d+/.test(L.page.url()), 'al aceptar se abre la conversación');
  await L.page.fill('#body', '¡Hola, Álvaro! ¿Qué rutas haces?');
  await L.page.click('button:has-text("Enviar mensaje")');
  await L.page.waitForLoadState();
  check((await L.page.locator('.bubble').count()) === 2, 'la conversación tiene el saludo y la respuesta');
  await L.page.screenshot({ path: SHOTS + '/mensajes.png', fullPage: true });
  await A.page.goto(B + '/');
  check(await A.page.locator('.site-header .badge').count() === 1, 'Álvaro ve el aviso de mensaje nuevo en la cabecera');

  // Privacidad: un tercero no puede leer la conversación.
  const convUrl = L.page.url().split('#')[0];
  const X = await newUser(browser, 'intruso');
  await register(X.page, 'intruso', 'curious');
  const r = await X.page.goto(convUrl);
  check(r.status() === 404, 'otra persona no puede abrir una conversación ajena (404)');
  const r2 = await X.page.goto(B + '/foro/profesionales');
  check(r2.status() === 403, 'el foro de profesionales está cerrado para miembros normales (403)');
  const r3 = await X.page.goto(B + '/admin');
  check(r3.status() === 403, 'el panel de administración está cerrado para miembros (403)');
  await X.ctx.close();

  // --- Profesional ---
  step = 'pro';
  const P = await newUser(browser, 'marta');
  await register(P.page, 'marta', 'pro');
  check(P.page.url().includes('/profesionales/alta'), 'quien se registra como profesional va al alta');
  await P.page.fill('#full_name', 'Marta Ruiz');
  await P.page.fill('#profession', 'Psicóloga sanitaria');
  await P.page.fill('#license_no', 'M-12345');
  await P.page.fill('#city', 'Madrid');
  await P.page.locator('input[name=online]').check();
  await P.page.fill('#specialties', 'Autismo en adultos, Diagnóstico, Mujeres');
  await P.page.fill('#bio', 'Acompaño a personas adultas en el proceso de diagnóstico.');
  await P.page.fill('#website', 'https://example.com');
  await P.page.locator('form:has(#full_name) input[type=checkbox][required]').check();
  await P.page.click('button:has-text("Guardar mi ficha")');
  await P.page.waitForLoadState();
  check(await P.page.locator('#estado').count() === 1, 'tras guardar la ficha se ve el estado del alta');
  await P.page.screenshot({ path: SHOTS + '/alta-pro.png', fullPage: true });
  let rp = await P.page.goto(B + '/foro/profesionales');
  check(rp.status() === 403, 'sin verificar no entra al foro privado');
  await P.page.goto(B + '/profesionales/directorio');
  check((await P.page.locator('article.person').count()) === 0, 'sin verificar no aparece en el directorio');

  // --- Admin ---
  step = 'admin';
  const AD = await newUser(browser, 'admin');
  await AD.page.goto(B + '/entrar');
  await AD.page.fill('#login', 'admin');
  await AD.page.fill('#password', 'admin12345');
  await AD.page.click('button[type=submit]');
  await AD.page.waitForLoadState();
  await AD.page.goto(B + '/admin');
  check((await AD.page.locator('article.card').count()) === 1, 'el aviso de Álvaro aparece en administración');
  await AD.page.screenshot({ path: SHOTS + '/admin.png', fullPage: true });
  await AD.page.click('button:has-text("Cerrar el aviso")');
  await AD.page.waitForLoadState();
  await AD.page.goto(B + '/admin?tab=profesionales');
  await AD.page.click('button:has-text("Marcar como verificado")');
  await AD.page.waitForLoadState();
  await AD.page.click('button:has-text("Activar sin Stripe")');
  await AD.page.waitForLoadState();
  check(await AD.page.locator('.tag:has-text("Publicada")').count() === 1, 'verificada + activada → ficha publicada');
  await scanPhpErrors(AD.page, 'admin pros');

  // Recurso
  await AD.page.goto(B + '/recursos/subir');
  await AD.page.fill('#title', 'Guía: primeros pasos después del diagnóstico');
  await AD.page.fill('#description', 'Qué significa el diagnóstico, qué apoyos puedes pedir y cómo contárselo a tu entorno.');
  await AD.page.setInputFiles('#file', __dirname + '/guia.pdf');
  await AD.page.click('button:has-text("Publicar el recurso")');
  await AD.page.waitForLoadState();
  check((await AD.page.locator('article.res').count()) === 1, 'el recurso se publica');
  await AD.page.goto(B + '/admin?tab=recursos');
  await AD.page.click('button:has-text("Destacar")');
  await AD.page.waitForLoadState();
  // Segundo recurso para ver la rejilla
  await AD.page.goto(B + '/recursos/subir');
  await AD.page.fill('#title', 'Agenda visual de la semana');
  await AD.page.fill('#description', 'Para planificar cada día con pictogramas.');
  await AD.page.locator('input[name=kind][value=plantilla]').check({ force: true });
  await AD.page.setInputFiles('#file', __dirname + '/guia.pdf');
  await AD.page.click('button:has-text("Publicar el recurso")');
  await AD.page.waitForLoadState();
  await AD.page.screenshot({ path: SHOTS + '/recursos.png', fullPage: true });
  check(await AD.page.locator('#destacado').count() === 1, 'el recurso destacado se muestra arriba');
  // Resumen del hilo y fijado
  await AD.page.goto(threadUrl);
  await AD.page.fill('#summary', 'Date tiempo. Es normal sentir alivio y tristeza a la vez.\nLee experiencias de otras personas con diagnóstico tardío.');
  await AD.page.click('button:has-text("Guardar resumen")');
  await AD.page.waitForLoadState();
  check(await AD.page.locator('#resumen').count() === 1, 'el resumen del hilo se muestra');

  // Descarga como usuaria
  const [dl] = await Promise.all([L.page.waitForEvent('download'), L.page.goto(B + '/recursos').then(() => L.page.click('a:has-text("Descargar en PDF")'))]);
  check((await dl.suggestedFilename()) === 'guia.pdf', 'la descarga funciona con el nombre original');

  // Profesional ya activa
  step = 'pro-activa';
  rp = await P.page.goto(B + '/foro/profesionales');
  check(rp.status() === 200, 'la profesional verificada entra al foro privado');
  await P.page.goto(threadUrl);
  await P.page.fill('#respuesta', 'Hola, Lucía. Lo que cuentas es muy frecuente tras un diagnóstico en la edad adulta.');
  await P.page.click('button:has-text("Publicar respuesta")');
  await P.page.waitForLoadState();
  check(await P.page.locator('article.post.is-pro .pill.gold').count() === 1, 'su respuesta lleva el sello de profesional verificado');
  await P.page.screenshot({ path: SHOTS + '/hilo-completo.png', fullPage: true });
  await P.page.goto(B + '/profesionales/directorio');
  check((await P.page.locator('article.person').count()) === 1, 'aparece en el directorio');
  await P.page.screenshot({ path: SHOTS + '/directorio.png', fullPage: true });
  await P.page.click('a:has-text("Ver la ficha y pedir cita")');
  await P.page.waitForLoadState();
  check(await P.page.locator('a:has-text("Pedir cita")').count() === 1, 'la ficha tiene botón «Pedir cita»');
  await P.page.screenshot({ path: SHOTS + '/ficha.png', fullPage: true });
  await scanPhpErrors(P.page, 'ficha');
  await P.page.goto(B + '/profesionales/alta');
  check(await P.page.locator('.flash:has-text("publicada en el directorio")').count() === 1, 'el estado del alta dice que está publicada');

  // Modo calma y salida
  step = 'calma';
  await L.page.goto(B + '/');
  await L.page.click('[data-calm]');
  check(await L.page.evaluate(() => document.documentElement.classList.contains('calm')), 'el botón de modo calma activa la clase');
  await L.page.reload();
  check(await L.page.evaluate(() => document.documentElement.classList.contains('calm')), 'el modo calma se recuerda al recargar');
  await L.page.click('[data-calm]');

  // Capturas de páginas públicas (escritorio y móvil)
  step = 'capturas';
  const G = await newUser(browser, 'invitado');
  for (const [name, path] of [['home', '/'], ['foro', '/foro/asperger'], ['pros', '/profesionales'], ['registro', '/registro'], ['amor-invitado', '/amor'], ['normas', '/normas']]) {
    await G.page.goto(B + path);
    await G.page.screenshot({ path: `${SHOTS}/${name}.png`, fullPage: true });
    await scanPhpErrors(G.page, name);
    const over = await G.page.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    check(over <= 0, `${name}: sin desbordamiento horizontal en escritorio`);
  }
  const mob = await browser.newContext({ viewport: { width: 390, height: 844 }, deviceScaleFactor: 1 });
  const mp = await mob.newPage();
  for (const [name, path] of [['home', '/'], ['foro', '/foro/asperger'], ['hilo', threadUrl.replace(B, '')], ['pros', '/profesionales'], ['registro', '/registro'], ['recursos', '/recursos'], ['amor', '/amor']]) {
    await mp.goto(B + path);
    await mp.screenshot({ path: `${SHOTS}/m-${name}.png`, fullPage: true });
    const over = await mp.evaluate(() => document.documentElement.scrollWidth - document.documentElement.clientWidth);
    check(over <= 0, `${name}: sin desbordamiento horizontal en móvil (${over}px)`);
  }

  // Borrado de cuenta
  step = 'borrar';
  await A.page.goto(B + '/perfil/cuenta');
  await A.page.fill('#c3', 'clave-segura-1');
  await A.page.click('button:has-text("Borrar mi cuenta para siempre")');
  await A.page.waitForLoadState();
  check(await A.page.locator('.flash:has-text("se han borrado")').count() === 1, 'una persona puede borrar su cuenta y sus datos');

  await browser.close();
  console.log('\n' + (problems.length ? 'PROBLEMAS:\n- ' + problems.join('\n- ') : 'TODO CORRECTO'));
  process.exit(problems.length ? 1 : 0);
})().catch((e) => { console.error('ERROR en «' + step + '»:', e.message.split('\n').slice(0, 6).join('\n')); console.log('Problemas hasta ahora:', problems); process.exit(2); });
