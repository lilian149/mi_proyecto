<?php
// views/auth/login.php
require_once __DIR__ . '/../../config/helpers.php';
if (isLoggedIn()) redirect(BASE_URL . 'index.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Iniciar Sesión — SGAT Urmiri</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body class="bg-light" style="padding-top:0">

<div class="min-vh-100 d-flex align-items-center"
     style="background:linear-gradient(135deg,#0d3b27,#1a6b4a);">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-md-5">
        <div class="text-center mb-4">
          <a href="<?= BASE_URL ?>index.php" class="text-white text-decoration-none">
            <h3 class="fw-bold">🌿 SGAT-Urmiri</h3>
          </a>
          <p class="text-white-50 small">Complejo Turístico de Aguas Termales</p>
        </div>
        <div class="card border-0 shadow-lg rounded-4">
          <div class="card-body p-4">
            <h5 class="fw-bold mb-4 text-center">Iniciar Sesión</h5>
            <?php include __DIR__ . '/../partials/flash.php'; ?>
            <form action="<?= BASE_URL ?>controllers/auth_controller.php?action=login" method="POST">
              <div class="mb-3">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                  <input type="email" name="correo" class="form-control"
                         placeholder="correo@ejemplo.com" required autofocus>
                </div>
              </div>
              <div class="mb-4">
                <label class="form-label fw-semibold">Contraseña</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="bi bi-lock"></i></span>
                  <input type="password" name="password" id="pass" class="form-control"
                         placeholder="••••••••" required>
                  <button class="btn btn-outline-secondary" type="button"
                          onclick="togglePass()">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                  </button>
                </div>
              </div>
              <button type="submit" class="btn btn-primary-urmiri w-100 py-2 fw-bold">
                <i class="bi bi-box-arrow-in-right"></i> Ingresar
              </button>
            </form>
            <hr>
            <p class="text-center mb-0 small">
              ¿No tienes cuenta?
              <a href="<?= BASE_URL ?>views/auth/registro.php" class="fw-bold">Regístrate aquí</a>
            </p>
            <p class="text-center mt-2 small text-muted">
              Admin demo: <code>admin@urmiri.bo</code> / <code>password</code>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePass() {
  const p = document.getElementById('pass');
  const i = document.getElementById('eyeIcon');
  p.type = p.type === 'password' ? 'text' : 'password';
  i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
