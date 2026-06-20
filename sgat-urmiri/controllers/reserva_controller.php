<?php
// ============================================================
// controllers/reserva_controller.php
// ============================================================
require_once __DIR__ . '/../config/helpers.php';

$action = $_GET['action'] ?? '';

// ── AJAX: Verificar disponibilidad ─────────────────────────
if ($action === 'verificar_disponibilidad' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');
  $habId   = (int)($_POST['habitacion_id'] ?? 0);
  $ingreso = $_POST['fecha_ingreso'] ?? '';
  $salida  = $_POST['fecha_salida']  ?? '';

  if (!$habId || !$ingreso || !$salida) {
    echo json_encode(['disponible' => false]);
    exit;
  }

  $db = getDB();
  $st = $db->prepare('SELECT precio_noche, estado FROM habitaciones WHERE id=?');
  $st->execute([$habId]);
  $hab = $st->fetch();

  if (!$hab || $hab['estado'] === 'mantenimiento') {
    echo json_encode(['disponible' => false]);
    exit;
  }

  // Verificar conflictos de fechas
  $st = $db->prepare("
    SELECT COUNT(*) FROM reservas
    WHERE habitacion_id = ?
      AND estado IN ('pendiente','confirmada')
      AND NOT (fecha_salida <= ? OR fecha_ingreso >= ?)
  ");
  $st->execute([$habId, $ingreso, $salida]);
  $conflictos = $st->fetchColumn();

  echo json_encode([
    'disponible'   => ($conflictos == 0),
    'precio_noche' => $hab['precio_noche']
  ]);
  exit;
}

// ── Crear reserva ───────────────────────────────────────────
if ($action === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  requireLogin();

  $habId    = (int)($_POST['habitacion_id'] ?? 0);
  $ingreso  = $_POST['fecha_ingreso'] ?? '';
  $salida   = $_POST['fecha_salida']  ?? '';
  $personas = (int)($_POST['cantidad_personas'] ?? 1);
  $noches   = (int)($_POST['num_noches'] ?? 0);
  $total    = (float)($_POST['total'] ?? 0);
  $precio   = (float)($_POST['precio_noche'] ?? 0);
  $obs      = trim($_POST['observaciones'] ?? '');

  if (!$habId || !$ingreso || !$salida || $noches < 1 || $total <= 0) {
    redirect(BASE_URL . 'views/reservas/nueva_reserva.php', 'error', 'Datos de reserva inválidos.');
  }

  $db = getDB();

  // Verificar disponibilidad final
  $st = $db->prepare("
    SELECT COUNT(*) FROM reservas
    WHERE habitacion_id=? AND estado IN ('pendiente','confirmada')
      AND NOT (fecha_salida<=? OR fecha_ingreso>=?)
  ");
  $st->execute([$habId, $ingreso, $salida]);
  if ($st->fetchColumn() > 0) {
    redirect(BASE_URL . 'views/reservas/nueva_reserva.php?hab=' . $habId,
             'error', 'La habitación ya no está disponible en esas fechas.');
  }

  // Insertar reserva
  $codigo = generarCodigo();
  $db->prepare("
    INSERT INTO reservas (codigo,usuario_id,habitacion_id,fecha_ingreso,fecha_salida,
                          cantidad_personas,num_noches,precio_noche,total,observaciones)
    VALUES (?,?,?,?,?,?,?,?,?,?)
  ")->execute([
    $codigo, $_SESSION['usuario_id'], $habId, $ingreso, $salida,
    $personas, $noches, $precio, $total, $obs
  ]);

  // ✅ ACTUALIZAR ESTADO DE HABITACIÓN A OCUPADA
  $db->prepare("UPDATE habitaciones SET estado='ocupada' WHERE id=?")
     ->execute([$habId]);

  redirect(BASE_URL . 'views/reservas/mis_reservas.php',
           'success', "¡Reserva creada! Código: $codigo. Total: " . bs($total));
}

// ── Cancelar reserva ─────────────────────────────────────────
if ($action === 'cancelar') {
  requireLogin();
  $id = (int)($_GET['id'] ?? 0);
  $db = getDB();

  $st = $db->prepare('SELECT * FROM reservas WHERE id=?');
  $st->execute([$id]);
  $reserva = $st->fetch();

  if (!$reserva || ($reserva['usuario_id'] != $_SESSION['usuario_id'] && $_SESSION['rol'] !== 'administrador')) {
    redirect(BASE_URL . 'views/reservas/mis_reservas.php', 'error', 'Acceso no permitido.');
  }

  // Cancelar la reserva
  $db->prepare("UPDATE reservas SET estado='cancelada' WHERE id=?")
     ->execute([$id]);

  // ✅ VOLVER A DISPONIBLE si no hay otras reservas activas para esa habitación
  $st = $db->prepare("
    SELECT COUNT(*) FROM reservas
    WHERE habitacion_id=? AND estado IN ('pendiente','confirmada') AND id!=?
  ");
  $st->execute([$reserva['habitacion_id'], $id]);
  $otrasActivas = $st->fetchColumn();

  if ($otrasActivas == 0) {
    $db->prepare("UPDATE habitaciones SET estado='disponible' WHERE id=?")
       ->execute([$reserva['habitacion_id']]);
  }

  $redirect = $_SESSION['rol'] === 'administrador'
    ? BASE_URL . 'views/admin/reservas.php'
    : BASE_URL . 'views/reservas/mis_reservas.php';

  redirect($redirect, 'success', 'Reserva cancelada. La habitación volvió a estar disponible.');
}

// ── Confirmar reserva (admin/operador) ───────────────────────
if ($action === 'confirmar') {
  requireOperadorOrAdmin();
  $id = (int)($_GET['id'] ?? 0);
  $db = getDB();

  $st = $db->prepare('SELECT habitacion_id FROM reservas WHERE id=?');
  $st->execute([$id]);
  $reserva = $st->fetch();

  $db->prepare("UPDATE reservas SET estado='confirmada' WHERE id=?")
     ->execute([$id]);

  // ✅ ASEGURAR QUE LA HABITACIÓN QUEDE OCUPADA al confirmar
  if ($reserva) {
    $db->prepare("UPDATE habitaciones SET estado='ocupada' WHERE id=?")
       ->execute([$reserva['habitacion_id']]);
  }

  redirect(BASE_URL . 'views/admin/reservas.php', 'success', 'Reserva confirmada.');
}

// ── Completar reserva (check-out) ─────────────────────────────
if ($action === 'completar') {
  requireOperadorOrAdmin();
  $id = (int)($_GET['id'] ?? 0);
  $db = getDB();

  $st = $db->prepare('SELECT habitacion_id FROM reservas WHERE id=?');
  $st->execute([$id]);
  $reserva = $st->fetch();

  $db->prepare("UPDATE reservas SET estado='completada' WHERE id=?")
     ->execute([$id]);

  // ✅ LIBERAR HABITACIÓN al completar (check-out)
  if ($reserva) {
    $st = $db->prepare("
      SELECT COUNT(*) FROM reservas
      WHERE habitacion_id=? AND estado IN ('pendiente','confirmada') AND id!=?
    ");
    $st->execute([$reserva['habitacion_id'], $id]);
    $otrasActivas = $st->fetchColumn();

    if ($otrasActivas == 0) {
      $db->prepare("UPDATE habitaciones SET estado='disponible' WHERE id=?")
         ->execute([$reserva['habitacion_id']]);
    }
  }

  redirect(BASE_URL . 'views/admin/reservas.php', 'success', 'Reserva completada. Habitación liberada.');
}

redirect(BASE_URL . 'index.php');
