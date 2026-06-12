<?php
require 'config.php';

switch (action()) {

    // ── List all ──────────────────────────────────────────
    case 'list':
        $stmt = db()->query('SELECT * FROM projects ORDER BY order_num ASC, created_at DESC');
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['tech']       = jdec($r['tech']);
            $r['challenges'] = jdec($r['challenges']);
            $r['results']    = jdec($r['results']);
            $r['metrics']    = jdec($r['metrics']);
            $r['gallery']    = jdec($r['gallery']);
        }
        json_ok($rows);

    // ── Single project (for portfolio-detail page) ────────
    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');
        $stmt = db()->prepare('SELECT * FROM projects WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $r = $stmt->fetch();
        if (!$r) json_err('Proyek tidak ditemukan', 404);
        $r['tech']       = jdec($r['tech']);
        $r['challenges'] = jdec($r['challenges']);
        $r['results']    = jdec($r['results']);
        $r['metrics']    = jdec($r['metrics']);
        $r['gallery']    = jdec($r['gallery']);
        json_ok($r);

    // ── Public list (only published) ─────────────────────
    case 'public':
        $stmt = db()->query("SELECT * FROM projects WHERE status='published' ORDER BY order_num ASC, created_at DESC");
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) {
            $r['tech']    = jdec($r['tech']);
            $r['gallery'] = jdec($r['gallery']);
            $r['metrics'] = jdec($r['metrics']);
        }
        json_ok($rows);

    // ── Save (insert / update) ────────────────────────────
    case 'save':
        require_auth();
        $b = get_body();

        $title  = trim($b['title'] ?? '');
        $cat    = trim($b['cat']   ?? '');
        if (!$title || !$cat) json_err('Judul dan kategori wajib diisi');

        $fields = [
            'title'      => $title,
            'client'     => trim($b['client']    ?? ''),
            'year'       => (int)($b['year']     ?? date('Y')),
            'cat'        => $cat,
            'short_desc' => trim($b['short']     ?? ''),
            'long_desc'  => trim($b['long']      ?? ''),
            'challenge'  => trim($b['challenge'] ?? ''),
            'solution'   => trim($b['solution']  ?? ''),
            'duration'   => trim($b['duration']  ?? ''),
            'scope'      => trim($b['scope']     ?? ''),
            'tech'       => json_encode($b['tech']       ?? []),
            'challenges' => json_encode($b['challenges'] ?? []),
            'results'    => json_encode($b['results']    ?? []),
            'metrics'    => json_encode($b['metrics']    ?? []),
            'thumbnail'  => trim($b['thumbnail'] ?? ''),
            'gallery'    => json_encode($b['gallery']    ?? []),
            'color'      => (int)($b['color']    ?? 0),
            'status'     => in_array($b['status']??'', ['published','draft']) ? $b['status'] : 'published',
            'order_num'  => (int)($b['order_num'] ?? 99),
        ];

        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($fields)));
            $stmt = db()->prepare("UPDATE projects SET $set WHERE id = :id");
            $fields['id'] = $id;
            $stmt->execute($fields);
            json_ok(['id' => $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $vals = implode(', ', array_map(fn($k) => ":$k", array_keys($fields)));
            $stmt = db()->prepare("INSERT INTO projects ($cols) VALUES ($vals)");
            $stmt->execute($fields);
            json_ok(['id' => (int)db()->lastInsertId()]);
        }

    // ── Toggle status ─────────────────────────────────────
    case 'toggle':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        $st = $b['status'] === 'published' ? 'published' : 'draft';
        db()->prepare("UPDATE projects SET status=? WHERE id=?")->execute([$st, $id]);
        json_ok();

    // ── Delete ────────────────────────────────────────────
    case 'delete':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');
        db()->prepare("DELETE FROM projects WHERE id=?")->execute([$id]);
        json_ok();

    default:
        json_err('Unknown action');
}
