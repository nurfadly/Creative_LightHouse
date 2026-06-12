<?php
require 'config.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_err('Method not allowed', 405);

$file = $_FILES['file'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    json_err('Upload gagal. Coba lagi.');
}

// Validasi tipe file
$allowed_types = ['image/jpeg','image/png','image/gif','image/webp','image/svg+xml'];
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->file($file['tmp_name']);
if (!in_array($mime, $allowed_types)) {
    json_err('Tipe file tidak diizinkan. Gunakan JPG, PNG, GIF, WebP, atau SVG.');
}

// Batasi ukuran (5 MB)
if ($file['size'] > 5 * 1024 * 1024) {
    json_err('Ukuran file maksimal 5 MB.');
}

// Pastikan folder uploads ada dan bisa ditulis
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Buat nama file unik
$ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
$basename = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', pathinfo($file['name'], PATHINFO_FILENAME)));
$filename = $basename . '_' . uniqid() . '.' . $ext;
$dest     = UPLOAD_DIR . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    json_err('Gagal menyimpan file. Periksa permission folder uploads/');
}

json_ok([
    'url'      => UPLOAD_URL . $filename,
    'filename' => $filename,
]);
