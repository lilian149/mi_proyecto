<?php
// views/admin/servicios.php
require_once __DIR__ . '/../../config/helpers.php';
requireOperadorOrAdmin();
$db = getDB();

// ── Guardar servicio (crear / editar) ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id    = (int)($_POST['id'] ?? 0);
  $nom   = trim($_POST['nombre'] ?? '');
  $desc  = trim($_POST['descripcion'] ?? '');
  $prec  = (float)($_POST['precio'] ?? 0);
  $ico   = trim($_POST['icono'] ?? 'bi-star');
  $act   = isset($_POST['activo']) ? 1 : 0;
  if (!$nom) { redirect(BASE_URL . 'views/admin/servicios.php', 'error', 'El nombre es requerido.'); }
  if ($id) {
    $db->prepare("UPDATE servicios SET nombre=?,descripcion=?,precio=?,icono=?,activo=? WHERE id=?")
       ->execute([$nom, $desc, $prec, $ico, $act, $id]);
    redirect(BASE_URL . 'views/admin/servicios.php', 'success', 'Servicio actualizado.');
  } else {
    $db->prepare("INSERT INTO servicios (nombre,descripcion,precio,icono,activo) VALUES (?,?,?,?,?)")
       ->execute([$nom, $desc, $prec, $ico, $act]);
    redirect(BASE_URL . 'views/admin/servicios.php', 'success', 'Servicio creado.');
  }
}
if (isset($_GET['del'])) {
  $db->prepare("DELETE FROM servicios WHERE id=?")->execute([(int)$_GET['del']]);
  redirect(BASE_URL . 'views/admin/servicios.php', 'success', 'Servicio eliminado.');
}

$servicios = $db->query("SELECT * FROM servicios ORDER BY nombre")->fetchAll();
$editar = null;
if (isset($_GET['editar'])) {
  $st = $db->prepare("SELECT * FROM servicios WHERE id=?");
  $st->execute([(int)$_GET['editar']]);
  $editar = $st->fetch();
}

