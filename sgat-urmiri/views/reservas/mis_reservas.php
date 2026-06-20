<?php
// views/reservas/mis_reservas.php
require_once __DIR__ . '/../../config/helpers.php';
requireLogin();

$db = getDB();
$st = $db->prepare("
  SELECT r.*, h.nombre AS habitacion, h.imagen
  FROM reservas r
  JOIN habitaciones h ON h.id = r.habitacion_id
  WHERE r.usuario_id = ?
  ORDER BY r.created_at DESC
");
$st->execute([$_SESSION['usuario_id']]);
$reservas = $st->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mis Reservas — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/navbar.php'; ?>

<div style="background:linear-gradient(135deg,#1a6b4a,#2e9e6f);" class="py-4 text-white text-center">
  <h2 class="fw-bold mb-0"><i class="bi bi-journal-check"></i> Mis Reservas</h2>
</div>

<div class="container py-4">
  <?php include __DIR__ . '/../partials/flash.php'; ?>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Historial de Reservas</h5>
    <a href="nueva_reserva.php" class="btn btn-primary-urmiri btn-sm">
      <i class="bi bi-plus-circle"></i> Nueva Reserva
    </a>
  </div>

  <?php if (empty($reservas)): ?>
    <div class="text-center py-5 text-muted">
      <i class="bi bi-calendar-x display-3"></i>
      <p class="mt-3">No tienes reservas aún.</p>
      <a href="nueva_reserva.php" class="btn btn-primary-urmiri">Hacer mi primera reserva</a>
    </div>
  <?php else: ?>
    <div class="row g-4">
      <?php foreach ($reservas as $r): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
          <img src="<?= UPLOADS_URL . htmlspecialchars($r['imagen']) ?>"
               class="card-img-top rounded-top-4" style="height:150px;object-fit:cover"
               onerror="this.src='https://placehold.co/400x150/1a6b4a/fff?text=Urmiri'">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h6 class="fw-bold mb-0"><?= sanitize($r['habitacion']) ?></h6>
              <?php
                $badgeClass = match($r['estado']) {
                  'confirmada' => 'bg-success',
                  'cancelada'  => 'bg-danger',
                  default      => 'bg-warning text-dark'
                };
              ?>
              <span class="badge <?= $badgeClass ?>"><?= strtoupper($r['estado']) ?></span>
            </div>
            <p class="small text-muted mb-1">
              <i class="bi bi-hash"></i> <?= sanitize($r['codigo']) ?>
            </p>
            <p class="small text-muted mb-1">
              <i class="bi bi-calendar"></i>
              <?= date('d/m/Y', strtotime($r['fecha_ingreso'])) ?> →
              <?= date('d/m/Y', strtotime($r['fecha_salida'])) ?>
            </p>
            <p class="small text-muted mb-2">
              <i class="bi bi-moon"></i> <?= $r['num_noches'] ?> noches |
              <i class="bi bi-people"></i> <?= $r['cantidad_personas'] ?> personas
            </p>
            <div class="fw-bold text-success fs-5 mb-2"><?= bs((float)$r['total']) ?></div>

            <?php if ($r['estado'] === 'pendiente'): ?>
            <a href="<?= BASE_URL ?>controllers/reserva_controller.php?action=cancelar&id=<?= $r['id'] ?>"
               class="btn btn-outline-danger btn-sm w-100"
               onclick="return confirm('¿Cancelar esta reserva?')">
              <i class="bi bi-x-circle"></i> Cancelar
            </a>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
