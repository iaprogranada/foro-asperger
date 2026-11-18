<?php
/** Foros y subforos iniciales. Se ejecuta una sola vez desde el instalador. */

function seed_forums(int $adminId): void
{
    if ((int) val('SELECT COUNT(*) FROM forums') > 0) return;

    $general = [
        ['presentaciones', 'Presentaciones', 'Di hola a la comunidad', 'user', 'amber'],
        ['vida-diaria', 'Vida diaria', 'Rutinas, casa y gestiones', 'home', 'mint'],
        ['amistad-y-pareja', 'Amistad y pareja', 'Relaciones con otras personas', 'heart', 'pink'],
        ['trabajo', 'Trabajo', 'Empleo y entrevistas', 'briefcase', 'sky'],
        ['estudios', 'Estudios', 'Instituto, universidad y cursos', 'book', 'lav'],
        ['salud-mental', 'Salud mental', 'Ansiedad, agotamiento y descanso', 'leaf', 'green'],
        ['sentidos', 'Sentidos', 'Ruido, luz, tacto y comida', 'eye', 'coral'],
        ['logros', 'Logros', 'Buenas noticias, grandes o pequeñas', 'star', 'sand'],
    ];
    $apoyos = [
        ['presentaciones', 'Presentaciones', 'Di hola a la comunidad', 'user', 'amber'],
        ['comunicacion', 'Comunicación', 'Pictogramas, comunicadores y lenguaje', 'chat-lines', 'sky'],
        ['vida-diaria', 'Vida diaria y autonomía', 'Rutinas, higiene, comida y sueño', 'home', 'mint'],
        ['apoyos-y-ayudas', 'Apoyos y ayudas', 'Dependencia, discapacidad y trámites', 'doc', 'lav'],
        ['salud', 'Salud', 'Médicos, medicación y terapias', 'leaf', 'green'],
        ['logros', 'Logros', 'Buenas noticias, grandes o pequeñas', 'star', 'sand'],
    ];

    $forums = [
        // grupo, slug, nombre, descripción, icono, color, solo profesionales, subforos
        ['quien', 'soy-autista', 'Soy autista', 'Para todas las personas del espectro', 'inf', 'mint', 0, $general],
        ['quien', 'asperger', 'Asperger', 'Vivir con síndrome de Asperger', 'spark', 'sky', 0, $general],
        ['quien', 'autismo-leve', 'Autismo leve', 'Grado 1 · necesita algunos apoyos', 'bars1', 'green', 0, $general],
        ['quien', 'autismo-moderado', 'Autismo moderado', 'Grado 2 · necesita bastantes apoyos', 'bars2', 'amber', 0, $apoyos],
        ['quien', 'autismo-profundo', 'Autismo profundo', 'Grado 3 · necesita muchos apoyos', 'bars3', 'coral', 0, $apoyos],
        ['quien', 'familias', 'Familias', 'Madres, padres, parejas y hermanos', 'house', 'pink', 0, [
            ['presentaciones', 'Presentaciones', 'Di hola a la comunidad', 'user', 'amber'],
            ['infancia', 'Infancia', 'De 0 a 11 años', 'star', 'sky'],
            ['adolescencia', 'Adolescencia', 'De 12 a 17 años', 'headphones', 'lav'],
            ['hijos-adultos', 'Hijos adultos', 'Autonomía, trabajo y futuro', 'key', 'mint'],
            ['colegio-y-apoyos', 'Colegio y apoyos', 'Aula, adaptaciones y ayudas', 'book', 'green'],
            ['parejas', 'Parejas', 'Mi pareja es autista', 'heart', 'pink'],
            ['hermanos', 'Hermanos', 'Crecer con un hermano autista', 'users', 'coral'],
            ['cuidarse', 'Cuidarse para cuidar', 'El bienestar de quien acompaña', 'leaf', 'sand'],
        ]],
        ['etapa', 'zona-joven', 'Zona joven', 'Estudios, amistades y primeros pasos', 'headphones', 'lav', 0, [
            ['presentaciones', 'Presentaciones', 'Di hola a la comunidad', 'user', 'amber'],
            ['estudios', 'Estudios', 'Instituto, FP y universidad', 'book', 'sky'],
            ['amistades', 'Amistades', 'Hacer y mantener amigos', 'users', 'mint'],
            ['primer-trabajo', 'Primer trabajo', 'Prácticas y primeras entrevistas', 'briefcase', 'green'],
            ['independizarse', 'Independizarse', 'Vivir fuera de casa', 'key', 'coral'],
            ['ocio', 'Ocio', 'Juegos, series y aficiones', 'planet', 'pink'],
        ]],
        ['etapa', 'zona-adulta', 'Zona adulta', 'Trabajo, pareja y vida independiente', 'key', 'sky', 0, [
            ['presentaciones', 'Presentaciones', 'Di hola a la comunidad', 'user', 'amber'],
            ['trabajo', 'Trabajo', 'Empleo, compañeros y adaptaciones', 'briefcase', 'sky'],
            ['pareja-y-familia', 'Pareja y familia', 'Convivencia, crianza y relaciones', 'heart', 'pink'],
            ['casa-y-dinero', 'Casa y dinero', 'Gestiones, facturas y organización', 'home', 'mint'],
            ['salud', 'Salud', 'Médicos, agotamiento y descanso', 'leaf', 'green'],
            ['logros', 'Logros', 'Buenas noticias, grandes o pequeñas', 'star', 'sand'],
        ]],
        ['etapa', 'zona-senior', 'Zona senior', 'Madurez, salud y diagnóstico tardío', 'tree', 'sand', 0, [
            ['presentaciones', 'Presentaciones', 'Di hola a la comunidad', 'user', 'amber'],
            ['diagnostico-tardio', 'Diagnóstico tardío', 'Entenderse después de los 50', 'search', 'sky'],
            ['salud', 'Salud', 'Médicos, cambios y cuidados', 'leaf', 'green'],
            ['jubilacion', 'Jubilación', 'Rutinas nuevas y tiempo libre', 'calendar', 'mint'],
            ['recuerdos', 'Recuerdos', 'Mirar atrás con otra mirada', 'book', 'lav'],
        ]],
        ['tema', 'diagnostico', 'Diagnóstico', 'Dudas, pruebas y diagnóstico tardío', 'search', 'amber', 0, [
            ['soy-autista', '¿Soy autista?', 'Dudas antes de pedir una valoración', 'question', 'sky'],
            ['el-proceso', 'El proceso', 'Pruebas, profesionales y esperas', 'lines', 'mint'],
            ['despues', 'Después del diagnóstico', 'Qué hacer con la noticia', 'arrow', 'green'],
            ['contarlo', 'Contarlo', 'Familia, amigos y trabajo', 'chat-lines', 'pink'],
        ]],
        ['tema', 'mujeres', 'Mujeres en el espectro', 'Experiencias y diagnóstico en femenino', 'venus', 'pink', 0, [
            ['presentaciones', 'Presentaciones', 'Di hola a la comunidad', 'user', 'amber'],
            ['diagnostico-tardio', 'Diagnóstico tardío', 'Años sin saberlo', 'search', 'sky'],
            ['enmascaramiento', 'Enmascaramiento', 'Ocultar rasgos y su coste', 'eye', 'lav'],
            ['maternidad', 'Maternidad', 'Ser madre y autista', 'house', 'coral'],
            ['salud', 'Salud', 'Ciclo, menopausia y bienestar', 'leaf', 'green'],
        ]],
        ['tema', 'trabajo-y-estudios', 'Trabajo y estudios', 'Empleo, aula y adaptaciones', 'briefcase', 'sky', 0, [
            ['buscar-empleo', 'Buscar empleo', 'Currículum y entrevistas', 'search', 'amber'],
            ['en-el-trabajo', 'En el trabajo', 'Compañeros, jefes y rutinas', 'briefcase', 'sky'],
            ['adaptaciones', 'Adaptaciones', 'Qué pedir y cómo pedirlo', 'settings', 'mint'],
            ['estudios', 'Estudios', 'Exámenes, trabajos en grupo y apoyos', 'book', 'lav'],
        ]],
        ['tema', 'sentidos-y-calma', 'Sentidos y calma', 'Ruido, luz, sobrecarga y descanso', 'leaf', 'green', 0, [
            ['ruido-y-luz', 'Ruido y luz', 'Auriculares, gafas y entornos', 'headphones', 'sky'],
            ['comida-y-texturas', 'Comida y texturas', 'Selectividad y ropa', 'heart', 'coral'],
            ['sobrecarga', 'Sobrecarga y crisis', 'Antes, durante y después', 'bolt', 'amber'],
            ['agotamiento', 'Agotamiento autista', 'Reconocerlo y recuperarse', 'moon', 'lav'],
            ['descanso', 'Descanso y sueño', 'Rutinas para dormir mejor', 'leaf', 'green'],
        ]],
        ['tema', 'intereses-especiales', 'Intereses especiales', 'Comparte lo que más te gusta', 'planet', 'lav', 0, [
            ['ciencia-y-tecnologia', 'Ciencia y tecnología', 'Del espacio a la programación', 'planet', 'sky'],
            ['arte-y-musica', 'Arte y música', 'Crear, tocar y escuchar', 'spark', 'pink'],
            ['juegos', 'Juegos', 'Videojuegos, rol y juegos de mesa', 'grid', 'lav'],
            ['naturaleza-y-animales', 'Naturaleza y animales', 'Plantas, mascotas y aire libre', 'tree', 'green'],
            ['colecciones', 'Colecciones', 'Enseña lo que coleccionas', 'star', 'amber'],
            ['otros', 'Otros intereses', 'Todo lo demás', 'heart', 'coral'],
        ]],
        ['tema', 'tdah-y-mas', 'TDAH y más', 'TDAH, dislexia y altas capacidades', 'bolt', 'coral', 0, [
            ['tdah', 'TDAH', 'Atención, impulsividad y organización', 'bolt', 'coral'],
            ['dislexia', 'Dislexia y dispraxia', 'Lectura, escritura y coordinación', 'book', 'sky'],
            ['altas-capacidades', 'Altas capacidades', 'Solas o junto al autismo', 'spark', 'amber'],
            ['varias-a-la-vez', 'Varias a la vez', 'Cuando coinciden varios diagnósticos', 'inf', 'lav'],
        ]],
        ['pro', 'profesionales', 'Foro de profesionales', 'Espacio privado para profesionales verificados', 'shield', 'amber', 1, [
            ['presentaciones', 'Presentaciones', 'Quién eres y a qué te dedicas', 'user', 'amber'],
            ['formacion', 'Formación', 'Cursos, lecturas y congresos', 'book', 'sky'],
            ['derivaciones', 'Derivaciones', 'Busca o recomienda a un colega', 'arrow', 'mint'],
            ['dudas-clinicas', 'Dudas clínicas', 'Sin datos que identifiquen a nadie', 'question', 'lav'],
        ]],
    ];

    $bienvenida = "Hola. Este hilo explica cómo funciona el foro.\n\n"
        . "1. Elige el subforo que más se parezca a tu tema.\n2. Pulsa «Abrir un hilo nuevo».\n3. Escribe un título claro y cuenta lo que quieras.\n\n"
        . "Normas básicas: trata a los demás con respeto, escribe claro (si es una broma, dilo) y no compartas datos privados de nadie.\n\n"
        . "Si algo te molesta, pulsa «Avisar a moderación». Lo revisa una persona.";

    $pos = 0;
    foreach ($forums as [$grp, $slug, $name, $desc, $icon, $color, $pro, $subs]) {
        $fid = insert('INSERT INTO forums (parent_id, grp, slug, name, description, icon, color, position, pro_only) VALUES (NULL,?,?,?,?,?,?,?,?)',
            [$grp, $slug, $name, $desc, $icon, $color, $pos++, $pro]);
        $firstSub = null;
        foreach ($subs as $i => [$s, $n, $d, $ic, $c]) {
            $sid = insert('INSERT INTO forums (parent_id, grp, slug, name, description, icon, color, position, pro_only) VALUES (?,?,?,?,?,?,?,?,?)',
                [$fid, $grp, $s, $n, $d, $ic, $c, $i, $pro]);
            $firstSub ??= $sid;
        }
        $title = 'Lee esto primero: normas del foro y cómo abrir tu primer hilo';
        $tid = insert('INSERT INTO threads (forum_id, user_id, title, slug, tone, pinned) VALUES (?,?,?,?,?,1)', [$firstSub, $adminId, $title, slugify($title), 'serio']);
        q('INSERT INTO posts (thread_id, user_id, body, tone, is_first) VALUES (?,?,?,?,1)', [$tid, $adminId, $bienvenida, 'serio']);
    }
}