// Menú de desayunos y almuerzos (mismo que menu.php)
$desayunos = [
  ['id'=>'d1','nombre'=>'Desayuno Continental',    'precio'=>25,'calorias'=>'~420 kcal','tag'=>'Clásico',   'desc'=>'Pan fresco, mermelada, mantequilla, jugo natural y té o café.'],
  ['id'=>'d2','nombre'=>'Desayuno Buffet Completo','precio'=>35,'calorias'=>'~750 kcal','tag'=>'Popular',   'desc'=>'Variedad de panes, frutas frescas, huevos a elección, embutidos, cereales, jugos y bebidas calientes.'],
  ['id'=>'d3','nombre'=>'Desayuno Boliviano',       'precio'=>30,'calorias'=>'~580 kcal','tag'=>'Tradicional','desc'=>'Api morado con pastel, buñuelos con miel, ch\'uño phuti y marraqueta.'],
  ['id'=>'d4','nombre'=>'Desayuno Saludable',       'precio'=>28,'calorias'=>'~380 kcal','tag'=>'Fit',       'desc'=>'Granola con yogur, ensalada de frutas, tostadas integrales, jugo verde y té de hierbas.'],
];
$almuerzos = [
  ['id'=>'a1','nombre'=>'Trucha a la Plancha',      'precio'=>55,'calorias'=>'~620 kcal','tag'=>'Estrella ★','desc'=>'Trucha fresca del altiplano a la plancha con papa cocida, ensalada verde y arroz.'],
  ['id'=>'a2','nombre'=>'Sopa de Maní',             'precio'=>35,'calorias'=>'~480 kcal','tag'=>'Tradicional','desc'=>'Sopa tradicional paceña con maní tostado, verduras, papa y arroz.'],
  ['id'=>'a3','nombre'=>'Pollo al Horno',           'precio'=>48,'calorias'=>'~700 kcal','tag'=>'Familiar',  'desc'=>'Pollo entero al horno con hierbas aromáticas, papas doradas, ensalada mixta y crema de ají amarillo.'],
  ['id'=>'a4','nombre'=>'Plato Vegetariano',        'precio'=>40,'calorias'=>'~420 kcal','tag'=>'Veggie',    'desc'=>'Quinoa salteada con verduras del altiplano, tofu ahumado, crema de locoto y pan integral.'],
  ['id'=>'a5','nombre'=>'Chicharrón de Cerdo',      'precio'=>52,'calorias'=>'~820 kcal','tag'=>'Clásico',   'desc'=>'Chicharrón crujiente estilo paceño con mote, llajua casera, ensalada de cebolla y maíz.'],
  ['id'=>'a6','nombre'=>'Lomo Saltado Altiplánico', 'precio'=>58,'calorias'=>'~740 kcal','tag'=>'Especial',  'desc'=>'Lomo de res salteado con tomate, cebolla, locoto, papas fritas y arroz.'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Servicios — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>
    body{padding-top:0}
    .menu-img {
      width:60px; height:50px; object-fit:cover; border-radius:6px;
    }
    .menu-img-placeholder {
      width:60px; height:50px; border-radius:6px;
      display:flex; align-items:center; justify-content:center;
      font-size:1.4rem; color:#fff;
    }
    .tag-badge {
      font-size:.7rem; font-weight:600; padding:.2rem .6rem;
      border-radius:100px; white-space:nowrap;
    }
    .section-header {
      background: #1a3d28; color:#fff;
      padding:.6rem 1.2rem; font-weight:600; font-size:.85rem;
      letter-spacing:.06em; text-transform:uppercase;
    }
  </style>
</head>
<body>
<div class="d-flex">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="main-content flex-grow-1 bg-light">

    <!-- Cabecera -->
    <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="bi bi-stars"></i> Gestión de Servicios</h5>
      <button class="btn btn-primary-urmiri btn-sm" data-bs-toggle="modal" data-bs-target="#modalSrv"
              onclick="resetSrvModal()">
        <i class="bi bi-plus-circle"></i> Nuevo Servicio
      </button>
    </div>

    <div class="p-4">
      <?php include __DIR__ . '/../partials/flash.php'; ?>

      <!-- ══ MENÚ: DESAYUNOS Y ALMUERZOS ══════════════════════════ -->
      <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-0">

          <!-- Desayunos -->
          <div class="section-header rounded-top-4">
            <i class="bi bi-cup-hot me-2"></i>Desayunos
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:70px">Foto</th>
                  <th>Nombre</th>
                  <th>Estado</th>
                  <th>Precio</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($desayunos as $d): ?>
                <tr>
                  <td>
                    <img src="<?= BASE_URL ?>uploads/menu/<?= $d['id'] ?>.jpg"
                         class="menu-img"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                         alt="<?= $d['nombre'] ?>">
                    <div class="menu-img-placeholder" style="background:#c9862b;display:none">
                      <i class="bi bi-cup-hot"></i>
                    </div>
                  </td>
                  <td><strong><?= sanitize($d['nombre']) ?></strong></td>
                  <td><span class="badge bg-success">Activo</span></td>
                  <td><span class="fw-bold text-success">Bs. <?= number_format($d['precio'],2) ?></span></td>
                  <td>
                    <button class="btn btn-warning btn-sm me-1" disabled title="Editar"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-danger btn-sm" disabled title="Eliminar"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <!-- Almuerzos -->
          <div class="section-header mt-0">
            <i class="bi bi-bowl-hot me-2"></i>Almuerzos
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:70px">Foto</th>
                  <th>Nombre</th>
                  <th>Estado</th>
                  <th>Precio</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($almuerzos as $a): ?>
                <tr>
                  <td>
                    <img src="<?= BASE_URL ?>uploads/menu/<?= $a['id'] ?>.jpg"
                         class="menu-img"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                         alt="<?= $a['nombre'] ?>">
                    <div class="menu-img-placeholder" style="background:#1a6b4a;display:none">
                      <i class="bi bi-bowl-hot"></i>
                    </div>
                  </td>
                  <td><strong><?= sanitize($a['nombre']) ?></strong></td>
                  <td><span class="badge bg-success">Activo</span></td>
                  <td><span class="fw-bold text-success">Bs. <?= number_format($a['precio'],2) ?></span></td>
                  <td>
                    <button class="btn btn-warning btn-sm me-1" disabled title="Editar"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-danger btn-sm" disabled title="Eliminar"><i class="bi bi-trash"></i></button>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>

      <!-- ══ SERVICIOS ADICIONALES ════════════════════════════════ -->
      <div class="card border-0 shadow-sm rounded-4">
        <div class="section-header rounded-top-4">
          <i class="bi bi-stars me-2"></i>Servicios Adicionales del Complejo
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-modern table-hover align-middle mb-0">
              <thead class="table-light">
                <tr><th>Icono</th><th>Nombre</th><th>Descripción</th><th>Precio</th><th>Estado</th><th>Acciones</th></tr>
              </thead>
              <tbody>
                <?php foreach ($servicios as $s): ?>
                <tr>
                  <td class="text-center fs-4"><i class="bi <?= sanitize($s['icono']) ?> text-success"></i></td>
                  <td><strong><?= sanitize($s['nombre']) ?></strong></td>
                  <td class="small text-muted"><?= sanitize(substr($s['descripcion'],0,60)) ?>...</td>
                  <td><?= $s['precio'] > 0 ? '<span class="fw-bold text-success">Bs. ' . number_format((float)$s['precio'],2) . '</span>' : '<span class="badge bg-secondary">Incluido</span>' ?></td>
                  <td><?= $s['activo'] ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>' ?></td>
                  <td>
                    <a href="?editar=<?= $s['id'] ?>" class="btn btn-warning btn-sm me-1"><i class="bi bi-pencil"></i></a>
                    <a href="?del=<?= $s['id'] ?>" class="btn btn-danger btn-sm"
                       onclick="return confirm('¿Eliminar este servicio?')"><i class="bi bi-trash"></i></a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div><!-- /p-4 -->
  </div>
</div>

<!-- Modal Servicio Adicional -->
<div class="modal fade" id="modalSrv" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content rounded-4">
      <div class="modal-header rounded-top-4" style="background:#1a3d28;">
        <h5 class="modal-title fw-bold text-white" id="modalSrvTitulo">
          <i class="bi bi-plus-circle me-2"></i>Nuevo Servicio
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST">
        <?php if ($editar): ?><input type="hidden" name="id" value="<?= $editar['id'] ?>"><?php endif; ?>
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label fw-semibold">Nombre *</label>
            <input type="text" name="nombre" class="form-control"
                   value="<?= $editar ? sanitize($editar['nombre']) : '' ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="2"><?= $editar ? sanitize($editar['descripcion']) : '' ?></textarea>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Precio (Bs.)</label>
              <input type="number" name="precio" class="form-control" min="0" step="0.01"
                     value="<?= $editar ? $editar['precio'] : '0' ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Icono Bootstrap</label>
              <input type="text" name="icono" class="form-control" placeholder="bi-star"
                     value="<?= $editar ? sanitize($editar['icono']) : 'bi-star' ?>">
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="activo" id="chkActivo"
                       <?= (!$editar || $editar['activo']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="chkActivo">Activo en el sitio</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary-urmiri fw-bold"><i class="bi bi-save"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function resetSrvModal() {
  document.getElementById('modalSrvTitulo').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Nuevo Servicio';
}
<?php if ($editar): ?>
document.addEventListener('DOMContentLoaded',()=>new bootstrap.Modal(document.getElementById('modalSrv')).show());
<?php endif; ?>
</script>
</body>
</html>
