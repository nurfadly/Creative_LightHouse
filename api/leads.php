<?php
require 'config.php';

switch (action()) {

    case 'list':
        require_auth();
        $stmt = db()->query('SELECT * FROM leads ORDER BY created_at DESC');
        json_ok($stmt->fetchAll());

    case 'submit':
        // Public: dipakai dari halaman kontak
        $b = get_body();
        $name = trim($b['name'] ?? '');
        if (!$name) json_err('Nama wajib diisi');

        $stmt = db()->prepare("INSERT INTO leads (name, email, phone, company, service, message, status) VALUES (?,?,?,?,?,?,'Baru')");
        $stmt->execute([
            $name,
            trim($b['email']   ?? ''),
            trim($b['phone']   ?? ''),
            trim($b['company'] ?? ''),
            trim($b['service'] ?? ''),
            trim($b['message'] ?? ''),
        ]);
        json_ok(['id' => (int)db()->lastInsertId()]);

    case 'update':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');

        $allowed_status = ['Baru','Diproses','Selesai','Tidak Relevan'];
        $status = in_array($b['status'] ?? '', $allowed_status) ? $b['status'] : 'Baru';

        $stmt = db()->prepare("UPDATE leads SET status=?, notes=?, updated_by=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([
            $status,
            trim($b['notes'] ?? ''),
            $_SESSION['user']['name'] ?? '',
            $id,
        ]);
        json_ok();

    case 'delete':
        require_auth();
        $b  = get_body();
        $id = (int)($b['id'] ?? 0);
        if (!$id) json_err('ID diperlukan');
        db()->prepare("DELETE FROM leads WHERE id=?")->execute([$id]);
        json_ok();

    default:
        json_err('Unknown action');
}
