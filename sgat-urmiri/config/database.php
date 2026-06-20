<?php
// ============================================================
// SGAT-Urmiri — Configuración de Base de Datos
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sgat_urmiri');
define('DB_CHARSET', 'utf8mb4');

define('BASE_URL', 'http://localhost/sgat-urmiri/');
define('UPLOADS_PATH', __DIR__ . '/../uploads/habitaciones/');
define('UPLOADS_URL', BASE_URL . 'uploads/habitaciones/');

// ── Conexión PDO ────────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div class="alert alert-danger m-4">Error de conexión: ' . htmlspecialchars($e->getMessage()) . '</div>');
        }
    }
    return $pdo;
}
