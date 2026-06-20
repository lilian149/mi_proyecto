<?php
// ============================================================
// controllers/auth_controller.php
// ============================================================
require_once __DIR__ . '/../config/helpers.php';

$action = $_GET['action'] ?? '';

switch ($action) {

  // ── Registro ────────────────────────────────────────────
  case 'registro':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      redirect(BASE_URL . 'views/auth/registro.php');
    }

    $nombre   = trim($_POST['nombre_completo'] ?? '');
    $ci       = trim($_POST['ci'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $pass     = $_POST['password'] ?? '';
    $pass2    = $_POST['password2'] ?? '';

    if (!$nombre || !$ci || !$correo || !$pass) {
      redirect(BASE_URL . 'views/auth/registro.php', 'error', 'Todos los campos obligatorios son requeridos.');
    }
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
      redirect(BASE_URL . 'views/auth/registro.php', 'error', 'El correo no es válido.');
    }
    if (strlen($pass) < 6) {
      redirect(BASE_URL . 'views/auth/registro.php', 'error', 'La contraseña debe tener al menos 6 caracteres.');
    }
    if ($pass !== $pass2) {
      redirect(BASE_URL . 'views/auth/registro.php', 'error', 'Las contraseñas no coinciden.');
    }

    $db = getDB();
    // Verificar CI o correo duplicado
    $st = $db->prepare('SELECT id FROM usuarios WHERE ci=? OR correo=?');
    $st->execute([$ci, $correo]);
    if ($st->fetch()) {
      redirect(BASE_URL . 'views/auth/registro.php', 'error', 'El CI o correo ya están registrados.');
    }

    $hash = password_hash($pass, PASSWORD_BCRYPT);
    $db->prepare('INSERT INTO usuarios (nombre_completo,ci,correo,telefono,password) VALUES (?,?,?,?,?)')
       ->execute([$nombre, $ci, $correo, $telefono, $hash]);

    redirect(BASE_URL . 'views/auth/login.php', 'success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
    break;

  // ── Login ───────────────────────────────────────────────
  case 'login':
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      redirect(BASE_URL . 'views/auth/login.php');
    }

    $correo = trim($_POST['correo'] ?? '');
    $pass   = $_POST['password'] ?? '';

    if (!$correo || !$pass) {
      redirect(BASE_URL . 'views/auth/login.php', 'error', 'Ingresa tu correo y contraseña.');
    }

    $db = getDB();
    $st = $db->prepare('SELECT * FROM usuarios WHERE correo=? AND activo=1');
    $st->execute([$correo]);
    $user = $st->fetch();

    if (!$user || !password_verify($pass, $user['password'])) {
      redirect(BASE_URL . 'views/auth/login.php', 'error', 'Credenciales incorrectas.');
    }

    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['nombre']     = $user['nombre_completo'];
    $_SESSION['rol']        = $user['rol'];
    $_SESSION['correo']     = $user['correo'];

    if ($user['rol'] === 'administrador' || $user['rol'] === 'operador') {
      redirect(BASE_URL . 'views/admin/dashboard.php', 'success', 'Bienvenido, ' . $user['nombre_completo'] . '!');
    } else {
      redirect(BASE_URL . 'index.php', 'success', 'Bienvenido, ' . $user['nombre_completo'] . '!');
    }
    break;

  // ── Logout ──────────────────────────────────────────────
  case 'logout':
    session_destroy();
    redirect(BASE_URL . 'index.php', 'success', 'Sesión cerrada correctamente.');
    break;

  default:
    redirect(BASE_URL . 'index.php');
}
