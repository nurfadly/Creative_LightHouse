<?php
require 'config.php';

switch (action()) {

    case 'list':
        $stmt = db()->query('SELECT * FROM testimonials ORDER BY id DESC');
        json_ok($stmt->fetchAll());

    case 'public':
        $stmt = db()->query("SELECT * FROM testimonials WHERE status='published' ORDER BY id ASC");
        json_ok($stmt->fetchAll());

    case 'save':
        require_auth();
        $b     = get_body();
        $quote = trim($b['quote'] ?? '');
        $name  = trim($b['name']  ?? '');
        if (!$quote || !$name) json_err('Kutipan dan nama wajib diisi');

        $fields = [
            'name'    => $name,
            'role'    => trim($b['role']    ?? ''),
            'company' => trim($b['company'] ?? ''),
            'quote'   => $quote,
            'rating'  => max(1, min(5, (int)($b['rating'] ?? 5))),
            'status'  => $b['status'] === 'draft' ? 'draft' : 'published',
        ];

        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $set = implode(', ', array_map(fn($k) => "$k=:$k", array_keys($fields)));
            $stmt = db()->prepare("UPDATE testimonials SET $set WHERE id=:id");
            $fields['id'] = $id;
            $stmt->execute($fields);
            json_ok(['id' => $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $vals = implode(', ', array_map(fn($k) => ":$k", array_keys($fields)));
            db()->prepare("INSERT INTO testimonials ($cols) VALUES ($vals)")->execute($fields);
            json_ok(['id' => (int)db()->lastInsertId()]);
        }

    case 'toggle':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        $st = $b['status'] === 'published' ? 'published' : 'draft';
        db()->prepare("UPDATE testimonials SET status=? WHERE id=?")->execute([$st, $id]);
        json_ok();

    case 'delete':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');
        db()->prepare("DELETE FROM testimonials WHERE id=?")->execute([$id]);
        json_ok();

    default:
        json_err('Unknown action');
}
