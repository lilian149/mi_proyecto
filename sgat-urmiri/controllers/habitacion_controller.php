<?php
// ============================================================
// controllers/habitacion_controller.php
// ============================================================
require_once __DIR__ . '/../config/helpers.php';
requireOperadorOrAdmin();

$action = $_GET['action'] ?? '';

// ── Crear / Editar ──────────────────────────────────────────
if (in_array($action, ['crear', 'editar']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre      = trim($_POST['nombre'] ?? '');
  $descripcion = trim($_POST['descripcion'] ?? '');
  $capacidad   = (int)($_POST['capacidad'] ?? 1);
  $precio      = (float)($_POST['precio_noche'] ?? 0);
  $estado      = $_POST['estado'] ?? 'disponible';
  $id          = (int)($_POST['id'] ?? 0);

  if (!$nombre || $precio <= 0) {
    redirect(BASE_URL . 'views/admin/habitaciones.php', 'error', 'Nombre y precio son obligatorios.');
  }

  $db = getDB();
  $imagenNombre = null;

  // Subida de imagen
  if (!empty($_FILES['imagen']['name'])) {
    $ext       = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($ext, $permitidos)) {
      redirect(BASE_URL . 'views/admin/habitaciones.php', 'error', 'Formato de imagen no permitido.');
    }
    $imagenNombre = uniqid('hab_') . '.' . $ext;
    $destino = UPLOADS_PATH . $imagenNombre;
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
      redirect(BASE_URL . 'views/admin/habitaciones.php', 'error', 'Error al subir la imagen.');
    }
  }

  if ($action === 'crear') {
    $db->prepare("INSERT INTO habitaciones (nombre,descripcion,capacidad,precio_noche,estado,imagen)
                  VALUES (?,?,?,?,?,?)")
       ->execute([$nombre, $descripcion, $capacidad, $precio, $estado,
                  $imagenNombre ?? 'default.jpg']);
    redirect(BASE_URL . 'views/admin/habitaciones.php', 'success', 'Habitación creada correctamente.');

  } else {
    if ($imagenNombre) {
      $db->prepare("UPDATE habitaciones SET nombre=?,descripcion=?,capacidad=?,precio_noche=?,estado=?,imagen=? WHERE id=?")
         ->execute([$nombre, $descripcion, $capacidad, $precio, $estado, $imagenNombre, $id]);
    } else {
      $db->prepare("UPDATE habitaciones SET nombre=?,descripcion=?,capacidad=?,precio_noche=?,estado=? WHERE id=?")
         ->execute([$nombre, $descripcion, $capacidad, $precio, $estado, $id]);
    }
    redirect(BASE_URL . 'views/admin/habitaciones.php', 'success', 'Habitación actualizada.');
  }
}

// ── Eliminar ────────────────────────────────────────────────
if ($action === 'eliminar') {
  requireAdmin();
  $id = (int)($_GET['id'] ?? 0);
  getDB()->prepare('DELETE FROM habitaciones WHERE id=?')->execute([$id]);
  redirect(BASE_URL . 'views/admin/habitaciones.php', 'success', 'Habitación eliminada.');
}

redirect(BASE_URL . 'views/admin/habitaciones.php');
