<?php
// views/partials/navbar.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg,#1a6b4a,#2e9e6f);">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>index.php">
      <img src="<?= BASE_URL ?>assets/images/logo.png" alt="Urmiri" height="38"
           onerror="this.style.display='none'">
      <span class="fw-bold fs-5">🌿 SGAT-Urmiri</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='index.php'?'active':'' ?>"
             href="<?= BASE_URL ?>index.php">Inicio</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $currentPage==='habitaciones.php'?'active':'' ?>"
             href="<?= BASE_URL ?>views/habitaciones/habitaciones.php">Habitaciones</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>index.php#servicios">Servicios</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>index.php#contacto">Contacto</a>
        </li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if (isLoggedIn()): ?>
          <?php if (in_array($_SESSION['rol'],['administrador','operador'])): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>views/admin/dashboard.php">
              <i class="bi bi-speedometer2"></i> Dashboard
            </a>
          </li>
          <?php endif; ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-person-circle"></i>
              <?= sanitize($_SESSION['nombre']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="<?= BASE_URL ?>views/reservas/mis_reservas.php">
                <i class="bi bi-journal-check"></i> Mis Reservas</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>controllers/auth_controller.php?action=logout">
                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= BASE_URL ?>views/auth/login.php">
              <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
            </a>
          </li>
          <li class="nav-item">
            <a class="btn btn-warning btn-sm ms-2 my-auto"
               href="<?= BASE_URL ?>views/auth/registro.php">Regístrate</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
