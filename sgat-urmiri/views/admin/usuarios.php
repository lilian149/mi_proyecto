<?php
// views/admin/usuarios.php
require_once __DIR__ . '/../../config/helpers.php';
requireAdmin(); // Solo administrador
$db = getDB();

// ── Eliminar usuario ──────────────────────────────────────────
if (isset($_GET['del'])) {
  $id = (int)$_GET['del'];
  if ($id != $_SESSION['usuario_id']) {
    $db->prepare("DELETE FROM usuarios WHERE id=?")->execute([$id]);
    redirect(BASE_URL . 'views/admin/usuarios.php', 'success', 'Usuario eliminado correctamente.');
  } else {
    redirect(BASE_URL . 'views/admin/usuarios.php', 'error', 'No puedes eliminarte a ti mismo.');
  }
}

// ── Activar / Desactivar ──────────────────────────────────────
if (isset($_GET['toggle'])) {
  $id  = (int)$_GET['toggle'];
  $val = (int)$_GET['val'];
  $db->prepare("UPDATE usuarios SET activo=? WHERE id=?")->execute([$val, $id]);
  redirect(BASE_URL . 'views/admin/usuarios.php', 'success', 'Estado actualizado.');
}

// ── Guardar usuario (crear / editar) ──────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id       = (int)($_POST['id'] ?? 0);
  $nombre   = trim($_POST['nombre_completo'] ?? '');
  $ci       = trim($_POST['ci'] ?? '');
  $correo   = trim($_POST['correo'] ?? '');
  $telefono = trim($_POST['telefono'] ?? '');
  $rol      = $_POST['rol'] ?? 'turista';
  $activo   = isset($_POST['activo']) ? 1 : 0;
  $password = trim($_POST['password'] ?? '');

  if (!$nombre || !$ci || !$correo) {
    redirect(BASE_URL . 'views/admin/usuarios.php', 'error', 'Nombre, CI y correo son obligatorios.');
  }

  if ($id) {
    // Editar
    if ($password !== '') {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $db->prepare("UPDATE usuarios SET nombre_completo=?,ci=?,correo=?,telefono=?,rol=?,activo=?,password=? WHERE id=?")
         ->execute([$nombre, $ci, $correo, $telefono, $rol, $activo, $hash, $id]);
    } else {
      $db->prepare("UPDATE usuarios SET nombre_completo=?,ci=?,correo=?,telefono=?,rol=?,activo=? WHERE id=?")
         ->execute([$nombre, $ci, $correo, $telefono, $rol, $activo, $id]);
    }
    redirect(BASE_URL . 'views/admin/usuarios.php', 'success', 'Usuario actualizado correctamente.');
  } else {
    // Crear
    if (!$password) {
      redirect(BASE_URL . 'views/admin/usuarios.php', 'error', 'La contraseña es obligatoria al crear un usuario.');
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $db->prepare("INSERT INTO usuarios (nombre_completo,ci,correo,telefono,rol,activo,password) VALUES (?,?,?,?,?,?,?)")
       ->execute([$nombre, $ci, $correo, $telefono, $rol, $activo, $hash]);
    redirect(BASE_URL . 'views/admin/usuarios.php', 'success', 'Usuario creado correctamente.');
  }
}

$usuarios = $db->query("SELECT * FROM usuarios ORDER BY created_at DESC")->fetchAll();

