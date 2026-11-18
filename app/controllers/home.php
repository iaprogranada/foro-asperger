<?php
/** Portada, buscador y páginas de texto. */

function home_index(): void
{
    $forums = rows('SELECT * FROM forums WHERE parent_id IS NULL ORDER BY position');
    $groups = ['quien' => [], 'etapa' => [], 'tema' => [], 'pro' => []];
    foreach ($forums as $f) $groups[$f['grp']][] = $f;
    view('home', ['active' => 'foros', 'groups' => $groups]);
}

function home_search(): void
{
    $term = trim((string) ($_GET['q'] ?? ''));
    $threads = [];
    if (mb_strlen($term) >= 2) {
        $like = '%' . addcslashes($term, '%_\\') . '%';
        $proOk = (is_pro() || is_mod()) ? '' : ' AND f.pro_only = 0';
        $threads = rows('SELECT ' . THREAD_COLS . ' ' . THREAD_FROM . " WHERE t.deleted_at IS NULL{$proOk} AND (t.title LIKE ? OR EXISTS (SELECT 1 FROM posts p WHERE p.thread_id = t.id AND p.deleted_at IS NULL AND p.body LIKE ?)) ORDER BY t.last_post_at DESC LIMIT 40", [$like, $like]);
    }
    view('search', ['title' => 'Buscar', 'active' => 'foros', 'term' => $term, 'threads' => $threads]);
}

function home_page(string $page): void
{
    $titles = ['normas' => 'Normas de la comunidad', 'privacidad' => 'Privacidad', 'accesibilidad' => 'Accesibilidad', 'contacto' => 'Contacto'];
    view('pages/' . $page, ['title' => $titles[$page]]);
}
