<?php
// views/admin/dashboard.php
require_once __DIR__ . '/../../config/helpers.php';
requireOperadorOrAdmin();

$db = getDB();

// Estadísticas
$totalReservas    = $db->query("SELECT COUNT(*) FROM reservas")->fetchColumn();
$reservasPend     = $db->query("SELECT COUNT(*) FROM reservas WHERE estado='pendiente'")->fetchColumn();
$habDisponibles   = $db->query("SELECT COUNT(*) FROM habitaciones WHERE estado='disponible'")->fetchColumn();
$habOcupadas      = $db->query("SELECT COUNT(*) FROM habitaciones WHERE estado='ocupada'")->fetchColumn();
$ingresosTotal    = $db->query("SELECT COALESCE(SUM(total),0) FROM reservas WHERE estado='confirmada'")->fetchColumn();
$totalUsuarios    = $db->query("SELECT COUNT(*) FROM usuarios WHERE rol='turista'")->fetchColumn();

// Últimas 5 reservas
$ultimasReservas  = $db->query("
  SELECT r.*, u.nombre_completo, h.nombre AS habitacion
  FROM reservas r
  JOIN usuarios u ON u.id = r.usuario_id
  JOIN habitaciones h ON h.id = r.habitacion_id
  ORDER BY r.created_at DESC LIMIT 5
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>body{padding-top:0}</style>
</head>
<body>
<div class="d-flex">

  <!-- ── Sidebar ────────────────────────────────────────── -->
  <?php include __DIR__ . '/sidebar.php'; ?>

  <!-- ── Contenido principal ──────────────────────────── -->
  <div class="main-content flex-grow-1 bg-light">
    <!-- Topbar -->
    <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold">📊 Dashboard</h5>
      <div class="d-flex align-items-center gap-3">
        <span class="text-muted small">Hola, <strong><?= sanitize($_SESSION['nombre']) ?></strong></span>
        <a href="<?= BASE_URL ?>controllers/auth_controller.php?action=logout"
           class="btn btn-outline-danger btn-sm">
          <i class="bi bi-box-arrow-right"></i> Salir
        </a>
      </div>
    </div>

    <div class="p-4">
      <?php include __DIR__ . '/../partials/flash.php'; ?>

      <!-- Tarjetas estadísticas -->
      <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
          <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="icon-wrap bg-primary bg-opacity-10">
                <i class="bi bi-calendar-check text-primary"></i>
              </div>
              <div>
                <div class="fs-2 fw-bold"><?= $totalReservas ?></div>
                <small class="text-muted">Total Reservas</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="icon-wrap bg-success bg-opacity-10">
                <i class="bi bi-house-check text-success"></i>
              </div>
              <div>
                <div class="fs-2 fw-bold text-success"><?= $habDisponibles ?></div>
                <small class="text-muted">Disponibles</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="icon-wrap bg-danger bg-opacity-10">
                <i class="bi bi-house-fill text-danger"></i>
              </div>
              <div>
                <div class="fs-2 fw-bold text-danger"><?= $habOcupadas ?></div>
                <small class="text-muted">Ocupadas</small>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="card stat-card">
            <div class="card-body d-flex align-items-center gap-3">
              <div class="icon-wrap bg-warning bg-opacity-10">
                <i class="bi bi-cash-stack text-warning"></i>
              </div>
              <div>
                <div class="fs-4 fw-bold text-warning"><?= bs((float)$ingresosTotal) ?></div>
                <small class="text-muted">Ingresos Confirmados</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Reservas pendientes + turistas -->
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <div class="card stat-card border-start border-4 border-warning">
            <div class="card-body">
              <h6 class="text-muted">Reservas Pendientes</h6>
              <div class="fs-1 fw-bold text-warning"><?= $reservasPend ?></div>
              <a href="reservas.php?estado=pendiente" class="btn btn-warning btn-sm mt-2">Ver todas</a>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card stat-card border-start border-4 border-info">
            <div class="card-body">
              <h6 class="text-muted">Turistas Registrados</h6>
              <div class="fs-1 fw-bold text-info"><?= $totalUsuarios ?></div>
              <a href="usuarios.php" class="btn btn-info btn-sm mt-2 text-white">Ver usuarios</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Últimas reservas -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between">
          <h6 class="fw-bold mb-0">Últimas Reservas</h6>
          <a href="reservas.php" class="btn btn-sm btn-outline-success">Ver todas</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern table-hover mb-0">
              <thead>
                <tr>
                  <th>Código</th><th>Turista</th><th>Habitación</th>
                  <th>Ingreso</th><th>Total</th><th>Estado</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($ultimasReservas as $r): ?>
                <tr>
                  <td><code><?= sanitize($r['codigo']) ?></code></td>
                  <td><?= sanitize($r['nombre_completo']) ?></td>
                  <td><?= sanitize($r['habitacion']) ?></td>
                  <td><?= date('d/m/Y', strtotime($r['fecha_ingreso'])) ?></td>
                  <td class="fw-bold"><?= bs((float)$r['total']) ?></td>
                  <td>
                    <?php
                      $cls = match($r['estado']) {
                        'confirmada'=>'success','cancelada'=>'danger',default=>'warning'
                      };
                    ?>
                    <span class="badge bg-<?= $cls ?>"><?= strtoupper($r['estado']) ?></span>
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
