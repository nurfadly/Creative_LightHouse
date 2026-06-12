<?php
// ============================================================
// LIGHTHOUSE CMS — API Config
// Edit DB_HOST, DB_NAME, DB_USER, DB_PASS sesuai VPS Anda
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'lighthouse_cms');
define('DB_USER', 'lighthouse_user');  // ganti sesuai MySQL user Anda
define('DB_PASS', 'GantiPasswordIni'); // ganti dengan password MySQL Anda

define('UPLOAD_DIR',  __DIR__ . '/../uploads/');
define('UPLOAD_URL',  '/uploads/'); // URL path relatif ke domain

// ── CORS ──────────────────────────────────────────────────
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowed = [
    // Tambah domain GitHub Pages Anda di sini jika frontend di GitHub Pages
    'https://nurfadly.github.io',
    'http://localhost',
    'http://127.0.0.1',
];
if (in_array($origin, $allowed) || empty($origin)) {
    header('Access-Control-Allow-Origin: ' . ($origin ?: '*'));
} else {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

session_start();

// ── Database ──────────────────────────────────────────────
function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}

// ── Helpers ───────────────────────────────────────────────
function json_ok($data = null): void {
    echo json_encode(['success' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
    exit;
}

function json_err(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
    exit;
}

function require_auth(): void {
    if (empty($_SESSION['user'])) json_err('Unauthorized — silakan login', 401);
}

function get_body(): array {
    $raw = file_get_contents('php://input');
    return json_decode($raw, true) ?? [];
}

function action(): string {
    return $_GET['action'] ?? '';
}

function jdec($val): mixed {
    if (is_array($val)) return $val;
    return json_decode($val ?? '[]', true) ?? [];
}
