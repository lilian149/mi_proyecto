<?php
// ============================================================
// SGAT-Urmiri — Helpers y Sesión
// ============================================================

session_start();

require_once __DIR__ . '/database.php';

// ── Autenticación ───────────────────────────────────────────
function isLoggedIn(): bool {
    return isset($_SESSION['usuario_id']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . 'views/auth/login.php');
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if ($_SESSION['rol'] !== 'administrador') {
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

function requireOperadorOrAdmin(): void {
    requireLogin();
    if (!in_array($_SESSION['rol'], ['administrador', 'operador'])) {
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

// ── Flash Messages ──────────────────────────────────────────
function setFlash(string $tipo, string $msg): void {
    $_SESSION['flash'] = ['tipo' => $tipo, 'msg' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

// ── Sanitización ────────────────────────────────────────────
function sanitize(string $val): string {
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}

// ── Formato moneda boliviana ─────────────────────────────────
function bs(float $n): string {
    return 'Bs. ' . number_format($n, 2, '.', ',');
}

// ── Generar código único de reserva ─────────────────────────
function generarCodigo(): string {
    return 'RES-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Y');
}

// ── Redirect con mensaje ─────────────────────────────────────
function redirect(string $url, string $tipo = '', string $msg = ''): void {
    if ($tipo && $msg) setFlash($tipo, $msg);
    header('Location: ' . $url);
    exit;
}
