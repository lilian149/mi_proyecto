<?php
// views/admin/habitaciones.php
require_once __DIR__ . '/../../config/helpers.php';
requireOperadorOrAdmin();
$db = getDB();
$habitaciones = $db->query("SELECT * FROM habitaciones ORDER BY nombre")->fetchAll();

// Para edición
$editar = null;
if (isset($_GET['editar'])) {
  $st = $db->prepare("SELECT * FROM habitaciones WHERE id=?");
  $st->execute([(int)$_GET['editar']]);
  $editar = $st->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Habitaciones — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>body{padding-top:0}</style>
</head>
<body>
<div class="d-flex">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="main-content flex-grow-1 bg-light">
    <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="bi bi-house"></i> Gestión de Habitaciones</h5>
      <button class="btn btn-primary-urmiri btn-sm" data-bs-toggle="modal" data-bs-target="#modalHab">
        <i class="bi bi-plus-circle"></i> Nueva Habitación
      </button>
    </div>
    <div class="p-4">
      <?php include __DIR__ . '/../partials/flash.php'; ?>

      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern table-hover align-middle mb-0">
              <thead>
                <tr>
                  <th>Imagen</th><th>Nombre</th><th>Capacidad</th>
                  <th>Precio/Noche</th><th>Estado</th><th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($habitaciones as $h): ?>
                <tr>
                  <td>
                    <img src="<?= UPLOADS_URL . htmlspecialchars($h['imagen']) ?>"
                         width="70" height="50" style="object-fit:cover;border-radius:8px"
                         onerror="this.src='https://placehold.co/70x50/1a6b4a/fff?text=+'"
                         alt="">
                  </td>
                  <td><strong><?= sanitize($h['nombre']) ?></strong><br>
                    <small class="text-muted"><?= sanitize(substr($h['descripcion'],0,50)) ?>...</small>
                  </td>
                  <td><?= $h['capacidad'] ?> <i class="bi bi-people text-muted"></i></td>
                  <td class="fw-bold text-success"><?= bs((float)$h['precio_noche']) ?></td>
                  <td>
                    <span class="badge badge-<?= $h['estado'] ?>"><?= strtoupper($h['estado']) ?></span>
                  </td>
                  <td>
                    <a href="?editar=<?= $h['id'] ?>" class="btn btn-warning btn-sm me-1">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <?php if ($_SESSION['rol'] === 'administrador'): ?>
                    <button class="btn btn-danger btn-sm"
                      onclick="confirmarEliminar('<?= BASE_URL ?>controllers/habitacion_controller.php?action=eliminar&id=<?= $h['id'] ?>','<?= sanitize($h['nombre']) ?>')">
                      <i class="bi bi-trash"></i>
                    </button>
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

<!-- Modal: Crear/Editar Habitación -->
<div class="modal fade" id="modalHab" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header bg-primary text-white rounded-top-4">
        <h5 class="modal-title fw-bold">
          <?= $editar ? '✏️ Editar Habitación' : '➕ Nueva Habitación' ?>
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="<?= BASE_URL ?>controllers/habitacion_controller.php?action=<?= $editar?'editar':'crear' ?>"
            method="POST" enctype="multipart/form-data">
        <?php if ($editar): ?>
          <input type="hidden" name="id" value="<?= $editar['id'] ?>">
        <?php endif; ?>
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label fw-semibold">Nombre *</label>
              <input type="text" name="nombre" class="form-control"
                     value="<?= $editar ? sanitize($editar['nombre']) : '' ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-semibold">Capacidad *</label>
              <input type="number" name="capacidad" class="form-control" min="1" max="20"
                     value="<?= $editar ? $editar['capacidad'] : 2 ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Precio por Noche (Bs.) *</label>
              <div class="input-group">
                <span class="input-group-text">Bs.</span>
                <input type="number" name="precio_noche" class="form-control"
                       min="0" step="0.01"
                       value="<?= $editar ? $editar['precio_noche'] : '' ?>" required>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Estado</label>
              <select name="estado" class="form-select">
                <?php foreach (['disponible','ocupada','mantenimiento'] as $est): ?>
                <option value="<?= $est ?>"
                  <?= ($editar && $editar['estado']===$est)?'selected':'' ?>>
                  <?= ucfirst($est) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Descripción</label>
              <textarea name="descripcion" class="form-control" rows="3"><?= $editar ? sanitize($editar['descripcion']) : '' ?></textarea>
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Imagen de la Habitación</label>
              <input type="file" name="imagen" id="imagen" class="form-control" accept=".jpg,.jpeg,.png,.webp">
              <?php if ($editar && $editar['imagen']): ?>
                <div class="mt-2">
                  <img src="<?= UPLOADS_URL . htmlspecialchars($editar['imagen']) ?>"
                       id="imgPreview" height="80" style="border-radius:8px;object-fit:cover"
                       onerror="this.style.display='none'">
                </div>
              <?php else: ?>
                <img id="imgPreview" style="display:none;height:80px;border-radius:8px;object-fit:cover;margin-top:8px">
              <?php endif; ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-urmiri fw-bold">
            <i class="bi bi-save"></i> Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
<script>
  function confirmarEliminar(url, nombre) {
    if (confirm(`¿Eliminar habitación "${nombre}"? Esta acción no se puede deshacer.`)) {
      window.location.href = url;
    }
  }
  <?php if ($editar): ?>
  // Auto-abrir modal si hay habitación para editar
  document.addEventListener('DOMContentLoaded', () => {
    new bootstrap.Modal(document.getElementById('modalHab')).show();
  });
  <?php endif; ?>
</script>
</body>
</html>
