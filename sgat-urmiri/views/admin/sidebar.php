<?php
// views/admin/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav class="sidebar d-flex flex-column">
  <!-- Brand -->
  <div class="sidebar-brand text-white">
    <a href="<?= BASE_URL ?>index.php" class="text-decoration-none text-white">
      <div class="fw-bold fs-5">🌿 SGAT-Urmiri</div>
      <small class="opacity-50">Panel Administrativo</small>
    </a>
  </div>

  <!-- Rol badge -->
  <div class="px-3 py-2">
    <span class="badge bg-warning text-dark w-100">
      <?= strtoupper($_SESSION['rol'] ?? 'operador') ?>
    </span>
  </div>

  <!-- Navegación -->
  <ul class="nav flex-column px-2 mt-1 flex-grow-1">
    <li class="nav-item">
      <a class="nav-link <?= $currentPage==='dashboard.php'?'active':'' ?>"
         href="dashboard.php">
        <i class="bi bi-speedometer2"></i> Dashboard
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $currentPage==='reservas.php'?'active':'' ?>"
         href="reservas.php">
        <i class="bi bi-calendar-check"></i> Reservas
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $currentPage==='habitaciones.php'?'active':'' ?>"
         href="habitaciones.php">
        <i class="bi bi-house"></i> Habitaciones
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $currentPage==='usuarios.php'?'active':'' ?>"
         href="usuarios.php">
        <i class="bi bi-people"></i> Usuarios
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $currentPage==='servicios.php'?'active':'' ?>"
         href="servicios.php">
        <i class="bi bi-stars"></i> Servicios
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link <?= $currentPage==='reportes.php'?'active':'' ?>"
         href="reportes.php">
        <i class="bi bi-bar-chart-line"></i> Reportes
      </a>
    </li>
    <hr class="border-secondary">
    <li class="nav-item">
      <a class="nav-link" href="<?= BASE_URL ?>index.php">
        <i class="bi bi-house-door"></i> Ver Sitio
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link text-danger" href="<?= BASE_URL ?>controllers/auth_controller.php?action=logout">
        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
      </a>
    </li>
  </ul>

  <!-- Pie del sidebar -->
  <div class="px-3 pb-3 mt-auto">
    <small class="text-white-50">© <?= date('Y') ?> SGAT-Urmiri</small>
  </div>
</nav>
