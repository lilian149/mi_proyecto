<?php
// views/auth/registro.php
require_once __DIR__ . '/../../config/helpers.php';
if (isLoggedIn()) redirect(BASE_URL . 'index.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registro — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light" style="padding-top:0">

<div class="min-vh-100 d-flex align-items-center"
     style="background:linear-gradient(135deg,#0d3b27,#1a6b4a);">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="text-center mb-4">
          <a href="<?= BASE_URL ?>index.php" class="text-white text-decoration-none">
            <h3 class="fw-bold">🌿 SGAT-Urmiri</h3>
          </a>
        </div>
        <div class="card border-0 shadow-lg rounded-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 text-center">Crear Cuenta</h5>
            <?php include __DIR__ . '/../partials/flash.php'; ?>
            <form action="<?= BASE_URL ?>controllers/auth_controller.php?action=registro" method="POST"
                  novalidate id="formReg">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-semibold">Nombre Completo *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="nombre_completo" class="form-control"
                           placeholder="Juan Pérez Mamani" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Carnet de Identidad *</label>
                  <input type="text" name="ci" class="form-control"
                         placeholder="1234567" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Teléfono</label>
                  <input type="tel" name="telefono" class="form-control" placeholder="71234567">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Correo electrónico *</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="correo" class="form-control"
                           placeholder="correo@ejemplo.com" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Contraseña *</label>
                  <input type="password" name="password" id="pass1" class="form-control"
                         placeholder="Mínimo 6 caracteres" required minlength="6">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-semibold">Repetir Contraseña *</label>
                  <input type="password" name="password2" id="pass2" class="form-control"
                         placeholder="Repite la contraseña" required>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary-urmiri w-100 py-2 fw-bold">
                    <i class="bi bi-person-plus"></i> Crear Cuenta
                  </button>
                </div>
              </div>
            </form>
            <hr>
            <p class="text-center mb-0 small">
              ¿Ya tienes cuenta?
              <a href="<?= BASE_URL ?>views/auth/login.php" class="fw-bold">Iniciar sesión</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('formReg').addEventListener('submit', e => {
  const p1 = document.getElementById('pass1').value;
  const p2 = document.getElementById('pass2').value;
  if (p1 !== p2) {
    e.preventDefault();
    alert('Las contraseñas no coinciden.');
  }
});
</script>
</body>
</html>
