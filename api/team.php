<?php
require 'config.php';

switch (action()) {

    case 'list':
        $stmt = db()->query('SELECT * FROM team ORDER BY order_num ASC, id ASC');
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) { $r['skills'] = jdec($r['skills']); $r['active'] = (bool)$r['active']; }
        json_ok($rows);

    case 'public':
        $stmt = db()->query("SELECT * FROM team WHERE active=1 ORDER BY order_num ASC, id ASC");
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) { $r['skills'] = jdec($r['skills']); $r['active'] = true; }
        json_ok($rows);

    case 'save':
        require_auth();
        $b = get_body();
        $name = trim($b['name'] ?? '');
        $role = trim($b['role'] ?? '');
        if (!$name || !$role) json_err('Nama dan jabatan wajib diisi');

        $fields = [
            'name'      => $name,
            'role'      => $role,
            'bio'       => trim($b['bio']      ?? ''),
            'skills'    => json_encode($b['skills'] ?? []),
            'linkedin'  => trim($b['linkedin'] ?? ''),
            'photo'     => trim($b['photo']    ?? ''),
            'color'     => (int)($b['color']   ?? 0),
            'active'    => $b['active'] ? 1 : 0,
            'order_num' => (int)($b['order_num'] ?? 99),
        ];

        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $set = implode(', ', array_map(fn($k) => "$k=:$k", array_keys($fields)));
            $stmt = db()->prepare("UPDATE team SET $set WHERE id=:id");
            $fields['id'] = $id;
            $stmt->execute($fields);
            json_ok(['id' => $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $vals = implode(', ', array_map(fn($k) => ":$k", array_keys($fields)));
            $stmt = db()->prepare("INSERT INTO team ($cols) VALUES ($vals)");
            $stmt->execute($fields);
            json_ok(['id' => (int)db()->lastInsertId()]);
        }

    case 'delete':
        require_auth();
        $b = get_body();
        $id = (int)($b['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');
        db()->prepare("DELETE FROM team WHERE id=?")->execute([$id]);
        json_ok();

    default:
        json_err('Unknown action');
}
