<?php
require 'config.php';

switch (action()) {

    case 'list':
        $stmt = db()->query('SELECT * FROM services ORDER BY order_num ASC, id ASC');
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) { $r['active'] = (bool)$r['active']; }
        json_ok($rows);

    case 'public':
        $stmt = db()->query("SELECT * FROM services WHERE active=1 ORDER BY order_num ASC, id ASC");
        $rows = $stmt->fetchAll();
        foreach ($rows as &$r) { $r['active'] = true; }
        json_ok($rows);

    case 'save':
        require_auth();
        $b    = get_body();
        $name = trim($b['name'] ?? '');
        if (!$name) json_err('Nama layanan wajib diisi');

        $fields = [
            'name'        => $name,
            'description' => trim($b['desc']     ?? ''),
            'icon'        => trim($b['icon']      ?? 'web'),
            'active'      => $b['active'] ? 1 : 0,
            'order_num'   => (int)($b['order_num'] ?? 99),
        ];

        $id = (int)($b['id'] ?? 0);
        if ($id) {
            $set = implode(', ', array_map(fn($k) => "$k=:$k", array_keys($fields)));
            $stmt = db()->prepare("UPDATE services SET $set WHERE id=:id");
            $fields['id'] = $id;
            $stmt->execute($fields);
            json_ok(['id' => $id]);
        } else {
            $cols = implode(', ', array_keys($fields));
            $vals = implode(', ', array_map(fn($k) => ":$k", array_keys($fields)));
            db()->prepare("INSERT INTO services ($cols) VALUES ($vals)")->execute($fields);
            json_ok(['id' => (int)db()->lastInsertId()]);
        }

    case 'toggle':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        db()->prepare("UPDATE services SET active=? WHERE id=?")->execute([$b['active'] ? 1 : 0, $id]);
        json_ok();

    case 'delete':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');
        db()->prepare("DELETE FROM services WHERE id=?")->execute([$id]);
        json_ok();

    default:
        json_err('Unknown action');
}
