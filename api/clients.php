<?php
require 'config.php';

switch (action()) {

    case 'list':
        $stmt = db()->query('SELECT * FROM clients ORDER BY order_num ASC, id ASC');
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) { $r['active'] = (bool)$r['active']; }
        json_ok($rows);

    case 'public':
        $stmt = db()->query("SELECT * FROM clients WHERE active=1 ORDER BY order_num ASC, id ASC");
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) { $r['active'] = true; }
        json_ok($rows);

    case 'save':
        require_auth();
        $b    = get_body();
        $name = trim($b['name'] ?? '');
        if (!$name) json_err('Nama klien wajib diisi');
        if (empty($b['logo'])) json_err('Logo klien wajib diupload');

        $fields = [
            'name'      => $name,
            'logo'      => trim($b['logo']),
            'active'    => $b['active'] ? 1 : 0,
            'order_num' => (int)($b['order_num'] ?? 99),
        ];

        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $set = implode(', ', array_map(fn($k) => "$k=:$k", array_keys($fields)));
            $stmt = db()->prepare("UPDATE clients SET $set WHERE id=:id");
            $fields['id'] = $id;
            $stmt->execute($fields);
            json_ok(['id' => $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $vals = implode(', ', array_map(fn($k) => ":$k", array_keys($fields)));
            db()->prepare("INSERT INTO clients ($cols) VALUES ($vals)")->execute($fields);
            json_ok(['id' => (int)db()->lastInsertId()]);
        }

    case 'delete':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');
        db()->prepare("DELETE FROM clients WHERE id=?")->execute([$id]);
        json_ok();

    default:
        json_err('Unknown action');
}
