<?php
require 'config.php';

switch (action()) {

    // ── Login ─────────────────────────────────────────────
    case 'login':
        $b     = get_body();
        $email = trim($b['email'] ?? '');
        $pass  = $b['password'] ?? '';
        if (!$email || !$pass) json_err('Email dan password wajib diisi');

        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($pass, $user['password'])) {
            json_err('Email atau password salah');
        }

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ];
        json_ok($_SESSION['user']);

    // ── Logout ────────────────────────────────────────────
    case 'logout':
        session_destroy();
        json_ok();

    // ── Check session ─────────────────────────────────────
    case 'me':
        json_ok($_SESSION['user'] ?? null);

    default:
        json_err('Unknown action');
}