// Usuario a editar (si viene ?editar=ID)
$editar = null;
if (isset($_GET['editar'])) {
  $st = $db->prepare("SELECT * FROM usuarios WHERE id=?");
  $st->execute([(int)$_GET['editar']]);
  $editar = $st->fetch();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Usuarios — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
  <style>body{padding-top:0}</style>
</head>
<body>
<div class="d-flex">
  <?php include __DIR__ . '/sidebar.php'; ?>
  <div class="main-content flex-grow-1 bg-light">

    <!-- Cabecera -->
    <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
      <h5 class="mb-0 fw-bold"><i class="bi bi-people"></i> Gestión de Usuarios</h5>
      <button class="btn btn-primary-urmiri btn-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario"
              onclick="resetModal()">
        <i class="bi bi-person-plus"></i> Nuevo Usuario
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
                  <th>#</th><th>Nombre</th><th>CI</th><th>Correo</th>
                  <th>Teléfono</th><th>Rol</th><th>Estado</th><th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                  <td><?= $u['id'] ?></td>
                  <td><strong><?= sanitize($u['nombre_completo']) ?></strong></td>
                  <td><?= sanitize($u['ci']) ?></td>
                  <td><?= sanitize($u['correo']) ?></td>
                  <td><?= sanitize($u['telefono'] ?? '—') ?></td>
                  <td>
                    <?php $cls = match($u['rol']) {
                      'administrador'=>'bg-danger','operador'=>'bg-warning text-dark',
                      default=>'bg-info text-dark'
                    }; ?>
                    <span class="badge <?= $cls ?>"><?= strtoupper($u['rol']) ?></span>
                  </td>
                  <td>
                    <?php if ($u['activo']): ?>
                      <span class="badge bg-success">Activo</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Inactivo</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ($u['id'] != $_SESSION['usuario_id']): ?>
                      <!-- Editar -->
                      <button class="btn btn-warning btn-sm me-1"
                              onclick="abrirEditar(<?= $u['id'] ?>,'<?= addslashes(sanitize($u['nombre_completo'])) ?>','<?= addslashes(sanitize($u['ci'])) ?>','<?= addslashes(sanitize($u['correo'])) ?>','<?= addslashes(sanitize($u['telefono'] ?? '')) ?>','<?= $u['rol'] ?>',<?= $u['activo'] ?>)"
                              title="Editar usuario">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <!-- Activar / Desactivar -->
                      <?php if ($u['activo']): ?>
                        <a href="?toggle=<?= $u['id'] ?>&val=0"
                           class="btn btn-secondary btn-sm me-1"
                           onclick="return confirm('¿Desactivar este usuario?')"
                           title="Desactivar">
                          <i class="bi bi-pause-circle"></i>
                        </a>
                      <?php else: ?>
                        <a href="?toggle=<?= $u['id'] ?>&val=1"
                           class="btn btn-success btn-sm me-1"
                           title="Activar">
                          <i class="bi bi-play-circle"></i>
                        </a>
                      <?php endif; ?>
                      <!-- Eliminar -->
                      <a href="?del=<?= $u['id'] ?>"
                         class="btn btn-danger btn-sm"
                         onclick="return confirm('⚠️ ¿Eliminar permanentemente a <?= addslashes(sanitize($u['nombre_completo'])) ?>?')"
                         title="Eliminar usuario">
                        <i class="bi bi-trash"></i>
                      </a>
                    <?php else: ?>
                      <small class="text-muted">(tú)</small>
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

<!-- ── Modal Crear / Editar Usuario ── -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content rounded-4">
      <div class="modal-header rounded-top-4" style="background:#1a3d28;">
        <h5 class="modal-title fw-bold text-white" id="modalUsuarioTitulo">
          <i class="bi bi-person-plus me-2"></i>Nuevo Usuario
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" id="formUsuario">
        <input type="hidden" name="id" id="input_id" value="0">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Nombre Completo *</label>
              <input type="text" name="nombre_completo" id="input_nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">CI *</label>
              <input type="text" name="ci" id="input_ci" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Correo Electrónico *</label>
              <input type="email" name="correo" id="input_correo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Teléfono</label>
              <input type="text" name="telefono" id="input_telefono" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold">Rol</label>
              <select name="rol" id="input_rol" class="form-select">
                <option value="turista">Turista</option>
                <option value="operador">Operador</option>
                <option value="administrador">Administrador</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-semibold" id="labelPassword">
                Contraseña * <small class="text-muted fw-normal" id="passHint"></small>
              </label>
              <input type="password" name="password" id="input_password" class="form-control"
                     placeholder="Nueva contraseña">
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="activo" id="input_activo" checked>
                <label class="form-check-label" for="input_activo">Usuario activo</label>
              </div>
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
<script>
function resetModal() {
  document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-person-plus me-2"></i>Nuevo Usuario';
  document.getElementById('input_id').value      = '0';
  document.getElementById('input_nombre').value  = '';
  document.getElementById('input_ci').value      = '';
  document.getElementById('input_correo').value  = '';
  document.getElementById('input_telefono').value= '';
  document.getElementById('input_rol').value     = 'turista';
  document.getElementById('input_password').value= '';
  document.getElementById('input_activo').checked= true;
  document.getElementById('input_password').required = true;
  document.getElementById('passHint').textContent = '';
}

function abrirEditar(id, nombre, ci, correo, telefono, rol, activo) {
  document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-pencil me-2"></i>Editar Usuario';
  document.getElementById('input_id').value       = id;
  document.getElementById('input_nombre').value   = nombre;
  document.getElementById('input_ci').value       = ci;
  document.getElementById('input_correo').value   = correo;
  document.getElementById('input_telefono').value = telefono;
  document.getElementById('input_rol').value      = rol;
  document.getElementById('input_password').value = '';
  document.getElementById('input_password').required = false;
  document.getElementById('passHint').textContent = '(dejar en blanco para no cambiar)';
  document.getElementById('input_activo').checked = activo == 1;
  new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}

<?php if ($editar): ?>
document.addEventListener('DOMContentLoaded', () => {
  abrirEditar(<?= $editar['id'] ?>,'<?= addslashes(sanitize($editar['nombre_completo'])) ?>',
    '<?= addslashes(sanitize($editar['ci'])) ?>','<?= addslashes(sanitize($editar['correo'])) ?>',
    '<?= addslashes(sanitize($editar['telefono'] ?? '')) ?>','<?= $editar['rol'] ?>',<?= $editar['activo'] ?>);
});
<?php endif; ?>
</script>
</body>
</html>
