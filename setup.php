<?php
// ============================================================
// LIGHTHOUSE CMS — Setup Script
// Jalankan SEKALI setelah import db.sql untuk set password admin
// Lalu HAPUS file ini dari server!
// URL: https://yourdomain.com/setup.php
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'lighthouse_cms');
define('DB_USER', 'lighthouse_user');  // sama seperti di api/config.php
define('DB_PASS', 'GantiPasswordIni'); // sama seperti di api/config.php

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER, DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $password = 'Rahasia@123';
    $hash     = password_hash($password, PASSWORD_DEFAULT);

    // Update password semua user
    $stmt = $pdo->prepare("UPDATE users SET password = ?");
    $stmt->execute([$hash]);

    echo '<h2 style="color:green">✅ Setup berhasil!</h2>';
    echo '<p>Password untuk semua akun telah diset ke: <strong>' . htmlspecialchars($password) . '</strong></p>';
    echo '<p><strong style="color:red">⚠️ PENTING: Hapus file setup.php dari server sekarang!</strong></p>';
    echo '<ul>';
    echo '<li>Admin: admin@lighthouse.id / ' . htmlspecialchars($password) . '</li>';
    echo '<li>Sales: sales@lighthouse.id / ' . htmlspecialchars($password) . '</li>';
    echo '</ul>';
    echo '<p><a href="lighthouse-cms.html">Buka CMS →</a></p>';

} catch (Exception $e) {
    echo '<h2 style="color:red">❌ Error</h2>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p>Pastikan DB_HOST, DB_NAME, DB_USER, DB_PASS sudah benar di file ini dan di api/config.php</p>';
}
