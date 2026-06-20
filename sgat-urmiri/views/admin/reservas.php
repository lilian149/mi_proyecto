<?php
// views/admin/reservas.php
require_once __DIR__ . '/../../config/helpers.php';
requireOperadorOrAdmin();
$db = getDB();

$filtroEstado = $_GET['estado'] ?? '';
$sql = "
  SELECT r.*, u.nombre_completo, h.nombre AS habitacion
  FROM reservas r
  JOIN usuarios u ON u.id = r.usuario_id
  JOIN habitaciones h ON h.id = r.habitacion_id
";
$params = [];
if ($filtroEstado) {
  $sql .= " WHERE r.estado=?";
  $params[] = $filtroEstado;
}
$sql .= " ORDER BY r.created_at DESC";
$st = $db->prepare($sql);
$st->execute($params);
$reservas = $st->fetchAll();

$totalIngresos = $db->query("SELECT COALESCE(SUM(total),0) FROM reservas WHERE estado='confirmada'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Gestión de Reservas — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>body{padding-top:0}</style>
</head>
<body>
<div class="d-flex">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="main-content flex-grow-1 bg-light">
    <div class="bg-white border-bottom px-4 py-3">
      <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-check"></i> Gestión de Reservas</h5>
    </div>
    <div class="p-4">
      <?php include __DIR__ . '/../partials/flash.php'; ?>

      <!-- Filtros de estado -->
      <div class="d-flex gap-2 mb-3 flex-wrap align-items-center justify-content-between">
        <div class="d-flex gap-2 flex-wrap">
          <a href="reservas.php" class="btn btn-sm <?= !$filtroEstado?'btn-dark':'btn-outline-dark' ?>">Todas</a>
          <a href="?estado=pendiente" class="btn btn-sm <?= $filtroEstado==='pendiente'?'btn-warning':'btn-outline-warning' ?>">Pendientes</a>
          <a href="?estado=confirmada" class="btn btn-sm <?= $filtroEstado==='confirmada'?'btn-success':'btn-outline-success' ?>">Confirmadas</a>
          <a href="?estado=cancelada" class="btn btn-sm <?= $filtroEstado==='cancelada'?'btn-danger':'btn-outline-danger' ?>">Canceladas</a>
        </div>
        <div class="fw-bold text-success">
          Ingresos confirmados: <?= bs((float)$totalIngresos) ?>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern table-hover mb-0">
              <thead>
                <tr>
                  <th>Código</th><th>Turista</th><th>Habitación</th>
                  <th>Ingreso</th><th>Salida</th><th>Noches</th>
                  <th>Total</th><th>Estado</th><th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php if (empty($reservas)): ?>
                <tr><td colspan="9" class="text-center py-4 text-muted">No hay reservas.</td></tr>
                <?php endif; ?>
                <?php foreach ($reservas as $r): ?>
                <tr>
                  <td><code><?= sanitize($r['codigo']) ?></code></td>
                  <td><?= sanitize($r['nombre_completo']) ?></td>
                  <td><?= sanitize($r['habitacion']) ?></td>
                  <td><?= date('d/m/Y', strtotime($r['fecha_ingreso'])) ?></td>
                  <td><?= date('d/m/Y', strtotime($r['fecha_salida'])) ?></td>
                  <td><?= $r['num_noches'] ?></td>
                  <td class="fw-bold"><?= bs((float)$r['total']) ?></td>
                  <td>
                    <?php $cls = match($r['estado']) {
                      'confirmada'=>'success','cancelada'=>'danger',default=>'warning'
                    }; ?>
                    <span class="badge bg-<?= $cls ?>"><?= strtoupper($r['estado']) ?></span>
                  </td>
                  <td>
                    <?php if ($r['estado'] === 'pendiente'): ?>
                    <a href="<?= BASE_URL ?>controllers/reserva_controller.php?action=confirmar&id=<?= $r['id'] ?>"
                       class="btn btn-success btn-sm" title="Confirmar">
                      <i class="bi bi-check-lg"></i>
                    </a>
                    <a href="<?= BASE_URL ?>controllers/reserva_controller.php?action=cancelar&id=<?= $r['id'] ?>"
                       class="btn btn-danger btn-sm" title="Cancelar"
                       onclick="return confirm('¿Cancelar reserva?')">
                      <i class="bi bi-x-lg"></i>
                    </a>
                    <?php else: ?>
                    <span class="text-muted small">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
